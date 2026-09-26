@extends('common.layout')

@section('title', 'Hospital Management')
@section('page-title', 'Hospitals')

@section('content')

    <div class="filters-bar">
        <form method="GET" action="{{ route('dashboard.hospitals') }}" style="display:flex; gap:15px; width:100%; align-items:flex-end;">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" name="search" placeholder="Search hospitals..." value="{{ request('search') }}">
            </div>
            <div>
                <button type="submit" class="btn-action">Search</button>
            </div>
        </form>
    </div>

    <div class="data-card">
        <div class="data-card-header">
            <h3>All Hospitals</h3>
            <a href="{{ route('dashboard.hospitals.create') }}" class="btn-create">
                <i class="fas fa-plus"></i> Add New Hospital
            </a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Registration No.</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Company</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hospitals as $hospital)
                        @php
                            $pending_id = '';
                            if($hospital->pendingRequests->isNotEmpty()){
                                $pendingRequests = $hospital->pendingRequests->toArray();
                                $pending_id = $pendingRequests[0]['id'];
                            }   
                        @endphp
                        <tr data-id="{{ $pending_id }}" class="{{ $hospital->pendingRequests->isNotEmpty() ? 'table-warning' : '' }}">
                            <td>
                                {{ $hospital->name }}

                                @if ($hospital->pendingRequests->isNotEmpty())
                                    <span class="badge bg-warning text-dark ms-2 request_button">
                                        Request
                                    </span>
                                @endif
                            </td>
                            <td>{{ $hospital->registration_number }}</td>
                            <td>{{ $hospital->contact_person }}</td>
                            <td>{{ $hospital->phone }}</td>
                            <td>{{ $hospital->city ?: 'N/A' }}</td>
                            <td>{{ $hospital->state ?: 'N/A' }}</td>
                            <td>{{ $hospital->createdByUser->name ?? 'N/A' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('dashboard.hospitals.show', $hospital->id) }}" class="btn-action">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('dashboard.hospitals.edit', $hospital->id) }}" class="btn-action edit">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                    <form action="{{ route('dashboard.hospitals.destroy', $hospital->id) }}" method="POST"
                                          style="display:inline;"
                                          onsubmit="return confirm('Delete this hospital?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; color:#7f8c8d; padding:30px;">
                                No hospitals found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="specimen_type_modal" id="viewRequestAdminModal">
        <div class="specimen_type_modal-content">
                <div class="specimen_type_modal-header">
                    <h3>Hospital Request</h3>
                </div>
                <form method="POST" id="update_request_admin" action="{{route('update.request.admin')}}" style="display: contents;">
                    @csrf
                    <input type="hidden" name="hospital_id" id="hospital_id">
                    <div class="specimen_type_modal-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea id="hospital_name" maxlength="200" name="message" placeholder="Message" autocomplete="off" readonly></textarea>
                                </div>
                            </div>

                    </div>
                    <div class="specimen_type_modal-footer">
                        <button class="btn-modal btn-modal-cancel" type="button" onclick="closeModalAdmin()">Cancel</button>
                        <button class="btn-modal btn-modal-assign" id="btnAddMessage" type="submit">Mark As Complete</button>
                    </div>
                </form>
        </div>
    </div>
    {{ $hospitals->links() }}

@endsection

@section('scripts')
<script>
     var token = '{{ session("web_token") }}';
    $('.request_button').click(function(){
        var hospital_id = $(this).parents('tr').attr('data-id');
        $.ajax({
            url: '/api/v1/hospitals/'+hospital_id,
            type: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            contentType: 'application/json',
            success: function (result) {
                if(result.data.message){
                    $('#hospital_name').val(result.data.message);
                }
            },
            error: function (result) {
            }
        });
        $('form#update_request_admin #hospital_id').val(hospital_id);
        document.getElementById('viewRequestAdminModal').classList.add('show');
    });
    function closeModalAdmin(){
        document.getElementById('viewRequestAdminModal').classList.remove('show');
    }

</script>

@endsection
