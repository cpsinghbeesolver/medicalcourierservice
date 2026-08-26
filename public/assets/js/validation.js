const validators = new Map();

function validateForm() {

    document.querySelectorAll('form').forEach((form) => {

        // Destroy existing validator for this form
        if (validators.has(form)) {
            validators.get(form).destroy();
        }

        // Create fresh validator
        const validator = new JustValidate(form);

        // Store it
        validators.set(form, validator);

        // Validation rules...
        const email = document.querySelector('#email');
        if (email) {
            validator.addField(document.querySelector('#email'), [
                {
                    rule: 'required',
                    errorMessage: 'Email is required',
                },
                {
                rule: 'email',
                },
            ])
        }
        const password = document.querySelector('#password');
        if (password) {
            validator.addField(document.querySelector('#password'), [
                {
                    rule: 'required',
                    errorMessage: 'Password is required',
                },
                {
                rule: 'password',
                },
            ])
        }
        const new_password = document.querySelector('#new_password');
        if (new_password) {
            validator.addField(document.querySelector('#new_password'), [
                {
                    rule: 'required',
                    errorMessage: 'Password is required',
                },
                {
                rule: 'password',
                },
            ])
        }
        const current_password = document.querySelector('#current_password');
        if (current_password) {
            validator.addField(document.querySelector('#current_password'), [
                {
                    rule: 'required',
                    errorMessage: 'Password is required',
                },
                {
                rule: 'password',
                },
            ])
        }
        const licenseNumber = document.querySelector('#license_number');
        if (licenseNumber) {
            validator.addField(licenseNumber, [
                {
                    rule: 'required',
                    errorMessage: 'License number is required',
                },
            ]);
        }
        const specimen_handling_cert = document.querySelector('#specimen_handling_cert');
        if (specimen_handling_cert) {
            validator.addField(specimen_handling_cert, [
                {
                    rule: 'required',
                    errorMessage: 'Specimen Handling Certification is required',
                },
            ]);
        }
        
        
        const specimen_cert_confirm = document.querySelector('#specimen_cert_confirm');
        if (specimen_cert_confirm) {
            validator.addField(specimen_cert_confirm, [
                {
                    rule: 'required',
                    errorMessage: 'Specimen handling certification is required',
                },
            ]);
        }

        const license_expiry_date = document.querySelector('#license_expiry_date');
        if (license_expiry_date) {
            validator.addField(document.querySelector('#license_expiry_date'), [
                {
                    rule: 'required',
                    errorMessage: 'License Expiry Date is required',
                }
            ])
        }
        const name = document.querySelector('#name');
        if (name) {
            validator.addField(document.querySelector('#name'), [
                {
                    rule: 'required',
                    errorMessage: 'Name is required',
                },
                {
                    rule: 'minLength',
                    value: 2,
                    errorMessage: 'Name must be at least 2 characters',
                },
                {
                    rule: 'maxLength',
                    value: 50,
                    errorMessage: 'Name cannot exceed 50 characters',
                },
                {
                    validator: (value) => /^[A-Za-z\s]+$/.test(value.trim()),
                    errorMessage: 'Name can only contain letters and spaces',
                },
            ])
        }
        const password_confirmation = document.querySelector('#password_confirmation');
        if (password_confirmation) {
            validator.addField(document.querySelector('#password_confirmation'), [
                {
                    rule: 'required',
                    errorMessage: 'Please confirm your password',
                },
                {
                    validator: (value, fields) => {
                        return value === document.querySelector('#password').value;
                    },
                    errorMessage: 'Passwords do not match',
                },
            ])
        }
        const phone = document.querySelector('#phone');
        if (phone) {
            validator.addField(document.querySelector('#phone'), [
                {
                    rule: 'required',
                    errorMessage: 'Contact number is required',
                },
                {
                    validator: (value) => {
                        return validatePhone();
                    },
                    errorMessage: 'Invalid Contact Number',
                },
            ])
        }
        const profile_phone = document.querySelector('#profile_phone');
        if (profile_phone) {
            validator.addField(document.querySelector('#profile_phone'), [
                {
                    rule: 'required',
                    errorMessage: 'Phone number is required',
                },
                {
                    validator: (value) => {
                        const phoneRegex = /^\+?[0-9\s\-()]{7,20}$/;
                        return phoneRegex.test(value.trim());
                    },
                    errorMessage: 'Please enter a valid Phone number',
                },
            ])
        }
        const mobile_no = document.querySelector('#mobile_no');
        if (mobile_no) {
            validator.addField(document.querySelector('#mobile_no'), [
                {
                    rule: 'required',
                    errorMessage: 'Mobile number is required',
                },
                {
                    validator: (value) => {
                        const phoneRegex = /^\+?[0-9\s\-()]{7,20}$/;
                        return phoneRegex.test(value.trim());
                    },
                    errorMessage: 'Please enter a valid Mobile number',
                },
            ])
        }
        
        const jobTitle = document.querySelector('#jobTitle');
        if (jobTitle) {
            validator.addField(document.querySelector('#jobTitle'), [
                {
                    rule: 'required',
                    errorMessage: 'Job Title is required',
                },
                {
                    rule: 'minLength',
                    value: 2,
                    errorMessage: 'Job Title must be at least 2 characters',
                },
                {
                    rule: 'maxLength',
                    value: 100,
                    errorMessage: 'Job Title cannot exceed 100 characters',
                }
            ])
        }
        const pickupLocation = document.querySelector('#pickupLocation');
        if (pickupLocation) {
            validator.addField(document.querySelector('#pickupLocation'), [
                {
                    rule: 'required',
                    errorMessage: 'Pickup Location is required',
                },
            ])
        }
        const timeWindowStart = document.querySelector('#timeWindowStart');
        if (timeWindowStart) {
            validator.addField(document.querySelector('#timeWindowStart'), [
                {
                    rule: 'required',
                    errorMessage: 'Pickup Location is required',
                },
            ])
        }
        const timeWindowEnd = document.querySelector('#timeWindowEnd');
        if (timeWindowEnd) {
            validator.addField(document.querySelector('#timeWindowEnd'), [
                {
                    rule: 'required',
                    errorMessage: 'Delivery Deadline is required',
                },
            ])
        }
        const urgencyLevel = document.querySelector('#urgencyLevel');
        if (urgencyLevel) {
            validator.addField(document.querySelector('#urgencyLevel'), [
                {
                    rule: 'required',
                    errorMessage: 'Urgency Level is required',
                },
            ])
        }
        const pickup_phone = document.querySelector('#pickup_phone');
        if (pickup_phone) {
            validator.addField(document.querySelector('#pickup_phone'), [
                {
                    rule: 'required',
                    errorMessage: 'Pickup Phone is required',
                },
                {
                    validator: (value) => {
                        const phoneRegex = /^\+?[0-9\s\-()]{7,20}$/;
                        return phoneRegex.test(value.trim());
                    },
                    errorMessage: 'Please enter a valid Phone number',
                },
            ])
        }

        document.querySelectorAll('[name^="items["][name$="[item_name]"]')
            .forEach((field) => {
                validator.addField(field, [
                    {
                        rule: 'required',
                        errorMessage: 'Item Name is required',
                    },
                    {
                        rule: 'minLength',
                        value: 2,
                        errorMessage: 'Item Name must be at least 2 characters',
                    },
                    {
                        rule: 'maxLength',
                        value: 100,
                        errorMessage: 'Item Name cannot exceed 100 characters',
                    }
                ]);
        });

        document.querySelectorAll('[name^="items["][name$="[dropoff_address]"]')
            .forEach((field) => {
                validator.addField(field, [
                    {
                        rule: 'required',
                        errorMessage: 'Dropoff Address is required',
                    }
                ]);
        });
        document.querySelectorAll('[name^="items["][name$="[dropoff_location]"]')
            .forEach((field) => {
                validator.addField(field, [
                    {
                        rule: 'required',
                        errorMessage: 'Near By Landmark is required',
                    }
                ]);
        });

        document.querySelectorAll('[name^="items["][name$="[specimen_id]"]')
            .forEach((field) => {
                validator.addField(field, [
                    {
                        rule: 'required',
                        errorMessage: 'Specimen ID is required',
                    }
                ]);
        });

        document.querySelectorAll('[name^="items["][name$="[temperature_requirement]"]')
            .forEach((field) => {
                validator.addField(field, [
                    {
                        rule: 'required',
                        errorMessage: 'Temperature is required',
                    }
                ]);
        });
        document.querySelectorAll('[name^="items["][name$="[specimen_type]"]')
            .forEach((field) => {
                validator.addField(field, [
                    {
                        rule: 'required',
                        errorMessage: 'Specimen Type is required',
                    }
                ]);
        });
        
        
        document.querySelectorAll('[name^="items["][name$="[dropoff_phone]"]')
            .forEach((field) => {
                validator.addField(field, [
                    {
                        rule: 'required',
                        errorMessage: 'Drop Off Phone is required',
                    },
                    {
                        validator: (value) => {
                            const phoneRegex = /^\+?[0-9\s\-()]{7,20}$/;
                            return phoneRegex.test(value.trim());
                        },
                        errorMessage: 'Please enter a valid Phone number',
                    },
                ]);

                
        });

        validator
        .onSuccess((event) => {
            
            if (document.getElementById('loginForm')) {
                document.querySelector('.loading-spinner').style.display = 'block';
                document.getElementById('loginBtn').disabled = true;
                event.currentTarget.submit();
            }
            if(document.getElementById('createDriverForm')){
                show_load_spinner();
                event.currentTarget.submit();
            }        
        });
    });
}
validateForm();
