@extends('common.layout')
@section('title', 'Edit Vehicle Requirement')
@section('page-title', 'Edit Vehicle Requirement')
@section('content')
<div class="profile-container">
    <div class="profile-section">
        <form action="{{ route('vehicle-requirement.update', $VehicleRequirement) }}" id="add_vehicle_requirement" method="POST">
            @csrf
            @method('PUT')

            @include('company/vehicle-requirement._form')

            <div class="profile-actions">
                <button type="submit" class="btn-create">
                    Update
                </button>

                <a href="{{ route('vehicle-requirement.index') }}" class="btn-cancel">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
    // const token = localStorage.getItem('api_token');

</script>
@endsection