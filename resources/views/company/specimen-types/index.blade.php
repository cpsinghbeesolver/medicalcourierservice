@extends('common.layout')

@section('title', 'Specimen Types')
@section('page-title', 'Specimen Types')

@section('styles')
@endsection

@section('content')

<!-- Data Table -->
<div class="data-card">
    <div class="data-card-header">
        <h3>All Specimen Types</h3>
        <a href="{{ route('specimen-types.create') }}" class="btn-create">
            <i class="fas fa-plus"></i> Add Specimen Type
        </a>
    </div>
    <div class="table-container">
        <table class="data-table" id="driversTable">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($specimenTypes)
                    @foreach($specimenTypes as $specimenType)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $specimenType->name }}</td>
                            <td>
                                @if($specimenType->status == '1')
                                    <span class="badge active">Active</span>
                                @elseif($specimenType->status == '0')
                                    <span class="badge inactive">In-Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="window.location.href='{{ route('specimen-types.edit', $specimenType->id) }}'">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <form id="delete-form-{{ $specimenType->id }}"
                                        action="{{ route('specimen-types.destroy', $specimenType) }}"
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
{{ $specimenTypes->links() }}
@endsection