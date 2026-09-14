<div class="form-section">
    <h3>Company Information</h3>

    <div class="form-row two-cols">
        <div class="form-group">
            <label>Company Name <span class="astrik">*</span></label>
            <input type="text" name="company_name" value="{{ old('company_name', $company->tenant->name ?? '') }}">
            @error('company_name') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Company URL (subdomain) <span class="astrik">*</span></label>
            <input type="text" name="company_url" value="{{ old('company_url', $company->tenant->subdomain ?? '') }}">
            @error('company_url') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="form-row one-col">
        <div class="form-group">
            <label>Status <span class="astrik">*</span></label>
            <select name="status">
                <option value="active" {{ old('status', $company->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $company->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="form-section">
    <h3>Primary Contact</h3>

    <div class="form-row two-cols">
        <div class="form-group">
            <label>Contact Name <span class="astrik">*</span></label>
            <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}">
            @error('name') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Email <span class="astrik">*</span></label>
            <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}">
            @error('email') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="form-row one-col">
        <div class="form-group">
            <label>Phone <span class="astrik">*</span></label>
            <input type="text" name="mobile_no" class="numbers-only" value="{{ old('mobile_no', $company->phone ?? '') }}">
            @error('mobile_no') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="form-section">
    <h3>{{ isset($company) ? 'Change Password' : 'Set Password' }}</h3>

    <div class="form-row two-cols">
        <div class="form-group">
            <label>
                Password
                @if(!isset($company)) <span class="astrik">*</span> @endif
            </label>
            <input type="password" name="password" autocomplete="new-password"
                   placeholder="{{ isset($company) ? 'Leave blank to keep current password' : '' }}">
            @error('password') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" autocomplete="new-password">
        </div>
    </div>
</div>
