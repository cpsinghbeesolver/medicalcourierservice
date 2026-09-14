@extends('common.layout')

@section('title', 'Hospital Management')
@section('page-title', 'Hospital Management')

@section('styles')
@endsection

@section('content')

<!-- Data Table -->
<div class="data-card">
    <div class="data-card-header">
        <h3>All Hospitals</h3>
        <a onclick="addHospital(this)" class="btn-create">
            <i class="fas fa-plus"></i> Add Hospital
        </a>
    </div>
    <div class="table-container">
        <table class="data-table" id="driversTable">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <!-- <th>Status</th>
                    <th>Actions</th> -->
                </tr>
            </thead>
            <tbody>
                @if($hospitals)
                    @foreach($hospitals as $hospital)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $hospital->name }}</td>
                            @if(1 == 0)
                            <td>
                                @if($hospital->status == '1')
                                    <span class="badge active">Active</span>
                                @elseif($hospital->status == '0')
                                    <span class="badge inactive">In-Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="window.location.href='{{ route('specimen-types.edit', $specimenType->id) }}'">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <form id="delete-form-{{ $hospital->id }}"
                                        action="{{ route('specimen-types.destroy', $hospital) }}"
                                        method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button class="btn-action btn-delete" onclick="confirmDeleteSpecimenType('{{ $specimenType->id }}')" title="Decline Enquiry">
                                        <i class="fas fa-times"></i> Delete
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 60px;">No Specimen Type found.</td>
                    </tr>
                @endif

            </tbody>

        </table>
    </div>
</div>

<!-- Assign Hopital Modal -->
<div class="specimen_type_modal" id="addHospitalModal">
    <div class="specimen_type_modal-content">
            <div class="specimen_type_modal-header">
                <h3>Add Hospital</h3>
            </div>
            <form method="POST" id="add_hospital" action="" style="display: contents;">
            <div class="specimen_type_modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" id="hospital_name" maxlength="200" name="hospital_name" placeholder="Please add name" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="hospital_email" name="hospital_email" maxlength="254" placeholder="Please add email" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Registration Number</label>
                            <input type="text" id="hospital_registration" name="hospital_registration" maxlength="254" placeholder="Please add registration number" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Address</label>
                            <div class="location-input-wrapper">
                                <i class="fas fa-map-marker-alt location-icon"></i>
                                <input type="text" id="hospital_address" class="hospital-location" name="hospital_address" placeholder="Please add address" autocomplete="off">
                                <input type="hidden" id="hospital_lat" name="hospital_lat">
                                <input type="hidden" id="hospital_long" name="hospital_long">
                                <input type="hidden" id="hospital_city" name="hospital_city">
                                <input type="hidden" id="hospital_state" name="hospital_state">
                                <input type="hidden" id="hospital_country" name="hospital_country">
                                <input type="hidden" id="hospital_zip" name="hospital_zip">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Contact Person</label>
                            <input type="text" id="hospital_contact_person" name="hospital_contact_person" placeholder="Please add contact Person" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" maxlength="15" class="numbers-only" id="hospital_phone" name="hospital_phone" placeholder="Please add phone no." autocomplete="off">
                        </div>
                    </div>
                    <div id="driversList"></div>

            </div>
            <div class="specimen_type_modal-footer">
                <button class="btn-modal btn-modal-cancel" type="button" onclick="closeModalHospital()">Cancel</button>
                <button class="btn-modal btn-modal-assign" id="btnAddHospital" type="submit">Add Hospital</button>
            </div>
            </form>
    </div>
</div>
{{ $hospitals->links() }}
@endsection
@section('scripts')
<script>
    var token = "{{ session('web_token') }}";
    function addHospital(thisButton){
        var index = $(thisButton).parents('.item-card').attr('data-item-index');
        $('form#add_hospital')[0].reset();
        document.getElementById('addHospitalModal').classList.add('show');
    }
    function closeModalHospital(){
        document.getElementById('addHospitalModal').classList.remove('show');
    }
    let autocompleteInstances = [];
    // initHospitalAutocomplete();
    function getAddressComponent(place, type, useShortName = false) {
        const component = place.address_components?.find(c =>
            c.types.includes(type)
        );

        if (!component) return '';

        return useShortName ? component.short_name : component.long_name;
    }
    // Initialize autocomplete for delivery locations
    function initHospitalAutocomplete() {
        const deliveryInputs = document.querySelectorAll('.hospital-location');
        deliveryInputs.forEach(input => {
            // Skip if already initialized
            if (input.dataset.autocompleteInitialized) return;

            const autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['address'],
                componentRestrictions: { country: ['us', 'in'] } // Change to your country code
            });

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    const wrapper = input.closest('#add_hospital');
                    wrapper.querySelector('#hospital_lat').value = place.geometry.location.lat();
                    wrapper.querySelector('#hospital_long').value = place.geometry.location.lng();

                    const country = getAddressComponent(place, 'country');
                    const city = getAddressComponent(place, 'locality');
                    const state = getAddressComponent(place, 'administrative_area_level_1');
                    const stateCode = getAddressComponent(place, 'administrative_area_level_1', true);
                    const zipCode = getAddressComponent(place, 'postal_code');
                    wrapper.querySelector('#hospital_city').value = city;
                    wrapper.querySelector('#hospital_state').value = state;
                    wrapper.querySelector('#hospital_zip').value = zipCode;
                    wrapper.querySelector('#hospital_country').value = country;

                }
            });

            input.dataset.autocompleteInitialized = 'true';
            autocompleteInstances.push(autocomplete);
        });
    }
</script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKwk2TS_ydCfzq7vx4SZnewwW9hX_YidQ&libraries=places&callback=initHospitalAutocomplete" async defer></script>

@endsection
