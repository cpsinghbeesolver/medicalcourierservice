<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\CompanySignupMail;
use Illuminate\Support\Facades\Mail;

class CompanyController extends Controller
{
    /**
     * Display a paginated, searchable list of companies (dispatcher users, role_id = 2).
     *
     * IMPORTANT: `name` and `phone` on User are encrypted at rest (EncryptsPhiData).
     * Laravel's Crypt::encryptString() is non-deterministic (random IV per call), so a
     * SQL LIKE against those columns can never match — even for the exact same plaintext.
     * Search is therefore limited to `email` (unencrypted) and the related tenant's
     * `name` / `subdomain` (also unencrypted). Do not add name/phone to this search
     * without first switching them to deterministic/blind-index encryption.
     */
    public function index(Request $request)
    {
        $query = User::where('role_id', 2)->with('tenant')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%")
                         ->orWhere('subdomain', 'like', "%{$search}%");
                  });
            });
        }

        $companies = $query->paginate(10)->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * Validation rules shared between store() and update().
     * $userId / $tenantId are passed on update so uniqueness checks
     * ignore the current record.
     */
    protected function rules($userId = null, $tenantId = null)
    {
        return [
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_url' => 'required|string|max:255|unique:tenants,subdomain' . ($tenantId ? ",{$tenantId}" : ''),
            'mobile_no' => 'required|numeric',
            'email' => 'required|email|unique:users,email' . ($userId ? ",{$userId}" : ''),
            // Required on create, optional on update (leave blank to keep current password)
            'password' => ($userId ? 'nullable' : 'required') . '|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Store a newly created company (tenant + its primary dispatcher user).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = DB::transaction(function () use ($request) {
                // Create the user first (without tenant_id), then the tenant,
                // then link them — same order as CompanyAuthController::signup().
                $user = User::create([
                    'name' => $request->input('name'),
                    'phone' => $request->input('mobile_no'),
                    'email' => $request->input('email'),
                    'email_verified_at' => now(),
                    'role_id' => 2,
                    'status' => $request->input('status'),
                    'password' => Hash::make($request->input('password')),
                ]);

                $tenant = Tenant::create([
                    'name' => $request->input('company_name'),
                    'subdomain' => strtolower($request->input('company_url')),
                ]);

                $user->update(['tenant_id' => $tenant->id]);

                return $user->refresh();
            });
        } catch (\Exception $e) {
            Log::error('Company creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create company. Please try again.');
        }

        try {
            Mail::to($user->email)->send(new CompanySignupMail($user));
        } catch (\Exception $e) {
            Log::error('Failed to send company signup email: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.companies')->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified company.
     */
    public function show($id)
    {
        $company = User::where('role_id', 2)->with('tenant')->findOrFail($id);
        return view('admin.companies.view', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit($id)
    {
        $company = User::where('role_id', 2)->with('tenant')->findOrFail($id);
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, $id)
    {
        $company = User::where('role_id', 2)->with('tenant')->findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->rules($company->id, $company->tenant->id ?? null)
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $company) {
                $userData = [
                    'name' => $request->input('name'),
                    'phone' => $request->input('mobile_no'),
                    'email' => $request->input('email'),
                    'status' => $request->input('status'),
                ];

                // Only touch the password if a new one was actually submitted
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->input('password'));
                }

                $company->update($userData);

                if ($company->tenant) {
                    $company->tenant->update([
                        'name' => $request->input('company_name'),
                        'subdomain' => strtolower($request->input('company_url')),
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Company update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update company. Please try again.');
        }

        return redirect()->route('dashboard.companies')->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified company (dispatcher user only).
     *
     * NOTE: This intentionally does NOT delete the associated Tenant row.
     * A tenant may have other users, and deliveries/hospitals reference
     * created_by against this user's id — deleting the tenant here could
     * silently orphan or cascade-delete unrelated data. If you need full
     * tenant teardown, handle that explicitly with its own confirmation step.
     */
    public function destroy($id)
    {
        $company = User::where('role_id', 2)->findOrFail($id);
        $company->delete();

        return redirect()->route('dashboard.companies')->with('success', 'Company deleted successfully.');
    }
}
