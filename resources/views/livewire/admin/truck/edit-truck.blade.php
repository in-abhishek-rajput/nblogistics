<div>
    <form wire:submit.prevent="update">
        <div class="row">
            {{-- Truck Number --}}
            <div class="col-md-12 mb-3">
                <input type="text" value="{{ $truck_number }}" wire:model="truck_number" class="form-control @error('truck_number') is-invalid @enderror"
                    id="truck_number" placeholder="Truck Registration Number">
                @error('truck_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Type --}}
            <div class="col-md-12 mb-0">
                <label class="form-label fw-bold">Truck Types</label>
                <div class="row text-center justify-content-center align-items-center">
                    @foreach ($types as $key => $typeLabel)
                        <div class="col-md-3 col-4 mb-3">
                            <label for="type_{{ $key }}" class="d-block text-center">
                                <input type="radio" wire:model="truck_type" value="{{ $key }}" id="type_{{ $key }}" class="form-check-input" {{ $truck_type == $key ? 'checked' : '' }} />
                                <img src="{{ asset('img/' . $key . '.png') }}" alt="" class="img-fluid mt-2" style="max-width: 60px;"><br>
                                <span class="fs-14">{{ $typeLabel }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('truck_type')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                <hr class="mb-2">
            </div>

            {{-- Ownership --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Ownership</label><br>
                <div class="d-flex mb-2 gap-3">
                    @foreach ($ownerships as $key => $ownership)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ownership" value="{{ $key }}"
                                id="ownership_{{ $key }}" {{ $ownership == $key ? 'checked' : '' }}>
                            <label class="form-check-label" for="ownership_{{ $key }}">
                                {{ $key === 'self' ? 'My Truck' : 'Market Truck' }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('ownership')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- Driver --}}
            <div class="col-md-12 mb-3">
                <select wire:model="driver_id" class="form-select @error('driver_id') is-invalid @enderror">
                    <option value="">Select Driver</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ $driver_id == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                    @endforeach
                </select>
                @error('driver_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">Update Truck</span>
                <span wire:loading wire:target="update">
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    Updating...
                </span>
            </button>
        </div>
    </form>
</div>