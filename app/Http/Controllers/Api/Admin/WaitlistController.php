<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaitlistSubmission;
use App\Mail\WaitlistAutoResponder;
use App\Mail\WaitlistAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use App\Models\SubscriptionPlansReference;

class WaitlistController extends Controller
{
    /**
     * Store a new waitlist submission
     */
    public function store(Request $request)
    {
        $adminEmail = config('mail.admin_email', config('mail.from.address'));
        // Rate limiting to prevent spam
        $key = 'waitlist:' . $request->ip();
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Check if email already exists in waitlist
        $existingSubmission = WaitlistSubmission::where('email', $request->email)->first();
        if ($existingSubmission) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already on the waitlist.',
            ], 409);
        }

        // Create submission
        $data = [
            'name' => $request->name,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        if ($request->has('plan_id')) {
            $data['plan_id'] = $request->plan_id;
        }
        $submission = WaitlistSubmission::create($data);
        
        // Increment rate limiter
        RateLimiter::hit($key, 3600); // 1 hour expiry

        // Send auto-responder email to user
        try {
            // print_r($submission);die;
            Mail::to($submission->email)->send(new WaitlistAutoResponder($submission));
        } catch (\Exception $e) {
            \Log::error('Failed to send waitlist auto-responder: ' . $e->getMessage());
        }

        // Send notification email to admin
        try {
            // $adminEmail = config('mail.admin_email', config('mail.from.address'));
            $adminEmail = env('MAIL_ADMIN_EMAIL');
            Mail::to($adminEmail)->send(new WaitlistAdminNotification($submission));
        } catch (\Exception $e) {
            \Log::error('Failed to send waitlist admin notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for joining our waitlist! We\'ll be in touch soon.',
            'data' => [
                'id' => $submission->id,
                'name' => $submission->name,
                'email' => $submission->email,
            ],
        ], 201);
    }

    /**
     * Get all waitlist submissions (Admin only)
     */
    public function index(Request $request)
    {
        $query = WaitlistSubmission::query();

        // Filter by status
        if ($request->has('status')) {
            $query->status($request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $submissions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $submissions,
        ]);
    }

    /**
     * Get a single waitlist submission (Admin only)
     */
    public function show($id)
    {
        $submission = WaitlistSubmission::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $submission,
        ]);
    }

    /**
     * Update waitlist submission status (Admin only)
     */
    public function update(Request $request, $id)
    {
        $submission = WaitlistSubmission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,contacted,converted,declined',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = [
            'status' => $request->status,
        ];

        if ($request->has('notes')) {
            $updateData['notes'] = $request->notes;
        }

        if ($request->status === 'contacted' && $submission->contacted_at === null) {
            $updateData['contacted_at'] = now();
        }

        $submission->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Waitlist submission updated successfully',
            'data' => $submission->fresh(),
        ]);
    }

    /**
     * Delete a waitlist submission (Admin only)
     */
    public function destroy($id)
    {
        $submission = WaitlistSubmission::findOrFail($id);
        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Waitlist submission deleted successfully',
        ]);
    }

    /**
     * Get waitlist statistics (Admin only)
     */
    public function statistics()
    {
        $stats = [
            'total' => WaitlistSubmission::count(),
            'pending' => WaitlistSubmission::status('pending')->count(),
            'contacted' => WaitlistSubmission::status('contacted')->count(),
            'converted' => WaitlistSubmission::status('converted')->count(),
            'declined' => WaitlistSubmission::status('declined')->count(),
            'recent_30_days' => WaitlistSubmission::recent(30)->count(),
            'recent_7_days' => WaitlistSubmission::recent(7)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
