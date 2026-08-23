<?php
//create a function to get all waitlist submissions and return them to the dashboard view
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaitlistSubmission;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Mail;
use App\Mail\CredentialsMail;
use App\Models\DriverProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.dashboard');
    }
    //create a function to return submissions to the dashboard view
    public function enquiries()
    {
        $submissions = WaitlistSubmission::latest()->paginate(10);
        return view('admin.enquiries', compact('submissions'));
    }

    public function enquiriesDetails($id)
    {
        $submission = WaitlistSubmission::findOrFail($id);
        return view('admin.enquiries-details', compact('submission'));
    }

    public function generateCredentials($id)
    {
        $submission = WaitlistSubmission::findOrFail($id);
        //check if the submission email already exists in the users table
        if (User::where('email', $submission->email)->exists()) {
            return redirect()->route('dashboard.enquiries')->with('error', 'User with this email already exists.');
        }
        // Here you would implement the logic to create a new user based on the submission details
        // For example:
        $password = Str::random(12); // Generate a random password
        
        //Create Tenant for the user
        $tenant = Tenant::create([
            'name' => $submission->company_name,
            'subdomain' => '',
        ]);

        $user = User::create([
            'name' => $submission->name,
            'email' => $submission->email,
            'phone' => $submission->phone,
            'tenant_id' => $tenant->id,
            'password' => bcrypt($password), // Use the generated password
            'role_id' => 3, // Assuming 3 is the role ID for admin
        ]);

        // After creating the user, you might want to updates the submission status or add notes
        $submission->status = 'contacted';
        $submission->notes = 'Verification requested for user ID: ' . $user->id;
        $submission->save();
        
        //send an email to the user with their credentials (this is just a placeholder, you would need to implement the actual email sending logic)
        \Mail::to($user->email)->send(new \App\Mail\CredentialsMail($submission, $password));
        return redirect()->route('dashboard.enquiries')->with('success', 'Verification email sent to user.');
    }

    public function rejectEnquiry($id)
    {
        $submission = WaitlistSubmission::findOrFail($id);
        $submission->status = 'declined';
        $submission->save();
        return redirect()->back()->with('success', 'Enquiry declined successfully.');
    }

    public function tenants()
    {
        $tenants = Tenant::latest()->paginate(10);
        return view('admin.tenants', compact('tenants'));
    }

    public function tenantsDetails($id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('admin.tenant-details', compact('tenant'));
    }

    public function updateTenant($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:trial,active,suspended,cancelled',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }   
        $tenant = Tenant::findOrFail($id);
        // Here you would implement the logic to update the tenant details based on the request data
        // For example:
        $tenant->name = $request->input('name');
        $tenant->status = $request->input('status');
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos/'.$id, 'public');
            $tenant->logo_path = Storage::url($logoPath);
        }
        $tenant->save();

        return redirect()->back()->with('success', 'Tenant updated successfully.');
    }

    public function maps()
    {
        $drivers = \App\Models\DriverProfile::with('user')
            ->where('created_by', auth()->id())
            ->get()
            ->map(function ($driver) {
                return collect([
                    'id' => $driver->user->id,
                    'title' => $driver->user->name,
                    'current_latitude' => $driver->current_latitude,
                    'current_longitude' => $driver->current_longitude,
                    'availability_status' => $driver->availability_status
                ]);
            }); 
        return view('company.maps', compact('drivers'));
    }
}
