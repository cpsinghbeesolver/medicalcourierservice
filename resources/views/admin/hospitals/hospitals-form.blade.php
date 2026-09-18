<div class="form-section">
    <h3>Hospital Information</h3>

    <div class="form-row">
        <div class="form-group">
            <label>Hospital Name <span class="astrik">*</span></label>
            <input type="text" name="name" value="{{ old('name', $hospital->name ?? '') }}">
            @error('name') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Hospital Email <span class="astrik">*</span></label>
            <input type="text" name="email" value="{{ old('email', $hospital->email ?? '') }}">
            @error('email') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Registration Number <span class="astrik">*</span></label>
            <input type="text" name="registration_number" value="{{ old('registration_number', $hospital->registration_number ?? '') }}">
            @error('registration_number') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>

        
    </div>

    <div class="form-row two-col">
        <div class="form-group">
            <label>Contact Person</label>
            <input type="text" name="contact_person" value="{{ old('contact_person', $hospital->contact_person ?? '') }}">
            @error('contact_person') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="numbers-only" value="{{ old('phone', $hospital->phone ?? '') }}">
            @error('phone') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="form-section">
    <h3>Address</h3>

    <div class="form-row one-col">
        <div class="form-group">
            <label>Address</label>
            <div class="location-input-wrapper">
                <i class="fas fa-map-marker-alt location-icon"></i>
                <input type="text" name="address" class="hospital-location"
                       value="{{ old('address', $hospital->address ?? '') }}"
                       placeholder="Start typing an address..." autocomplete="off">
            </div>
            @error('address') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" value="{{ old('city', $hospital->city ?? '') }}">
            @error('city') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>State</label>
            <input type="text" name="state" value="{{ old('state', $hospital->state ?? '') }}">
            @error('state') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>ZIP Code</label>
            <input type="text" name="zip" value="{{ old('zip', $hospital->zip ?? '') }}">
            @error('zip') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="form-row two-cols">
        <div class="form-group">
            <label>Country</label>
            <input type="text" name="country" value="{{ old('country', $hospital->country ?? '') }}">
            @error('country') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="form-row two-cols">
        <div class="form-group">
            <label>Latitude</label>
            <input type="text" name="latitude" value="{{ old('latitude', $hospital->latitude ?? '') }}">
            @error('latitude') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Longitude</label>
            <input type="text" name="longitude" value="{{ old('longitude', $hospital->longitude ?? '') }}">
            @error('longitude') <div class="just-validate-error-label" style="color:#e74c3c;">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<script>
    let autocompleteInstances = [];

    function getAddressComponent(place, type, useShortName = false) {
        const component = place.address_components?.find(c =>
            c.types.includes(type)
        );
        if (!component) return '';
        return useShortName ? component.short_name : component.long_name;
    }

    // Initialize autocomplete for the hospital address field.
    // Looks fields up by `name` within the closest <form>, so this works
    // the same way on both the Create and Edit pages without id collisions.
    function initHospitalAutocomplete() {
        const addressInputs = document.querySelectorAll('.hospital-location');
        addressInputs.forEach(input => {
            if (input.dataset.autocompleteInitialized) return;

            const autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['address'],
                componentRestrictions: { country: ['us', 'in'] } // Adjust to your supported countries
            });

            autocomplete.addListener('place_changed', function () {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                const form = input.closest('form');
                if (!form) return;

                const setField = (name, value) => {
                    const field = form.querySelector(`[name="${name}"]`);
                    if (field) field.value = value;
                };

                setField('latitude', place.geometry.location.lat());
                setField('longitude', place.geometry.location.lng());
                setField('city', getAddressComponent(place, 'locality'));
                setField('state', getAddressComponent(place, 'administrative_area_level_1'));
                setField('zip', getAddressComponent(place, 'postal_code'));
                setField('country', getAddressComponent(place, 'country'));
            });

            input.dataset.autocompleteInitialized = 'true';
            autocompleteInstances.push(autocomplete);
        });
    }
</script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKwk2TS_ydCfzq7vx4SZnewwW9hX_YidQ&libraries=places&callback=initHospitalAutocomplete" async defer></script>
