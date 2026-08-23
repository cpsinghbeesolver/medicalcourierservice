<div class="form-section">
    <div class="form-row">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" id="name" placeholder="Enter name" value="{{ old('name', $VehicleRequirement->name ?? '') }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="1" {{ old('status', $VehicleRequirement->status ?? 1) == 1 ? 'selected' : '' }}>
                    Active
                </option>

                <option value="0" {{ old('status', $VehicleRequirement->status ?? 1) == 0 ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>
        </div>
    </div>
</div>