@extends('common.layout')

@section('title', 'Edit Driver Profile')
@section('page-title', 'Edit Driver Form')

@section('styles')
<style>
    .form-container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        padding: 30px;
        max-width: auto;
    }

    .form-section {
        margin-bottom: 35px;
    }

    .form-section h3 {
        font-size: 17px;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section h3::before {
        content: attr(data-number);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background: #2c3e50;
        color: white;
        border-radius: 50%;
        font-size: 13px;
        font-weight: 600;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row.two-cols {
        grid-template-columns: 1fr 1fr;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        color: #2c3e50;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="tel"],
    .form-group input[type="password"],
    .form-group input[type="date"],
    .form-group input[type="file"],
    .form-group select {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #a8b456;
    }

    .checkbox-option {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 15px 0;
    }

    .checkbox-option input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #a8b456;
    }

    .checkbox-option label {
        margin: 0;
        cursor: pointer;
        font-size: 13px;
        color: #2c3e50;
    }

    .subsection {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .subsection h4 {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .file-upload-group {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        margin-bottom: 20px;
        min-width: 0;
    }

    .file-upload-group .form-group {
        flex: 1;
        margin-bottom: 0;
        min-width: 0;
    }

    .file-upload-group input[type="text"] {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        background: #fff;
    }

    .file-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 0;
    }

    .btn-browse {
        padding: 11px 20px;
        background: #2c3e50;
        color: #fff;
        border: 1px solid #2c3e50;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
        transition: background 0.3s, border-color 0.3s;
    }

    .btn-browse:hover {
        background: #1f2d3a;
        border-color: #1f2d3a;
    }

    .existing-file {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid #dce5b0;
        border-radius: 8px;
        color: #68752c;
        background: #f8faed;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.3s, border-color 0.3s;
    }

    .existing-file:hover {
        background: #eef3d7;
        border-color: #a8b456;
    }

    .existing-file i {
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .file-upload-group {
            align-items: stretch;
            flex-wrap: wrap;
        }

        .file-upload-group .form-group {
            flex-basis: 100%;
        }

        .file-actions {
            width: 100%;
            flex-wrap: wrap;
        }
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .btn-submit {
        padding: 12px 30px;
        background: #a8b456;
        color: #000;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: background 0.3s;
    }

    .btn-submit:hover {
        background: #96a048;
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .section-divider {
        height: 1px;
        background: #e9ecef;
        margin: 30px 0;
    }

    .error-message {
        background: #f8d7da;
        color: #842029;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
    }

    .success-message {
        background: #d1e7dd;
        color: #0f5132;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
    }
    #errorMessage, #successMessage {
        display: none;
    }
    .password-wrapper {
            position: relative;
            display: grid;
            align-items: center;
            width: 100%;
        }

        .password-wrapper input {
            flex: 1;
            padding-right: 35px;
        }

        .eye-icon {
            position: absolute;
            right: 7px;
            top: 11px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            color: #7f8c8d;
            transition: color 0.3s ease;
        }

        .eye-icon:hover {
            color: #2c3e50;
        }

</style>


@endsection

@section('content')
<div id="errorMessage" class="error-message"></div>
<div id="successMessage" class="success-message"></div>
<div class="form-container">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endforeach
    @endif
    
    <a href="/company/dashboard/drivers" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Driver Details
</a>
    <form id="createDriverForm" method="POST" enctype="multipart/form-data" action="{{ route('driver.update') }}" autocomplete="off">
        @csrf
        <input type="hidden" name="user_id" value="{{ $profile->id }}">
        <!-- 1. Personal & Contact Info -->
        <div class="form-section">
            <h3 data-number="1">Personal & Contact Info</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name  <span class="astrik">*</span></label>
                    <input type="text" class="letters-only" id="name" name="name" required placeholder="Enter full name" value="{{ old('name', $user->name) }}" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Country Code  <span class="astrik">*</span></label>
                    <select name="country_code" id="country_code">
                        <option value="+1" data-country="US" {{ old('country_code', $profile->country_code) == '+1' ? 'selected' : '' }}>🇺🇸 United States (+1)</option>
                        <option value="+91" data-country="IN" {{ old('country_code', $profile->country_code) == '+91' ? 'selected' : '' }}>🇮🇳 India (+91)</option>
                        <option value="+44" data-country="GB" {{ old('country_code', $profile->country_code) == '+44' ? 'selected' : '' }}>🇬🇧 United Kingdom (+44)</option>
                        <option value="+61" data-country="AU" {{ old('country_code', $profile->country_code) == '+61' ? 'selected' : '' }}>🇦🇺 Australia (+61)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contact Number  <span class="astrik">*</span></label>
                    <input type="tel" maxlength="15" id="phone" class="numbers-only" name="phone" required placeholder="Enter contact number" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" maxlength="254" id="email" name="email" required placeholder="Enter email address" value="{{ old('email', $user->email) }}" autocomplete="new-email" readonly disabled>
                </div>
                <div class="form-group">
                    <label>Home Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter home address" value="{{ old('address', $profile->address) }}">
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
                        <input type="text" id="date_of_birth" name="date_of_birth" placeholder="Enter Date" value="{{ old('date_of_birth', $profile->date_of_birth) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Availability Status</label>
                    <select id="availability_status" name="availability_status">
                        <option value="available" {{ old('availability_status', $profile->availability_status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="off_duty" {{ old('availability_status', $profile->availability_status) == 'off_duty' ? 'selected' : '' }}>Off Duty</option>
                        <option value="busy" {{ old('availability_status', $profile->availability_status) == 'busy' ? 'selected' : '' }}>Busy</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- 2. Licensing & Documentation -->
        <div class="form-section">
            <h3 data-number="2">Licensing & Documentation</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>License Number  <span class="astrik">*</span></label>
                    <input type="text" id="license_number" name="license_number" required placeholder="Enter license number" value="{{ old('license_number', $profile->license_number) }}">
                </div>
                <div class="form-group">
                    <label>License State</label>
                    <select id="license_state" name="license_state">
                        <option value="">Select state</option>
                        <option value="AL" {{ old('license_state', $profile->license_state) == 'AL' ? 'selected' : '' }}>Alabama</option>
                        <option value="AK" {{ old('license_state', $profile->license_state) == 'AK' ? 'selected' : '' }}>Alaska</option>
                        <option value="AZ" {{ old('license_state', $profile->license_state) == 'AZ' ? 'selected' : '' }}>Arizona</option>
                        <option value="AR" {{ old('license_state', $profile->license_state) == 'AR' ? 'selected' : '' }}>Arkansas</option>
                        <option value="CA" {{ old('license_state', $profile->license_state) == 'CA' ? 'selected' : '' }}>California</option>
                        <option value="CO" {{ old('license_state', $profile->license_state) == 'CO' ? 'selected' : '' }}>Colorado</option>
                        <option value="CT" {{ old('license_state', $profile->license_state) == 'CT' ? 'selected' : '' }}>Connecticut</option>
                        <option value="DE" {{ old('license_state', $profile->license_state) == 'DE' ? 'selected' : '' }}>Delaware</option>
                        <option value="FL" {{ old('license_state', $profile->license_state) == 'FL' ? 'selected' : '' }}>Florida</option>
                        <option value="GA" {{ old('license_state', $profile->license_state) == 'GA' ? 'selected' : '' }}>Georgia</option>
                        <option value="HI" {{ old('license_state', $profile->license_state) == 'HI' ? 'selected' : '' }}>Hawaii</option>
                        <option value="ID" {{ old('license_state', $profile->license_state) == 'ID' ? 'selected' : '' }}>Idaho</option>
                        <option value="IL" {{ old('license_state', $profile->license_state) == 'IL' ? 'selected' : '' }}>Illinois</option>
                        <option value="IN" {{ old('license_state', $profile->license_state) == 'IN' ? 'selected' : '' }}>Indiana</option>
                        <option value="IA" {{ old('license_state', $profile->license_state) == 'IA' ? 'selected' : '' }}>Iowa</option>
                        <option value="KS" {{ old('license_state', $profile->license_state) == 'KS' ? 'selected' : '' }}>Kansas</option>
                        <option value="KY" {{ old('license_state', $profile->license_state) == 'KY' ? 'selected' : '' }}>Kentucky</option>
                        <option value="LA" {{ old('license_state', $profile->license_state) == 'LA' ? 'selected' : '' }}>Louisiana</option>
                        <option value="ME" {{ old('license_state', $profile->license_state) == 'ME' ? 'selected' : '' }}>Maine</option>
                        <option value="MD" {{ old('license_state', $profile->license_state) == 'MD' ? 'selected' : '' }}>Maryland</option>
                        <option value="MA" {{ old('license_state', $profile->license_state) == 'MA' ? 'selected' : '' }}>Massachusetts</option>
                        <option value="MI" {{ old('license_state', $profile->license_state) == 'MI' ? 'selected' : '' }}>Michigan</option>
                        <option value="MN" {{ old('license_state', $profile->license_state) == 'MN' ? 'selected' : '' }}>Minnesota</option>
                        <option value="MS" {{ old('license_state', $profile->license_state) == 'MS' ? 'selected' : '' }}>Mississippi</option>
                        <option value="MO" {{ old('license_state', $profile->license_state) == 'MO' ? 'selected' : '' }}>Missouri</option>
                        <option value="MT" {{ old('license_state', $profile->license_state) == 'MT' ? 'selected' : '' }}>Montana</option>
                        <option value="NE" {{ old('license_state', $profile->license_state) == 'NE' ? 'selected' : '' }}>Nebraska</option>
                        <option value="NV" {{ old('license_state', $profile->license_state) == 'NV' ? 'selected' : '' }}>Nevada</option>
                        <option value="NH" {{ old('license_state', $profile->license_state) == 'NH' ? 'selected' : '' }}>New Hampshire</option>
                        <option value="NJ" {{ old('license_state', $profile->license_state) == 'NJ' ? 'selected' : '' }}>New Jersey</option>
                        <option value="NM" {{ old('license_state', $profile->license_state) == 'NM' ? 'selected' : '' }}>New Mexico</option>
                        <option value="NY" {{ old('license_state', $profile->license_state) == 'NY' ? 'selected' : '' }}>New York</option>
                        <option value="NC" {{ old('license_state', $profile->license_state) == 'NC' ? 'selected' : '' }}>North Carolina</option>
                        <option value="ND" {{ old('license_state', $profile->license_state) == 'ND' ? 'selected' : '' }}>North Dakota</option>
                        <option value="OH" {{ old('license_state', $profile->license_state) == 'OH' ? 'selected' : '' }}>Ohio</option>
                        <option value="OK" {{ old('license_state', $profile->license_state) == 'OK' ? 'selected' : '' }}>Oklahoma</option>
                        <option value="OR" {{ old('license_state', $profile->license_state) == 'OR' ? 'selected' : '' }}>Oregon</option>
                        <option value="PA" {{ old('license_state', $profile->license_state) == 'PA' ? 'selected' : '' }}>Pennsylvania</option>
                        <option value="RI" {{ old('license_state', $profile->license_state) == 'RI' ? 'selected' : '' }}>Rhode Island</option>
                        <option value="SC" {{ old('license_state', $profile->license_state) == 'SC' ? 'selected' : '' }}>South Carolina</option>
                        <option value="SD" {{ old('license_state', $profile->license_state) == 'SD' ? 'selected' : '' }}>South Dakota</option>
                        <option value="TN" {{ old('license_state', $profile->license_state) == 'TN' ? 'selected' : '' }}>Tennessee</option>
                        <option value="TX" {{ old('license_state', $profile->license_state) == 'TX' ? 'selected' : '' }}>Texas</option>
                        <option value="UT" {{ old('license_state', $profile->license_state) == 'UT' ? 'selected' : '' }}>Utah</option>
                        <option value="VT" {{ old('license_state', $profile->license_state) == 'VT' ? 'selected' : '' }}>Vermont</option>
                        <option value="VA" {{ old('license_state', $profile->license_state) == 'VA' ? 'selected' : '' }}>Virginia</option>
                        <option value="WA" {{ old('license_state', $profile->license_state) == 'WA' ? 'selected' : '' }}>Washington</option>
                        <option value="WV" {{ old('license_state', $profile->license_state) == 'WV' ? 'selected' : '' }}>West Virginia</option>
                        <option value="WI" {{ old('license_state', $profile->license_state) == 'WI' ? 'selected' : '' }}>Wisconsin</option>
                        <option value="WY" {{ old('license_state', $profile->license_state) == 'WY' ? 'selected' : '' }}>Wyoming</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>License Expiry Date  <span class="astrik">*</span></label>
                    <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
                        <input type="text" id="license_expiry_date" name="license_expiry_date" placeholder="Enter Date" required value="{{ old('license_expiry_date', $profile->license_expiry_date) }}">
                    </div>
                </div>
            </div>
            <div class="form-row two-cols">
                <div class="form-group">
                    <label>Insurance Policy Number</label>
                    <input type="text" id="insurance_policy" name="insurance_policy" placeholder="Enter insurance policy number" value="{{ old('insurance_policy', $profile->insurance_policy_number) }}">
                </div>
                <div class="form-group">
                    <label>Insurance Expiry Date</label>
                    <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
                        <input type="text" id="insurance_expiry" name="insurance_expiry" placeholder="Enter Date" value="{{ old('insurance_expiry', $profile->insurance_expiry_date) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- 3. Medical Compliance -->
        <div class="form-section">
            <h3 data-number="3">Medical Compliance</h3>

            <!-- HIPAA Certification Subsection -->
            <div class="subsection">
                <h4>HIPAA Certification</h4>
                <div class="form-row two-cols">
                    <div class="form-group">
                        <label>Certification Date</label>
                        <input type="text" id="hipaa_cert_date" name="hipaa_cert_date" placeholder="Enter Date" value="{{ old('hipaa_cert_date', $profile->hipaa_certification_date) }}">
                    </div>
                    <div class="file-upload-group">
                        <div class="form-group">
                            <label>File Upload</label>
                            <input type="file" id="hipaa_file" name="hipaa_file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                            <input type="text" id="hipaa_file_name" readonly placeholder="No file chosen">
                        </div>
                        <div class="file-actions">
                            <button type="button" class="btn-browse" onclick="document.getElementById('hipaa_file').click()">Browse</button>
                            @if($profile->hipaa_certification_file)
                                <a
                                    class="existing-file"
                                    href="{{ asset($profile->hipaa_certification_file) }}"
                                    target="_blank"
                                    rel="noopener"
                                    title="View current HIPAA certification file"
                                >
                                    <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                    <span>View</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row" style="margin-top: 20px;">
                <div class="form-group">
                    <label>Background Check Status</label>
                    <select id="background_check_status" name="background_check_status">
                        <option value="">Select status</option>
                        <option value="pending" {{ old('background_check_status', $profile->background_check_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('background_check_status', $profile->background_check_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="failed" {{ old('background_check_status', $profile->background_check_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Drug Screen Expiry</label>
                    <div class="calendar-input-wrapper">
                        <i class="fas fa-calendar-alt calendar-icon"></i>
                        <input type="text" id="drug_screen_expiry" name="drug_screen_expiry" placeholder="Enter Date" value="{{ old('drug_screen_expiry', $profile->drug_screen_expiry) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Specimen Handling Certification <span class="astrik">*</span></label>
                    <div class="calendar-input-wrapper">
                        <i class="fas fa-calendar-alt calendar-icon"></i>
                        <input type="text" id="specimen_handling_cert" name="specimen_handling_cert" placeholder="Enter Date" value="{{ old('specimen_handling_cert', $profile->specimen_handling_certification_date) }}">
                    </div>
                </div>
            </div>
            <div class="checkbox-option">
                <input type="checkbox" id="specimen_cert_confirm" name="specimen_cert_confirm" {{ old('specimen_cert_confirm', $profile->specimen_handling_confirmed) ? 'checked' : '' }}>
                <label for="specimen_cert_confirm">I confirm that specimen handling certification is completed</label>
            </div>

            <!-- Bloodborne Pathogen Subsection -->
            <div class="subsection">
                <h4>Bloodborne Pathogen</h4>
                <div class="form-row two-cols">
                    <div class="form-group">
                        <label>Training Date</label>
                        <div class="calendar-input-wrapper">
                            <i class="fas fa-calendar-alt calendar-icon"></i>
                            <input type="date" id="bloodborne_training_date" name="bloodborne_training_date" placeholder="Enter Date" value="{{ old('bloodborne_training_date', $profile->bloodborne_pathogen_training_date) }}">
                        </div>
                    </div>
                    <div class="file-upload-group">
                        <div class="form-group">
                            <label>File Upload</label>
                            <input type="file" id="bloodborne_file" name="bloodborne_file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                            <input type="text" id="bloodborne_file_name" readonly placeholder="No file chosen">
                        </div>
                        <div class="file-actions">
                            <button type="button" class="btn-browse" onclick="document.getElementById('bloodborne_file').click()">Browse</button>
                        </div>

                        <div class="file-actions">
                            @if($profile->bloodborne_pathogen_file)
                                <a
                                    class="existing-file"
                                    href="{{ $profile->bloodborne_pathogen_file }}"
                                    target="_blank"
                                    rel="noopener"
                                    title="View Bloodborne Pathogen file"
                                >
                                    <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                    <span>View</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-submit" id="submitBtn">Update Profile</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('input[type="file"]').forEach(function (fileInput) {
        fileInput.addEventListener('change', function () {
            const fileNameInput = document.getElementById(this.id + '_name');

            if (fileNameInput) {
                fileNameInput.value = this.files.length ? this.files[0].name : '';
            }
        });
    });
</script>
@endsection
