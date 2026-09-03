@extends('common.layout')
@section('title', 'Edit Specimen Type')
@section('page-title', 'Edit Specimen Type')
@section('content')
<div class="profile-container">
    <div class="profile-section">
        <form action="{{ route('specimen-types.update', $specimenType) }}" id="add_speciment_type" method="POST">
            @csrf
            @method('PUT')

            @include('company/specimen-types._form')

            <div class="profile-actions">
                <button type="submit" class="btn-create">
                    Update
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