@extends('common.layout')
@section('title', 'Add Temperature Requirement')
@section('page-title', 'Add Temperature Requirement')
@section('content')
<div class="profile-container">
    <div class="profile-section">
        <form action="{{ route('temperature-requirement.store') }}" id="add_temperature_requirement" method="POST">
            @csrf

            @include('company/temperature-requirement._form')

            <div class="profile-actions">
                <button type="submit" class="btn-create">
                    Create
                </button>

                <a href="{{ route('temperature-requirement.index') }}" class="btn-cancel">
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