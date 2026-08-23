@extends('common.layout')
@section('title', 'Add Vehicle Requirement')
@section('page-title', 'Add Vehicle Requirement')
@section('content')
<div class="profile-container">
    <div class="profile-section">
        <form action="{{ route('vehicle-requirement.store') }}" method="POST">
            @csrf

            @include('company/vehicle-requirement._form')

            <div class="profile-actions">
                <button type="submit" class="btn-create">
                    Create
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