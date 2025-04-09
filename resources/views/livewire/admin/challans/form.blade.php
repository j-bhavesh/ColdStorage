@if($isOpen)
<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $challanId ? 'Edit Challan' : 'Create New Challan' }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="farmer_id" class="form-label">Farmer</label>
                    <select wire:model="farmer_id" id="farmer_id" class="form-select" {{ $challanId ? 'disabled' : '' }}>
                        <option value="">Select Farmer</option>
                        @foreach($farmers as $farmer)
                            <option value="{{ $farmer->id }}">{{ $farmer->name }} ({{ $farmer->farmer_id }})</option>
                        @endforeach
                    </select>
                    @error('farmer_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- <div class="mb-3">
                    <label for="vehicle_id" class="form-label">Vehicle</label>
                    <select wire:model="vehicle_id" id="vehicle_id" class="form-select">
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }}</option>
                        @endforeach
                    </select>
                    @error('vehicle_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div> --}}

                {{-- Vehicle Number --}}
                <div class="form-group required mb-3">
                    <label>Vehicle Number</label>
                    <input type="text" wire:model="vehicle_number" class="form-control @error('vehicle_number') is-invalid @enderror" placeholder="Enter vehicle number">
                    @error('vehicle_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="challan_number" class="form-label">Challan Number</label>
                    <input type="text" wire:model="challan_number" id="challan_number" class="form-control">
                    @error('challan_number') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-footer">
                @if($challanId)
                    <button type="button" class="btn btn-primary" wire:click="update">Update</button>
                @else
                    <button type="button" class="btn btn-primary" wire:click="store">Create</button>
                @endif
                <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endif

@if($showOverrideModal)
<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bag Quantity Exceeded</h5>
            </div>
            <div class="modal-body">
                <p>The bag quantity exceeds the remaining bags in the agreement.</p>
                <p>Agreement Bags: {{ $overrideData['agreement_bags'] }}</p>
                <p>Extra Bags: {{ $overrideData['extra_bags'] }}</p>
                <p>Would you like to proceed with extra bags?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="handleOverride(false)">Cancel</button>
                <button type="button" class="btn btn-primary" wire:click="handleOverride(true)">Accept</button>
            </div>
        </div>
    </div>
</div>
@endif
