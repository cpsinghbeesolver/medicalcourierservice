@extends('common.layout')
@section('title', 'Edit Temperature Requirement')
@section('page-title', 'Edit Temperature Requirement')
@section('content')
<div class="profile-container">
    <div class="profile-section">
        <form action="{{ route('temperature-requirement.update', $TemperatureRequirement) }}" method="POST">
            @csrf
            @method('PUT')

            @include('company/temperature-requirement._form')

            <div class="profile-actions">
                <button type="submit" class="btn-create">
                    Update
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