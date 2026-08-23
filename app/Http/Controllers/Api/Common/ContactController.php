<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Mail\ContactAutoResponder;
use App\Mail\ContactAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Store a new contact submission
     */
    public function store(Request $request)
    {
        // Rate limiting to prevent spam
        $key = 'contact:' . $request->ip();
        // if (RateLimiter::tooManyAttempts($key, 5)) {
        //     $seconds = RateLimiter::availableIn($key);
        //     return response()->json([
        //         'success' => false,
        //         'message' => "Too many requests. Please try again in {$seconds} seconds.",
        //     ], 429);
        // }

        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create submission
        $submission = ContactSubmission::create([
            'name' => $request->name,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Increment rate limiter
        RateLimiter::hit($key, 3600); // 1 hour expiry

        // Send auto-responder email to user
        try {
            Mail::to($submission->email)->send(new ContactAutoResponder($submission));
        } catch (\Exception $e) {
            \Log::error('Failed to send contact auto-responder: ' . $e->getMessage());
        }

        // Send notification email to admin
        try {
            //$adminEmail = config('mail.admin_email', config('mail.from.address'));
            $adminEmail = env('MAIL_ADMIN_EMAIL');
            Mail::to($adminEmail)->send(new ContactAdminNotification($submission));
        } catch (\Exception $e) {
            \Log::error('Failed to send contact admin notification: ' . $e->getMessage());
        }

        
        //To user
        /*$toEmail = $request->email;
        $subject = "User Reservation";

        $data1 = ['name' => $request->name,'company_name' => $request->company_name,'messageText' => $request->message];

        Mail::send('emails.contact-auto-responder', $data1, function ($message) use ($toEmail) {
            $message->to($toEmail)
                    ->subject("User Reservation");
        });

        //To admin
        // $toEmail = 'adminbeesolver@yopmail.com';
        $toEmail = env('MAIL_ADMIN_EMAIL');
        $subject = "New User Reservation";

        $data = [
            'submission' => $submission
        ];

        Mail::send('emails.contact-admin-notification', $data, function ($message) use ($toEmail) {
            $message->to($toEmail)
                    ->subject("User Reservation");
        });*/



        return response()->json([
            'success' => true,
            'message' => 'Thank you for contacting us! We\'ll get back to you shortly.',
            'data' => [
                'id' => $submission->id,
                'name' => $submission->name,
                'email' => $submission->email,
            ],
        ], 201);
    }

    /**
     * Get all contact submissions (Admin only)
     */
    public function index(Request $request)
    {
        $query = ContactSubmission::with('assignedUser');

        // Filter by status
        if ($request->has('status')) {
            $query->status($request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->priority($request->priority);
        }

        // Filter by assigned user
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
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
     * Get a single contact submission (Admin only)
     */
    public function show($id)
    {
        $submission = ContactSubmission::with('assignedUser')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $submission,
        ]);
    }

    /**
     * Update contact submission (Admin only)
     */
    public function update(Request $request, $id)
    {
        $submission = ContactSubmission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:new,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'response' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = $request->only(['status', 'priority', 'assigned_to', 'response']);

        if ($request->has('status') && $request->status === 'resolved' && $submission->responded_at === null) {
            $updateData['responded_at'] = now();
        }

        $submission->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Contact submission updated successfully',
            'data' => $submission->fresh(['assignedUser']),
        ]);
    }

    /**
     * Delete a contact submission (Admin only)
     */
    public function destroy($id)
    {
        $submission = ContactSubmission::findOrFail($id);
        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact submission deleted successfully',
        ]);
    }

    /**
     * Get contact statistics (Admin only)
     */
    public function statistics()
    {
        $stats = [
            'total' => ContactSubmission::count(),
            'new' => ContactSubmission::status('new')->count(),
            'in_progress' => ContactSubmission::status('in_progress')->count(),
            'resolved' => ContactSubmission::status('resolved')->count(),
            'closed' => ContactSubmission::status('closed')->count(),
            'unresolved' => ContactSubmission::unresolved()->count(),
            'by_priority' => [
                'low' => ContactSubmission::priority('low')->count(),
                'medium' => ContactSubmission::priority('medium')->count(),
                'high' => ContactSubmission::priority('high')->count(),
                'urgent' => ContactSubmission::priority('urgent')->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Assign contact to a user (Admin only)
     */
    public function assign(Request $request, $id)
    {
        $submission = ContactSubmission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $submission->assignTo($request->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Contact assigned successfully',
            'data' => $submission->fresh(['assignedUser']),
        ]);
    }
}
