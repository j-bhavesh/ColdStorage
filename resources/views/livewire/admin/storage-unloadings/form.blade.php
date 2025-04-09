@if($isOpen)
<div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog" style="z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $id ? 'Edit Storage Unloading' : 'Create Storage Unloading' }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close">
                </button>
            </div>
            <form wire:submit.prevent="{{ $id ? 'update' : 'store' }}">
                <div class="modal-body">
                    <div class="form-group required mb-3">
                        <label>Company</label>
                        <select wire:model="company_id" class="form-control @error('company_id') is-invalid @enderror">
                            <option value="">Select Company</option>
                            @foreach($unloadingCompanies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Cold Storage</label>
                        <select wire:model="cold_storage_id" class="form-control @error('cold_storage_id') is-invalid @enderror">
                            <option value="">Select Cold Storage</option>
                            @foreach($coldStorages as $coldStorage)
                                <option value="{{ $coldStorage->id }}">{{ $coldStorage->name }}</option>
                            @endforeach
                        </select>
                        @error('cold_storage_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Transporter</label>
                        <select wire:model.live="transporter_id" class="form-control @error('transporter_id') is-invalid @enderror">
                            <option value="">Select Transporter</option>
                            @foreach($transporters as $transporter)
                                <option value="{{ $transporter->id }}">{{ $transporter->name }}</option>
                            @endforeach
                        </select>
                        @error('transporter_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- <div class="form-group required mb-3">
                        <label>Vehicle</label>
                        <select wire:model.live="vehicle_id" class="form-control @error('vehicle_id') is-invalid @enderror" {{ !$transporter_id ? 'disabled' : '' }}>
                            <option value="">
                                @if(!$transporter_id)
                                    Please select a transporter first
                                @elseif($vehicles->count() == 0)
                                    No vehicles available for this transporter
                                @else
                                    Select Vehicle
                                @endif
                            </option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }}</option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    {{-- Vehicle Number --}}
                    <div class="form-group required mb-3">
                        <label>Vehicle Number</label>
                        <input type="text" wire:model="vehicle_number" class="form-control @error('vehicle_number') is-invalid @enderror" placeholder="{{ !$transporter_id ? 'Please select a transporter first' : 'Enter vehicle number' }}" {{ !$transporter_id ? 'disabled' : '' }}>
                        @error('vehicle_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Seed Variety</label>
                        <select wire:model="seed_variety_id" class="form-control @error('seed_variety_id') is-invalid @enderror">
                            <option value="">Select Seed Variety</option>
                            @foreach($seedVarieties as $seedVariety)
                                <option value="{{ $seedVariety->id }}">{{ $seedVariety->name }}</option>
                            @endforeach
                        </select>
                        @error('seed_variety_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>RST No</label>
                        <input type="text" wire:model="rst_no" class="form-control @error('rst_no') is-invalid @enderror" placeholder="Enter RST No">
                        @error('rst_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Chamber No</label>
                        <input type="text" wire:model="chamber_no" class="form-control @error('chamber_no') is-invalid @enderror" placeholder="Enter Chamber No">
                        @error('chamber_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Location</label>
                        <input type="text" wire:model="location" class="form-control @error('location') is-invalid @enderror" placeholder="Enter Location">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Bag Quantity</label>
                        <input type="number" wire:model="bag_quantity" class="form-control @error('bag_quantity') is-invalid @enderror" placeholder="Enter Bag Quantity">
                        @error('bag_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Weight</label>
                        <input type="number" step="0.01" wire:model="weight" class="form-control @error('weight') is-invalid @enderror" placeholder="Enter Weight">
                        @error('weight')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $id ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif 