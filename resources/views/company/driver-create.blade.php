@extends('common.layout')

@section('title', 'Create Driver Profile')
@section('page-title', 'Create Driver Profile')

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
        display: block;
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
        position: relative;
        top: -4px;
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
    }

    .file-upload-group .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .btn-browse {
        padding: 11px 20px;
        background: #e0e0e0;
        color: #2c3e50;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
    }

    .btn-browse:hover {
        background: #d0d0d0;
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
    

    <form id="createDriverForm" method="POST" enctype="multipart/form-data" action="{{ route('driver.register') }}" autocomplete="off">
        @csrf
        <!-- 1. Personal & Contact Info -->
        <div class="form-section">
            <h3 data-number="1">Personal & Contact Info</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" class="letters-only" id="name" name="name" maxlength="100" required placeholder="Enter full name" value="{{ old('name') }}" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Country Code *</label>
                    <select name="country_code" id="country_code" required>
                        <option value="+1" data-country="US">🇺🇸 United States (+1)</option>
                        <option value="+91" data-country="IN">🇮🇳 India (+91)</option>
                        <option value="+44" data-country="GB">🇬🇧 United Kingdom (+44)</option>
                        <option value="+61" data-country="AU">🇦🇺 Australia (+61)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contact Number *</label>
                    <input type="tel" maxlength="15" id="phone" class="numbers-only" name="phone" required placeholder="Enter contact number" maxlength="15" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" maxlength="254" id="email" name="email" class="email-only" required placeholder="Enter email address" maxlength="80" value="{{ old('email') }}" autocomplete="new-email">
                </div>
            </div>
            <div class="form-row two-cols">
                <div class="form-group">
                    <label>Home Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter home address" maxlength="100" value="{{ old('address') }}">
                </div>
                 <div class="form-group">
                  <label>Date of Birth</label>
                    <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
                   
                    <input type="text" id="date_of_birth" name="date_of_birth" placeholder="Enter Date" value="{{ old('date_of_birth') }}">
                </div></div>
            </div>
            <div class="form-row two-cols">
                <div class="form-group">
                    <label for="password">Password *</label>        
                    <div class="password-wrapper">
                        <input type="password" maxlength="128" id="password" name="password" required placeholder="Enter password" value="{{ old('password') }}" autocomplete="new-password"> 
                        <svg class="eye-icon" id="togglePassword" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <div class="password-wrapper">
                        <input type="password" maxlength="128" id="password_confirmation" name="password_confirmation" required placeholder="Confirm password" value="{{ old('password_confirmation') }}" autocomplete="new-password">
                        <svg class="eye-icon" id="togglePasswordConfirm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- 2. Licensing & Documentation -->
        <div class="form-section">
            <h3 data-number="2">Licensing & Documentation</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>License Number *</label>
                    <input type="text" id="license_number" name="license_number" required placeholder="Enter license number" maxlength="30" value="{{ old('license_number') }}">
                </div>
                <div class="form-group">
                    <label>License State</label>
                    <select id="license_state" name="license_state">
                        <option value="">Select state</option>
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AZ">Arizona</option>
                        <option value="AR">Arkansas</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL">Illinois</option>
                        <option value="IN">Indiana</option>
                        <option value="IA">Iowa</option>
                        <option value="KS">Kansas</option>
                        <option value="KY">Kentucky</option>
                        <option value="LA">Louisiana</option>
                        <option value="ME">Maine</option>
                        <option value="MD">Maryland</option>
                        <option value="MA">Massachusetts</option>
                        <option value="MI">Michigan</option>
                        <option value="MN">Minnesota</option>
                        <option value="MS">Mississippi</option>
                        <option value="MO">Missouri</option>
                        <option value="MT">Montana</option>
                        <option value="NE">Nebraska</option>
                        <option value="NV">Nevada</option>
                        <option value="NH">New Hampshire</option>
                        <option value="NJ">New Jersey</option>
                        <option value="NM">New Mexico</option>
                        <option value="NY">New York</option>
                        <option value="NC">North Carolina</option>
                        <option value="ND">North Dakota</option>
                        <option value="OH">Ohio</option>
                        <option value="OK">Oklahoma</option>
                        <option value="OR">Oregon</option>
                        <option value="PA">Pennsylvania</option>
                        <option value="RI">Rhode Island</option>
                        <option value="SC">South Carolina</option>
                        <option value="SD">South Dakota</option>
                        <option value="TN">Tennessee</option>
                        <option value="TX">Texas</option>
                        <option value="UT">Utah</option>
                        <option value="VT">Vermont</option>
                        <option value="VA">Virginia</option>
                        <option value="WA">Washington</option>
                        <option value="WV">West Virginia</option>
                        <option value="WI">Wisconsin</option>
                        <option value="WY">Wyoming</option>
                    </select>
                </div>
               
                    <div class="form-group">
                  <label>License Expiry Date *</label>
                    <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
                    <input type="text" id="license_expiry_date" name="license_expiry_date" placeholder="Enter Date" required>
                </div></div>
            </div>
            <div class="form-row two-cols">
                <div class="form-group">
                    <label>Insurance Policy Number</label>
                    <input type="text" id="insurance_policy" name="insurance_policy" placeholder="Enter insurance policy number" maxlength="20" value="{{old('insurance_policy')}}">
                </div>
               <div class="form-group">
                  <label>Insurance Expiry Date</label>
                    <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
                    <input type="text" id="insurance_expiry" name="insurance_expiry" placeholder="Enter Date" value="{{old('insurance_expiry')}}">
                </div></div>
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
                        <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
                        <input type="text" id="hipaa_cert_date" name="hipaa_cert_date" placeholder="Enter Date " value="{{old('hipaa_cert_date')}}"></div>
                    </div>


                    <div class="file-upload-group">
                        <div class="form-group">
                            <label>File Upload</label>
                            <input type="file" id="hipaa_file" name="hipaa_file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                            <input type="text" id="hipaa_file_name" readonly placeholder="No file chosen">
                        </div>
                        <button type="button" class="btn-browse" onclick="document.getElementById('hipaa_file').click()">Browse</button>
                    </div>
                </div>
            </div>

            <div class="form-row" style="margin-top: 20px;">
                <div class="form-group">
                    <label>Background Check Status</label>
                    <select id="background_check_status" name="background_check_status">
                        <option value="">Select status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Drug Screen Expiry</label>
                    <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
                        <input type="text" id="drug_screen_expiry" name="drug_screen_expiry" placeholder="Enter Date " value="{{old('drug_screen_expiry')}}"></div>
                </div>
                <div class="form-group">
                    <label>Specimen Handling Certification <span class="astrik">*</span></label>
                     <div class="calendar-input-wrapper">
                       <i class="fas fa-calendar-alt calendar-icon"></i>
                    <input type="text" id="specimen_handling_cert" name="specimen_handling_cert" placeholder="Enter Date" value="{{old('specimen_handling_cert')}}"></div>
                </div>
            </div>
            <div class="checkbox-option">
                <input type="checkbox" id="specimen_cert_confirm" name="specimen_cert_confirm" {{ old('specimen_cert_confirm') ? 'checked' : '' }}>
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
                        <input type="text" id="bloodborne_training_date" name="bloodborne_training_date" placeholder="Enter Date" value="{{old('bloodborne_training_date')}}"></div>
                    </div>
                    <div class="file-upload-group">
                        <div class="form-group">
                            <label>File Upload</label>
                            <input type="file" id="bloodborne_file" name="bloodborne_file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                            <input type="text" id="bloodborne_file_name" readonly placeholder="No file chosen">
                        </div>
                        <button type="button" class="btn-browse" onclick="document.getElementById('bloodborne_file').click()">Browse</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-submit" id="submitBtn">Create Profile</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')

<script>
    // document.getElementById('createDriverForm').addEventListener('submit', handleFormSubmit);
    // async function handleFormSubmit(e) {
    //     show_load_spinner('content','Creating Driver Profile...','class');
    //     e.preventDefault();

    //     if (!validatePhone()) {
    //         hide_load_spinner('content','class');
    //         return false;
    //     }

    //     // Submit the form
    //     e.target.submit();
    // }
</script>
 <script>
    function setupPasswordToggle(passwordInputId, toggleIconId) {
        const passwordInput = document.getElementById(passwordInputId);
        const toggleIcon = document.getElementById(toggleIconId);

        toggleIcon.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Show eye-slash icon
                toggleIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                passwordInput.type = 'password';
                // Show eye icon
                toggleIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        });
    }

    // Setup toggle for both password fields
    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('password_confirmation', 'togglePasswordConfirm');
</script>
@endsection
