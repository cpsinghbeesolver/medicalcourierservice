@extends('common.layout')

@section('title', 'Edit Job')
@section('page-title', 'Edit Job')

@section('styles')
@endsection

@section('content')
<a href="/company/dashboard/deliveries" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Jobs
</a>
@php
    $deliveryItems = $delivery?->items ?? collect();
    $firstDeliveryItem = $deliveryItems->first();
    $pickupStartValue = old('scheduled_time_window_start', optional($delivery?->scheduled_time_window_start)->format('Y-m-d\TH:i') ?? '');
    $pickupEndValue = old('scheduled_time_window_end', optional($delivery?->scheduled_time_window_end)->format('Y-m-d\TH:i') ?? '');
@endphp
<div class="form-container">
    <form id="jobEditForm">
        <!-- 1. Logistics Basics -->
        <div class="form-section">
            <h3>1. Logistics Basics</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Job Title/Reference</label>
                    <input type="text" name="job_title" id="jobTitle" placeholder="Enter job title or reference" value="{{ old('job_title', optional($delivery)->pickup_name ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Pickup Location <span class="astrik">*</span></label>
                    <div class="location-input-wrapper">
                        <i class="fas fa-map-marker-alt location-icon"></i>
                        <input type="text"
                               id="pickupLocation"
                               name="pickup_location"
                               class="location-search"
                               placeholder="Search pickup address..."
                               autocomplete="off"
                               required
                               value="{{ old('pickup_location', optional($delivery)->pickup_address ?? '') }}">
                        <input type="hidden" id="pickupLat" name="pickup_latitude" value="{{ old('pickup_latitude', optional($delivery)->pickup_latitude ?? '') }}">
                        <input type="hidden" id="pickupLng" name="pickup_longitude" value="{{ old('pickup_longitude', optional($delivery)->pickup_longitude ?? '') }}">
                        <input type="hidden" id="pickupZipCode" name="pickupZipCode" value="{{ old('pickupZipCode', optional($delivery)->pickup_zip ?? '') }}">
                        <input type="hidden" id="pickupCity" name="pickupCity" value="{{ old('pickupCity', optional($delivery)->pickup_city ?? '') }}">
                        <input type="hidden" id="pickupState" name="pickupState" value="{{ old('pickupState', optional($delivery)->pickup_state ?? '') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Pickup Window <span class="astrik">*</span></label>
                    <div class="location-input-wrapper">
                       <i class="fas fa-calendar-alt location-icon"></i>
                        <input type="datetime-local" name="scheduled_time_window_start" id="timeWindowStart" placeholder="Select date and time" value="{{ $pickupStartValue }}" required>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Pickup Phone No. <span class="astrik">*</span></label>
                    <input type="text" name="pickup_phone" id="pickup_phone" placeholder="Enter pickup phone number" value="{{ old('pickup_phone', optional($delivery)->pickup_phone ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>Delivery Deadline <span class="astrik">*</span></label>
                    <input type="datetime-local" name="scheduled_time_window_end" id="timeWindowEnd" placeholder="Select date and time" value="{{ $pickupEndValue }}" required>
                </div>
                
            </div>
        </div>


        <!-- 2. Assignment & Instructions -->
        <div class="form-section">
            <h3>2. Assignment & Instructions</h3>
            <div class="form-row two-cols">
                <div class="form-group">
                    <label>Assigned Driver</label>
                    <select name="driver_id" id="driverId">
                        <option value="">Select</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="add-more-container">
                        <label>Vehicle Requirements</label>
                        <button type="button" class="btn-add-type" onclick="addVehicleRequirement()" title="Add More Vehicle">
                                <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <select name="required_vehicle_type" id="vehicleType">
                        <option value="">Select</option>
                        @foreach($vehicleRequirements as $vehicleRequirement)
                            <option value="{{ $vehicleRequirement->id }}" @selected(old('required_vehicle_type', optional($delivery)->required_vehicle_type ?? '') == $vehicleRequirement->id)>
                                {{ $vehicleRequirement->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row one-col" style="max-width: 100%;">
                <div class="form-group">
                    <label>Special Instructions</label>
                    <textarea name="special_instructions" id="specialInstructions" placeholder="Any special instructions">{{ old('special_instructions', optional($delivery)->special_instructions ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="specimen_details">
            <!-- 3. Specimen Details -->
            <div class="form-section">
                <h3>3. Specimen Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Priority <span class="astrik">*</span></label>
                        <select name="priority" id="priority" required>
                            <option value="">Select</option>
                            <option value="low" @selected(old('priority', optional($delivery)->priority ?? '') === 'low')>Low</option>
                            <option value="normal" @selected(old('priority', optional($delivery)->priority ?? '') === 'normal')>Normal</option>
                            <option value="high" @selected(old('priority', optional($delivery)->priority ?? '') === 'high')>High</option>
                            <option value="urgent" @selected(old('priority', optional($delivery)->priority ?? '') === 'urgent')>Urgent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Urgency Level</label>
                        <select name="urgency_level" id="urgencyLevel" required>
                            <option value="">Select</option>
                            <option value="routine" @selected(old('urgency_level', optional($delivery)->urgency_level ?? '') === 'routine')>Routine</option>
                            <option value="stat" @selected(old('urgency_level', optional($delivery)->urgency_level ?? '') === 'stat')>Stat</option>
                            <option value="life_threatening" @selected(old('urgency_level', optional($delivery)->urgency_level ?? '') === 'life_threatening')>Life Threatening</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Number of Containers/Bags</label>
                        <input type="number" name="number_of_containers" id="numberOfContainers" placeholder="Number of containers" value="{{ old('number_of_containers', optional($delivery)->container_count ?? 1) }}" min="1" required>
                    </div>
                </div>
            </div>

            <!-- 4. Items for Delivery -->
            <div class="form-section">
                <div class="section-header">
                    <h3>4. Items for Delivery</h3>
                    <div class="add-item-wrapper">
                        <button type="button" class="btn-add-item" onclick="addItem()" title="Add more item">
                            <i class="fas fa-plus"></i>
                        </button>
                        &nbsp;
                        <span class="add-more-item-text">Add more Item</span>
                    </div>
                </div>
                @foreach($deliveryItems AS $deliveryItem)
                    @php
                        //echo '<pre>'; print_r($deliveryItem); echo '</pre>';
                    @endphp
                    <div id="itemsList">
                        <div class="item-card" data-item-index="0">
                            <div class="form-row two-cols">
                                <div class="form-group">
                                    <label>Item 1 Name <span class="astrik">*</span></label>
                                    <input type="text" name="items[{{$loop->index}}][item_name]" value="{{ old('items.' . $loop->index . '.item_name', optional($deliveryItem)->item_name ?? '') }}" placeholder="Item name" required>
                                </div>
                                <div class="form-group">
                                    <label>Handling Instructions</label>
                                    <input type="text" name="items[{{$loop->index}}][handling_instructions]" value="{{ old('items.' . $loop->index . 'handling_instructions', optional($deliveryItem)->handling_instructions ?? '') }}" placeholder="Handling instructions">
                                </div>
                                
                            </div>
                            <div class="form-row two-cols">
                                <div class="form-group">
                                    <label>Drop Off Type <span class="astrik">*</span></label>
                                    <div class="location-input-wrapper">
                                        <select name="items[{{$loop->index}}][dropoff_type]" class="dropoff_type">
                                            <option value="hospital" {{ $deliveryItem->hospital ? 'selected' : '' }}>Hospital</option>
                                            <option value="address"  {{ !$deliveryItem->hospital ? 'selected' : '' }} >Address</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group type_hospital">
                                    <div class="add-more-container">
                                        <label>Hospital <span class="astrik">*</span></label>
                                        <button type="button" class="btn-add-type" onclick="addHospital(this)" title="Add Hospital">
                                                <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="">
                                        <input type="text" name="items[{{$loop->index}}][search_hospital]" value="{{ optional($deliveryItem)->hospital->name ?? '' }}" class="search-hospital" placeholder="Search Hospital" autocomplete="off" {{ !$deliveryItem->hospital ? 'disabled' : '' }}>
                                        <input type="hidden" name="items[{{$loop->index}}][hospital_id]" class="hospital-id" value="{{ optional($deliveryItem)->hospital->id ?? '' }}">
                                        <div class="hospital-autocomplete-results"></div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="form-row three-cols type_address" style="{{ $deliveryItem->hospital ? 'display: none;' : '' }}">
                                <div class="form-group">
                                    <label>Drop Off Address <span class="astrik">*</span></label>
                                    <div class="location-input-wrapper">
                                        <i class="fas fa-map-marker-alt location-icon"></i>
                                        <input type="text"
                                            name="items[{{$loop->index}}][dropoff_address]"
                                            class="location-search delivery-location"
                                            placeholder="Search dropoff address..."
                                            autocomplete="off"
                                            data-item-index="0"
                                            value="{{ old('items.' . $loop->index . 'dropoff_address', optional($deliveryItem)->dropoff_address ?? '') }}"
                                            required>
                                        <input type="hidden" name="items[{{$loop->index}}][dropoff_latitude]" value="{{ old('items.' . $loop->index . 'dropoff_latitude', optional($deliveryItem)->dropoff_latitude ?? '') }}" class="delivery-lat">
                                        <input type="hidden" name="items[{{$loop->index}}][dropoff_longitude]" value="{{ old('items.' . $loop->index . 'dropoff_longitude', optional($deliveryItem)->dropoff_longitude ?? '') }}" class="delivery-lng">
                                        <input type="hidden" name="items[{{$loop->index}}][dropoff_zipcode]" value="{{ old('items.' . $loop->index . 'dropoff_zipcode', optional($deliveryItem)->dropoff_zip ?? '') }}" class="delivery-zipcode">
                                        <input type="hidden" name="items[{{$loop->index}}][dropoff_city]" value="{{ old('items.' . $loop->index . 'dropoff_city', optional($deliveryItem)->dropoff_city ?? '') }}" class="delivery-city">
                                        <input type="hidden" name="items[{{$loop->index}}][dropoff_state]" value="{{ old('items.' . $loop->index . 'dropoff_state', optional($deliveryItem)->dropoff_state ?? '') }}" class="delivery-state">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Drop Off Phone <span class="astrik">*</span></label>
                                        <input type="text"
                                            name="items[{{$loop->index}}][dropoff_phone]"
                                            placeholder="Search dropoff phone..." class="validatePhone numbers-only invalid-phone"
                                            value="{{ old('items.' . $loop->index . 'dropoff_phone', optional($deliveryItem)->dropoff_phone ?? '') }}"
                                            required>
                                </div>
                                <div class="form-group">
                                    <label>Drop Off Contact Person</label>
                                        <input type="text"
                                            name="items[{{$loop->index}}][dropoff_contact_person]"
                                            placeholder="Search dropoff contact person..."
                                            value="{{ old('items.' . $loop->index . 'dropoff_contact_person', optional($deliveryItem)->dropoff_contact_person ?? '') }}"
                                            required>
                                </div>
                            </div>

                            <div class="form-row two-cols">
                                
                                <div class="form-group">
                                    <label>Near By Landmark</label>
                                        <input type="text"
                                            name="items[{{$loop->index}}][dropoff_location]"
                                            placeholder="Near By Landmark..."
                                            value="{{ old('items.' . $loop->index . 'dropoff_location', optional($deliveryItem)->dropoff_name ?? $deliveryItem->dropoff_name ?? '') }}"
                                            required>
                                </div>
                                <div class="form-group">
                                    <div class="add-more-container">
                                        <label>Specimen Type <span class="astrik">*</span></label>
                                        <button type="button" class="btn-add-type" onclick="addSpecimenType(this)" title="Add More Type">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <select name="items[{{$loop->index}}][specimen_type]" required>
                                        <option value="">Select</option>

                                        @foreach($specimenTypes as $specimenType)
                                            <option value="{{ $specimenType->id }}" @selected(old('items.0.specimen_type', optional($deliveryItem)->specimen_type ?? '') == $specimenType->id)>
                                                {{ $specimenType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                            </div>
                            <div class="form-row two-cols">
                                
                                <div class="form-group">
                                    <label>Specimen Id <span class="astrik">*</span></label>
                                    <input type="text" name="items[{{$loop->index}}][specimen_id]" value="{{ old('items.' . $loop->index . 'specimen_id', optional($deliveryItem)->item_code ?? '') }}" placeholder="Specimen ID">
                                </div>
                                <div class="form-group">
                                    <div class="add-more-container">
                                        <label>Temperature <span class="astrik">*</span></label>
                                        <button type="button" class="btn-add-type" onclick="addTemperatureRequirement(this)" title="Add More Type">
                                                <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="">
                                        <select name="items[{{$loop->index}}][temperature_requirement]">
                                            <option value="">Select</option>
                                            @foreach($temperatureRequirements as $temperatureRequirement)
                                                <option value="{{ $temperatureRequirement->id }}" @selected(old('items.0.temperature_requirement', optional($deliveryItem)->temperature_requirement ?? '') == $temperatureRequirement->id)>
                                                    {{ $temperatureRequirement->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row two-cols">
                                
                                <div class="form-group">
                                    <label>Description</label>
                                        <textarea name="items[{{$loop->index}}][description]" placeholder="Enter description...">{{ old('items.0.description', optional($deliveryItem)->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            

            <!-- 5. Verification Requirements -->
            <div class="form-section">
                <h3>5. Verification Requirements</h3>
                <div class="form-row two-cols">
                    <div class="form-group">
                        <label>Proof of Pickup</label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="pickup_signature" name="requires_pickup_signature" value="signature" @checked(old('requires_pickup_signature', (bool) optional($delivery)->requires_pickup_signature))>
                                <label for="pickup_signature">Recipient Signature</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="pickup_photo" name="requires_pickup_photo" value="photo_of_seal" @checked(old('requires_pickup_photo', (bool) optional($delivery)->requires_pickup_photo))>
                                <label for="pickup_photo">Photo of Seal</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="pickup_barcode" name="requires_pickup_barcode_scan" value="barcode_scan" @checked(old('requires_pickup_barcode_scan', (bool) optional($delivery)->requires_pickup_barcode_scan))>
                                <label for="pickup_barcode">Barcode Scan</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Proof of Delivery</label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="delivery_recipient_scan" name="requires_recepient_id_scan" value="recipient_id_scan" @checked(old('requires_recepient_id_scan', (bool) optional($delivery)->requires_recepient_id_scan))>
                                <label for="delivery_recipient_scan">Recipient ID Scan</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="delivery_signature" name="requires_dropoff_signature" value="digital_signature" @checked(old('requires_dropoff_signature', (bool) optional($delivery)->requires_dropoff_signature))>
                                <label for="delivery_signature">Digital Signature</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="delivery_barcode" name="requires_dropoff_barcode_scan" value="barcode_scan" @checked(old('requires_dropoff_barcode_scan', (bool) optional($delivery)->requires_dropoff_barcode_scan))>
                                <label for="delivery_barcode">Barcode Scan</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="delivery_photo_seal" name="requires_dropoff_photo" value="photo_of_seal" @checked(old('requires_dropoff_photo', (bool) optional($delivery)->requires_dropoff_photo))>
                                <label for="delivery_photo_seal">Photo of Seal</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-submit" id="submitBtn">Dispatch Job</button>
        </div>
    </form>
</div>


<!-- Assign Driver Modal -->
<div class="specimen_type_modal" id="specimenTypeModal">
    <div class="specimen_type_modal-content">
        <div class="specimen_type_modal-header">
            <h3>Add Specimen Type</h3>
        </div>
        <div class="specimen_type_modal-body">
            <div class="type-search">
                <input type="text" id="specimenTypeText" placeholder="Add specimen type">
            </div>
            <div id="driversList"></div>
        </div>
        <div class="specimen_type_modal-footer">
            <button class="btn-modal btn-modal-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-modal btn-modal-assign" id="btnAddSpecimenType" onclick="confirmAddSpecimenType(0)">Add Specimen Type</button>
        </div>
    </div>
</div>

<!-- Assign Temperature Modal -->
<div class="specimen_type_modal" id="addTemperatureRequirementModal">
    <div class="specimen_type_modal-content">
        <div class="specimen_type_modal-header">
            <h3>Add Temperature Requirement</h3>
        </div>
        <div class="specimen_type_modal-body">
            <div class="type-search">
                <input type="text" id="temperatureRequirementText" placeholder="Add Temperature Requirement">
            </div>
            <div id="driversList"></div>
        </div>
        <div class="specimen_type_modal-footer">
            <button class="btn-modal btn-modal-cancel" onclick="closeModalTemperatureRequirement()">Cancel</button>
            <button class="btn-modal btn-modal-assign" id="btnAddTemperatureRequirement" onclick="confirmAddTemperatureRequirement(0)">Add Temperature Requirement</button>
        </div>
    </div>
</div>

<!-- Assign Hopital Modal -->
<div class="specimen_type_modal" id="addHospitalModal">
    <div class="specimen_type_modal-content">
            <div class="specimen_type_modal-header">
                <h3>Add Hopital</h3>
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
                <button class="btn-modal btn-modal-assign" id="btnAddHospital" onclick="confirmAddHospital(0)" type="submit">Add Hospital</button>
            </div>
            </form>
    </div>
</div>
<div class="specimen_type_modal" id="addVehicleRequirementModal">
    <div class="specimen_type_modal-content">
        <div class="specimen_type_modal-header">
            <h3>Add Vehicle Requirement</h3>
        </div>
        <div class="specimen_type_modal-body">
            <div class="type-search">
                <input type="text" id="vehicleRequirementText" placeholder="Add Vehicle Requirement">
            </div>
            <div id="driversList"></div>
        </div>
        <div class="specimen_type_modal-footer">
            <button class="btn-modal btn-modal-cancel" onclick="closeModalVehicleRequirement()">Cancel</button>
            <button class="btn-modal btn-modal-assign" id="btnAddVehicleRequirement" onclick="confirmAddVehicleRequirement(0)">Add Vehicle Requirement</button>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    // const token = localStorage.getItem('api_token');
    const token = '{{ session("web_token") }}';
    let itemIndex = 1;
    let availableDrivers = [];
    let autocompleteInstances = [];
    const specimenTypeOptionsData = @json($specimenTypes->map(function ($specimenType) {
        return ['id' => $specimenType->id, 'name' => $specimenType->name];
    })->values());
    const temperatureRequirementOptionsData = @json($temperatureRequirements->map(function ($temperatureRequirement) {
        return ['id' => $temperatureRequirement->id, 'name' => $temperatureRequirement->name];
    })->values());

    function getSpecimenTypeOptions() {
        return specimenTypeOptionsData
            .map(specimenType => `<option value="${specimenType.id}">${specimenType.name}</option>`)
            .join('');
    }

    function getTemperatureRequirementOptions() {
        return temperatureRequirementOptionsData
            .map(temperatureRequirement => `<option value="${temperatureRequirement.id}">${temperatureRequirement.name}</option>`)
            .join('');
    }

    function getAddressComponent(place, type, useShortName = false) {
        const component = place.address_components?.find(c =>
            c.types.includes(type)
        );

        if (!component) return '';

        return useShortName ? component.short_name : component.long_name;
    }
    // Initialize Google Places Autocomplete
    function initAutocomplete() {
        //alert('Google Maps API Loaded - Initializing Autocomplete');
        // Initialize pickup location autocomplete
        const pickupInput = document.getElementById('pickupLocation');
        if (pickupInput) {
            const pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput, {
                types: ['address'],
                componentRestrictions: { country: ['us', 'in'] } // Change to your country code
            });

            pickupAutocomplete.addListener('place_changed', function() {
                const place = pickupAutocomplete.getPlace();
                console.log(place);
                if (place.geometry) {
                    document.getElementById('pickupLat').value = place.geometry.location.lat();
                    document.getElementById('pickupLng').value = place.geometry.location.lng();
                    pickupInput.value = place.formatted_address;
                    const city = getAddressComponent(place, 'locality');
                    const state = getAddressComponent(place, 'administrative_area_level_1');
                    const stateCode = getAddressComponent(place, 'administrative_area_level_1', true);
                    const zipCode = getAddressComponent(place, 'postal_code');
                    document.getElementById('pickupCity').value = city;
                    document.getElementById('pickupZipCode').value = zipCode;
                    document.getElementById('pickupState').value = state;
                }
            });
        }

        // Initialize delivery location autocomplete for existing inputs
        initDeliveryAutocomplete();
        initHospitalAutocomplete();
        initHospitalSearch();
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
    // Initialize autocomplete for delivery locations
    function initDeliveryAutocomplete() {
        const deliveryInputs = document.querySelectorAll('.delivery-location');
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
                    const wrapper = input.closest('.location-input-wrapper');
                    wrapper.querySelector('.delivery-lat').value = place.geometry.location.lat();
                    wrapper.querySelector('.delivery-lng').value = place.geometry.location.lng();
                    
                    const city = getAddressComponent(place, 'locality');
                    const state = getAddressComponent(place, 'administrative_area_level_1');
                    const stateCode = getAddressComponent(place, 'administrative_area_level_1', true);
                    const zipCode = getAddressComponent(place, 'postal_code');
                    wrapper.querySelector('.delivery-city').value = city;
                    wrapper.querySelector('.delivery-zipcode').value = zipCode;
                    wrapper.querySelector('.delivery-state').value = state;
                }
            });

            input.dataset.autocompleteInitialized = 'true';
            autocompleteInstances.push(autocomplete);
        });
    }

    // Load available drivers
    async function loadDrivers() {
        try {
            const response = await fetch('/api/v1/driver-profiles', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.data && data.data.profiles) {
                availableDrivers = data.data.profiles;

                const driverSelect = document.getElementById('driverId');
                const selectedDriverId = '{{ old('driver_id', optional($delivery)->driver_id ?? '') }}';
                availableDrivers.forEach(profile => {
                    const option = document.createElement('option');
                    option.value = profile.user.id;
                    option.textContent = `${profile.user.name} - ${profile.vehicle_type || 'Standard'}`;
                    if (selectedDriverId && String(profile.user.id) === String(selectedDriverId)) {
                        option.selected = true;
                    }
                    driverSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading drivers:', error);
        }
    }

    //Add Speciment Type
    function addSpecimenType(thisButton){
        // $('#specimenTypeModal').show(); 
        var index = $(thisButton).parents('.item-card').attr('data-item-index');
        $('#btnAddSpecimenType').attr('onclick', 'confirmAddSpecimenType(' + index + ')');
        document.getElementById('specimenTypeModal').classList.add('show');  
    }
    function closeModal(){
        document.getElementById('specimenTypeModal').classList.remove('show');
    }
    async function confirmAddSpecimenType(item_no){
        var specimenTypeText = document.getElementById('specimenTypeText').value;
        if(specimenTypeText.trim() == ''){
            return false;
        }
        const jobData = {
            name: document.getElementById('specimenTypeText').value,
            status: '1'
        };   
        const response = await fetch('/api/v1/add-specimen-type', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(jobData)
            });
        const result = await response.json();
        if (result.success) {
            $('select[name="items[' + item_no + '][specimen_type]"]').append('<option value="' + result.data.id + '" selected>' + result.data.name + '</option>');
            closeModal();
        } 
    }

    //Add Temperature Requirement
    function addTemperatureRequirement(thisButton){
        // $('#specimenTypeModal').show(); 
        var index = $(thisButton).parents('.item-card').attr('data-item-index');
        $('#btnAddTemperatureRequirement').attr('onclick', 'confirmAddTemperatureRequirement(' + index + ')');
        document.getElementById('addTemperatureRequirementModal').classList.add('show');  
    }
    //Add Hospital
    function addHospital(thisButton){
        var index = $(thisButton).parents('.item-card').attr('data-item-index');
        $('#btnAddHospital').attr('onclick', 'confirmAddHospital(' + index + ')');
        document.getElementById('addHospitalModal').classList.add('show');  
    }

    function closeModalHospital(){
        document.getElementById('addHospitalModal').classList.remove('show');
    }

    function closeModalTemperatureRequirement(){
        document.getElementById('addTemperatureRequirementModal').classList.remove('show');
    }
    async function confirmAddTemperatureRequirement(item_no){
        var temperatureRequirementText = document.getElementById('temperatureRequirementText').value;
        if(temperatureRequirementText.trim() == ''){
            return false;
        }
        const jobData = {
            name: document.getElementById('temperatureRequirementText').value,
            status: '1'
        };   
        const response = await fetch('/api/v1/add-temperature-requirement', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(jobData)
            });
        const result = await response.json();
        if (result.success) {
            $('select[name="items[' + item_no + '][temperature_requirement]"]').append('<option value="' + result.data.id + '" selected>' + result.data.name + '</option>');
            closeModalTemperatureRequirement();
        } 
    }


    //Add Vehicle Requirement
    function addVehicleRequirement(){
        // $('#specimenTypeModal').show(); 
        document.getElementById('addVehicleRequirementModal').classList.add('show');  
    }
    function closeModalVehicleRequirement(){
        document.getElementById('addVehicleRequirementModal').classList.remove('show');
    }
    async function confirmAddVehicleRequirement(item_no){
        var vehicleRequirementText = document.getElementById('vehicleRequirementText').value;
        if(vehicleRequirementText.trim() == ''){
            return false;
        }
        const jobData = {
            name: document.getElementById('vehicleRequirementText').value,
            status: '1'
        };   
        const response = await fetch('/api/v1/add-vehicle-requirement', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(jobData)
            });
        const result = await response.json();
        if (result.success) {
            $('select[name="required_vehicle_type"]').append('<option value="' + result.data.id + '" selected>' + result.data.name + '</option>');
            closeModalVehicleRequirement();
        } 
    }
    
    // Add new item entry
    function addItem() {
        if(itemIndex == '100'){
            showDialog('You cannot add more than 100 items.', 'error');
            return false;
        }
        const itemsList = document.getElementById('itemsList');
        const itemCard = document.createElement('div');
        itemCard.className = 'item-card';
        itemCard.setAttribute('data-item-index', itemIndex);
        itemCard.innerHTML = `
            <button type="button" class="item-remove" onclick="removeItem(this)">
                <i class="fas fa-times"></i>
            </button>
            <div class="form-row two-cols">
                <div class="form-group">
                    <label>Item ${itemIndex + 1} Name <span class="astrik">*</span></label>
                    <input type="text" name="items[${itemIndex}][item_name]" placeholder="Please enter item name">
                </div>
                <div class="form-group">
                    <label>Handling Instructions</label>
                    <input type="text" name="items[${itemIndex}][handling_instructions]" placeholder="Please enter handling instructions" maxlength="200">
                </div>
            </div>
            <div class="form-row two-cols">
                <div class="form-group">
                    <label>Drop Off Type <span class="astrik">*</span></label>
                    <div class="location-input-wrapper">
                        <select name="items[${itemIndex}][dropoff_type]" class="dropoff_type">
                            <option value="hospital">Hospital</option>
                            <option value="address">Address</option>
                        </select>
                    </div>
                </div>
                <div class="form-group type_hospital">
                    <div class="add-more-container">
                        <label>Hospital <span class="astrik">*</span></label>
                        <button type="button" class="btn-add-type" onclick="addHospital(this)" title="Add Hospital">
                                <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="">
                        <input type="text" name="items[${itemIndex}][search_hospital]" class="search-hospital" placeholder="Search Hospital" autocomplete="off">
                        <input type="hidden" name="items[${itemIndex}][hospital_id]" class="hospital-id">
                        <div class="hospital-autocomplete-results"></div>
                    </div>
                </div>
                
            </div>
            <div class="form-row three-cols type_address" style="display: none;">
                <div class="form-group" >
                    <label>Drop Off Address</label>
                    <div class="location-input-wrapper">
                        <i class="fas fa-map-marker-alt location-icon"></i>
                        <input type="text" name="items[${itemIndex}][dropoff_address]" class="location-search delivery-location pac-target-input" placeholder="Search dropoff address..." autocomplete="off" data-item-index="${itemIndex}" required="">
                        <input type="hidden" name="items[${itemIndex}][dropoff_latitude]" value="" class="delivery-lat">
                        <input type="hidden" name="items[${itemIndex}][dropoff_longitude]" value="" class="delivery-lng">
                        <input type="hidden" name="items[${itemIndex}][dropoff_zipcode]" value="" class="delivery-zipcode">
                        <input type="hidden" name="items[${itemIndex}][dropoff_city]" value="" class="delivery-city">
                        <input type="hidden" name="items[${itemIndex}][dropoff_state]" value="" class="delivery-state">
                    </div>
                </div>
                <div class="form-group">
                    <label>Drop Off Phone</label>
                    <input type="text" maxlength="15" name="items[${itemIndex}][dropoff_phone]" class="validatePhone numbers-only" placeholder="Please enter dropoff phone" maxlength="15" required="">
                </div>
                <div class="form-group">
                    <label>Drop Off Contact Person</label>
                    <input type="text" name="items[${itemIndex}][dropoff_contact_person]" placeholder="Please enter contact person" maxlength="15" required="">
                </div>
            </div>
            <div class="form-row two-cols">
                <div class="form-group">
                    <label>Near By Landmark <span class="astrik">*</span></label>
                    <input type="text" name="items[${itemIndex}][dropoff_location]" placeholder="Please enter dropoff location" maxlength="100" required="">
                </div>
                <div class="form-group">
                    <div class="add-more-container">
                        <label>Specimen Type <span class="astrik">*</span></label>
                        <button type="button" class="btn-add-type" onclick="addSpecimenType(this)" title="Add More Type">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <select name="items[${itemIndex}][specimen_type]" required>
                        <option value="">Select</option>
                        ${getSpecimenTypeOptions()}
                    </select>
                </div>
            </div>
            <div class="form-row two-cols">
                <div class="form-group">
                    <label>Specimen Id <span class="astrik">*</span></label>
                    <input type="text" name="items[${itemIndex}][specimen_id]" placeholder="Please enter specimen ID" maxlength="30">
                </div>
                <div class="form-group">
                    <div class="add-more-container">
                        <label>Temperature <span class="astrik">*</span></label>
                        <button type="button" class="btn-add-type" onclick="addTemperatureRequirement(this)" title="Add More Type">
                                <i class="fas fa-plus"></i>
                        </button>
                    </div>
                        <select name="items[${itemIndex}][temperature_requirement]">
                            <option value="">Select</option>
                            ${getTemperatureRequirementOptions()}
                        </select>
                </div>
            </div>
            <div class="form-row two-cols">
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="items[${itemIndex}][description]" placeholder="Please enter description"></textarea>
                </div>
            </div>
        `;
        itemsList.appendChild(itemCard);
        itemIndex++;
        initHospitalSearch();
        // Initialize autocomplete for the new delivery location input
        if (typeof google !== 'undefined') {
            initDeliveryAutocomplete();
        }
        validateForm();
    }

    // Remove item entry
    function removeItem(button) {
        const itemCard = button.closest('.item-card');
        const itemsList = document.getElementById('itemsList');

        if (itemsList.children.length > 1) {
            itemCard.remove();
        } else {
            showDialog('At least one item is required', 'warning');
        }
    }

    // Handle form submission
    document.getElementById('jobEditForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        show_load_spinner();
        if (document.querySelector('.just-validate-error-label')) {
            hide_load_spinner();
            return false;
        }
        setTimeout(function(){
            if (document.querySelector('.just-validate-error-label')) {
                hide_load_spinner();
                return false;
            }
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Dispatching...';

            const formData = new FormData(e.target);

            // Get pickup location from address search
            const pickupAddress = formData.get('pickup_location');
            const pickupLat = parseFloat(formData.get('pickup_latitude')) || null;
            const pickupLng = parseFloat(formData.get('pickup_longitude')) || null;
            const pickupZipCode = formData.get('pickupZipCode');
            const pickupCity = formData.get('pickupCity');
            const pickupState = formData.get('pickupState');
            const pickup_phone = formData.get('pickup_phone');
            const container_count = formData.get('number_of_containers');
            

            // Collect items with their delivery locations
            const items = [];
            const itemCards = document.querySelectorAll('.item-card');

            // Use the first item's delivery location as the primary delivery location
            let primaryDeliveryAddress = formData.get('items[0][dropoff_address]') || '';
            let primaryDeliveryLocation = formData.get('items[0][dropoff_location]') || '';
            let primaryDeliveryPhone = formData.get('items[0][dropoff_phone]') || '';
            let primaryDeliveryItemName = formData.get('items[0][item_name]') || '';
            let primaryDeliveryLat = parseFloat(formData.get('items[0][dropoff_latitude]')) || null;
            let primaryDeliveryLng = parseFloat(formData.get('items[0][dropoff_longitude]')) || null;
            let primaryDeliveryCity = formData.get('items[0][dropoff_city]') || null;
            let primaryDeliveryState = formData.get('items[0][dropoff_state]') || null;
            let primaryDeliveryZipCode = formData.get('items[0][dropoff_zipcode]') || null;

            itemCards.forEach((card, idx) => {

                const specimenId = formData.get(`items[${idx}][specimen_id]`);
                // items.push({
                //     item_type: 'specimen',
                //     item_name: formData.get(`items[${idx}][item_name]`),
                //     specimen_type: formData.get(`items[${idx}][specimen_type]`) || 'other',
                //     specimen_id: specimenId,
                //     barcode: specimenId,
                //     quantity: parseInt(formData.get('number_of_containers') || 1),
                //     description: formData.get(`items[${idx}][item_name]`),
                //     temperature_requirement: formData.get('temperature_requirement'),
                // });
                items.push({
                    item_type: 'specimen',
                    specimen_type: formData.get(`items[${idx}][specimen_type]`) || 'other',
                    temperature_requirement: formData.get(`items[${idx}][temperature_requirement]`),
                    item_name: formData.get(`items[${idx}][item_name]`),
                    handling_instructions: formData.get(`items[${idx}][handling_instructions]`),
                    item_code: specimenId,
                    hospital_id: formData.get(`items[${idx}][hospital_id]`) || null,
                    search_hospital: formData.get(`items[${idx}][search_hospital]`) || null,
                    dropoff_phone: formData.get(`items[${idx}][dropoff_phone]`),
                    description: formData.get(`items[${idx}][description]`),
                    dropoff_contact_person: formData.get(`items[${idx}][dropoff_contact_person]`),
                    dropoff_name: formData.get(`items[${idx}][dropoff_location]`),
                    dropoff_address: formData.get(`items[${idx}][dropoff_address]`),
                    dropoff_city: formData.get(`items[${idx}][dropoff_city]`),
                    dropoff_state: formData.get(`items[${idx}][dropoff_state]`),
                    dropoff_zip: formData.get(`items[${idx}][dropoff_zipcode]`),
                    dropoff_latitude: parseFloat(formData.get(`items[${idx}][dropoff_latitude]`)) || null,
                    dropoff_longitude: parseFloat(formData.get(`items[${idx}][dropoff_longitude]`)) || null,    
                });
            });

            // Get verification requirements
            const pickupProof = formData.getAll('pickup_proof[]');
            const deliveryProof = formData.getAll('delivery_proof[]');

            const jobData = {
                job_title: formData.get('job_title'),
                specimen_id: items[0]?.specimen_id || `SPEC-${Date.now()}`,
                patient_initials: 'N/A',
                urgency_level: formData.get('urgency_level') || 'routine',

                // Pickup Information
                pickup_name: formData.get('job_title'),
                pickup_address: pickupAddress || '',
                pickup_city: pickupCity,
                pickup_state: pickupState,
                pickup_zip: pickupZipCode,
                pickup_phone: pickup_phone,
                pickup_latitude: pickupLat,
                pickup_longitude: pickupLng,
                container_count: container_count,
                temperature_requirement: formData.get('temperature_requirement'),
                // pickup_latitude: '30.702312',
                // pickup_longitude: '76.699728',

                // Time Window
                scheduled_time_window_start: formData.get('scheduled_time_window_start'),
                scheduled_time_window_end: formData.get('scheduled_time_window_end'),

                // Delivery Information
                // delivery_name: primaryDeliveryItemName,
                // delivery_address: primaryDeliveryAddress || '',
                // delivery_city: primaryDeliveryCity,
                // delivery_state: primaryDeliveryState,
                // delivery_zip: primaryDeliveryZipCode,
                // delivery_phone: primaryDeliveryPhone,
                // // delivery_latitude: '28.644800',
                // // delivery_longitude: '77.216721',
                // delivery_latitude: primaryDeliveryLat,
                // delivery_longitude: primaryDeliveryLng,

                priority: document.getElementById('priority').value || 'normal',
                driver_id: formData.get('driver_id') || null,
                required_vehicle_type: formData.get('required_vehicle_type') || null,
                special_instructions: formData.get('special_instructions'),
                notes: formData.get('job_title') ? `Job Title: ${formData.get('job_title')}` : null,

                // Digital Chain of Custody
                requires_pickup_signature: document.getElementById('pickup_signature').checked ? 1 : 0,
                requires_pickup_photo: document.getElementById('pickup_photo').checked ? 1 : 0,
                requires_pickup_barcode_scan: document.getElementById('pickup_barcode').checked ? 1 : 0,
                requires_recepient_id_scan: document.getElementById('delivery_recipient_scan').checked ? 1 : 0,
                requires_dropoff_signature: document.getElementById('delivery_signature').checked ? 1 : 0,
                requires_dropoff_barcode_scan: document.getElementById('delivery_barcode').checked ? 1 : 0,
                requires_dropoff_photo: document.getElementById('delivery_photo_seal').checked ? 1 : 0, 
                items: items
            };

            console.log('Job Data:', jobData);

            try {
                var delivery_id = '{{$delivery_id}}';
                $.ajax({
                    url: '/api/v1/edit-delivery/'+delivery_id,
                    type: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    contentType: 'application/json',
                    data: JSON.stringify(jobData),

                    success: function (result) {
                        hide_load_spinner();
                        if (result.success) {
                            showDialog('Job updated successfully!', 'success', 'Success', function() {
                                window.location.href = '/company/dashboard/deliveries';
                            });
                        } 
                        
                    },
                    error: function (result) {
                        hide_load_spinner();
                        if (result.responseJSON.errors) {
                            let html = '<ol>';

                            result.responseJSON.errors.forEach(errorMsg => {
                                html += `<li>${errorMsg}</li>`;
                            });

                            html += '</ol>';
                            showDialog('<b>Failed to create job:</b> ' + html, 'error');   
                            // const errorMessages = Object.values(result.errors).flat().join('\n');
                            // showDialog('Failed to create job: ' + errorMessages, 'error');
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Dispatch Job';
                        }
                        else {
                            showDialog('Failed to create job: ' + (result.responseJSON.message || JSON.stringify(result.responseJSON.errors || 'Unknown error')), 'error');
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Dispatch Job';
                        }
                    }
                });
            }
            // try {
            //     show_load_spinner('content','Editing Job...','class');
            //     var delivery_id = '{{$delivery_id}}';
            //     const response = await fetch('/api/v1/edit-delivery/'+delivery_id, {
            //         method: 'POST',
            //         headers: {
            //             'Authorization': `Bearer ${token}`,
            //             'Content-Type': 'application/json',
            //             'Accept': 'application/json'
            //         },
            //         body: JSON.stringify(jobData)
            //     });

            //     const result = await response.json();

            //     if (result.success) {
            //         hide_load_spinner('content','class');
            //         showDialog('Job Edited successfully!', 'success', 'Success', function() {
            //             window.location.href = '/company/dashboard/deliveries';
            //         });
            //     } 
            //     else if (result.errors) {
            //         const errorMessages = Object.values(result.errors).flat().join(' ');
            //         let html = '<ol>';
            //         result.errors.forEach(errorMsg => {
            //             html += `<li>${errorMsg}</li>`;
            //         });
            //         html += '</ol>';
            //         showDialog('<b>Failed to update job:</b> ' + html, 'error');   
            //         submitBtn.disabled = false;
            //         submitBtn.textContent = 'Dispatch Job';
            //         hide_load_spinner('content','class');
                    
                    
            //     }
            //     else {
            //         showDialog('Failed to update job: ' + (result.message || JSON.stringify(result.errors || 'Unknown error')), 'error');
            //         submitBtn.disabled = false;
            //         submitBtn.textContent = 'Dispatch Job';
            //         hide_load_spinner('content','class');
            //     }
            // } 
            catch (error) {
                showDialog('Error updating job: ' + error.message, 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Dispatch Job';
                console.error(error);
                hide_load_spinner('content','class');
            }
        },500);
    });

    // Load data on page load
    // Google Maps API will call initAutocomplete() when ready
    loadDrivers();

    $("body").on("keyup", ".validatePhone", function() {
        const phone = $(this).val();

        // USA phone validation regex
        const regex = /^(\+1\s?)?(\([0-9]{3}\)|[0-9]{3})[-.\s]?[0-9]{3}[-.\s]?[0-9]{4}$/;
        if (!regex.test(phone)) {
            $(this).addClass('invalid-phone');
        } else {
            $(this).removeClass('invalid-phone');
        }
    });

    const dateTimeInput = document.getElementById('timeWindowEnd');

    dateTimeInput.addEventListener('click', function () {
        if (this.showPicker) {
            this.showPicker();
        }
    });
    const timeWindowStart = document.getElementById('timeWindowStart');
    timeWindowStart.addEventListener('click', function () {
        if (this.showPicker) {
            this.showPicker();
        }
    });
</script>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKwk2TS_ydCfzq7vx4SZnewwW9hX_YidQ&libraries=places&callback=initAutocomplete" async defer></script>
@endsection