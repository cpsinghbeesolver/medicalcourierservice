@extends('common.layout')

@section('title', 'Temperature Requirement')
@section('page-title', 'Temperature Requirement')

@section('styles')
@endsection

@section('content')

<!-- Data Table -->
<div class="data-card">
    <div class="data-card-header">
        <h3>All Temperature Requirements</h3>
        <a href="{{ route('temperature-requirement.create') }}" class="btn-create">
            <i class="fas fa-plus"></i> Add Temperature Requirement
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
                @if($TemperatureRequirements)
                    @foreach($TemperatureRequirements as $TemperatureRequirement)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $TemperatureRequirement->name }}</td>
                            <td>
                                @if($TemperatureRequirement->status == '1')
                                    <span class="badge active">Active</span>
                                @elseif($TemperatureRequirement->status == '0')
                                    <span class="badge inactive">In-Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" onclick="window.location.href='{{ route('temperature-requirement.edit', $TemperatureRequirement->id) }}'">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <form id="delete-form-{{ $TemperatureRequirement->id }}"
                                        action="{{ route('temperature-requirement.destroy', $TemperatureRequirement) }}"
                                        method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button class="btn-action btn-delete" onclick="confirmDeleteTemperatureRequirement('{{ $TemperatureRequirement->id }}')" title="Decline Enquiry">
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
{{ $TemperatureRequirements->links() }}
@endsection