@extends('common.layout')
@section('title', 'Add Specimen Type')
@section('page-title', 'Add Specimen Type')
@section('content')
<div class="profile-container">
    <div class="profile-section">
        <form action="{{ route('specimen-types.store') }}" method="POST">
            @csrf

            @include('company/specimen-types._form')

            <div class="profile-actions">
                <button type="submit" class="btn-create">
                    Create
                </button>

                <a href="{{ route('specimen-types.index') }}" class="btn-cancel">
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