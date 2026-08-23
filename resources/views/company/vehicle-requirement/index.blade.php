@extends('common.layout')

@section('title', 'Vehicle Requirement')
@section('page-title', 'Vehicle Requirement')

@section('styles')
@endsection

@section('content')

<!-- Data Table -->
<div class="data-card">
    <div class="data-card-header">
        <h3>All Vehicle Requirements</h3>
        <a href="{{ route('vehicle-requirement.create') }}" class="btn-create">
            <i class="fas fa-plus"></i> Add Vehicle Requirement
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
                @if($VehicleRequirements)
                    @foreach($VehicleRequirements as $VehicleRequirement)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $VehicleRequirement->name }}</td>
                            <td>
                                @if($VehicleRequirement->status == '1')
                                    <span class="badge active">Active</span>
                                @elseif($VehicleRequirement->status == '0')
                                    <span class="badge inactive">In-Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="window.location.href='{{ route('vehicle-requirement.edit', $VehicleRequirement->id) }}'">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <form id="delete-form-{{ $VehicleRequirement->id }}"
                                        action="{{ route('vehicle-requirement.destroy', $VehicleRequirement) }}"
                                        method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button class="btn-action btn-delete" onclick="confirmDeleteVehicleRequirement('{{ $VehicleRequirement->id }}')" title="Decline Enquiry">
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
{{ $VehicleRequirements->links() }}
@endsection