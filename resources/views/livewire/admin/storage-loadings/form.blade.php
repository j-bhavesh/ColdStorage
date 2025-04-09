@if($isOpen)
<div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog" style="z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $storageLoadingId ? 'Edit Storage Loading' : 'Create Storage Loading' }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close">
                </button>
            </div>
            <form wire:submit.prevent="{{ $storageLoadingId ? 'update' : 'store' }}">
                <div class="modal-body">
                    <div class="form-group required mb-3">
                        <label>Farmer</label>
                        <select id="selected_farmer_id" wire:model.live="selected_farmer_id" class="form-control @error('selected_farmer_id') is-invalid @enderror" {{ $storageLoadingId ? 'disabled' : '' }}>
                            <option value="">Select Farmer</option>
                            @foreach($farmers as $farmer)
                                @endphp
                                <option value="{{ $farmer->id }}">
                                    {{ $farmer->name }} ({{ $farmer->farmer_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('selected_farmer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($selected_farmer_id)
                        <div class="form-group required mb-3">
                            <label>Seed Variety</label>
                            <select wire:model.live="agreement_id" class="form-control @error('agreement_id') is-invalid @enderror">
                                <option value="">Select Seed Variety</option>
                                @foreach($seedVarieties as $seedVariety)
                                    <option value="{{ $seedVariety->agreement_id }}">
                                        {{ $seedVariety->name }} (#AgreementId: {{ $seedVariety->agreement_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('agreement_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

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
                        <label>RST Number</label>
                        <input type="text" wire:model="rst_number" class="form-control @error('rst_number') is-invalid @enderror" placeholder="Enter RST number">
                        @error('rst_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Chamber Number</label>
                        <input type="text" wire:model="chamber_no" class="form-control @error('chamber_no') is-invalid @enderror" placeholder="Enter Chamber number">
                        @error('chamber_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Net weight</label>
                        <input type="number" wire:model="net_weight" class="form-control @error('net_weight') is-invalid @enderror" placeholder="Enter net weight">
                        @error('net_weight')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Bag Quantity</label>
                        <input type="number" wire:model.live="bag_quantity" class="form-control @error('bag_quantity') is-invalid @enderror" placeholder="Enter bag quantity">
                        @error('bag_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($extra_bags)
                        <div class="form-group mb-3">
                            <label>Extra Bags</label>
                            <input type="number" wire:model="extra_bags" class="form-control @error('extra_bags') is-invalid @enderror" readonly>
                            @error('extra_bags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Remarks</label>
                            <textarea wire:model="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3" placeholder="Enter remarks for extra bags"></textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $storageLoadingId ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($showOverrideModal)
<div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog" style="z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bag Quantity Exceeded</h5>
            </div>
            <div class="modal-body">
                <p>The bag quantity exceeds the remaining bags in the agreement.</p>
                <p>Agreement Bags: {{ $overrideData['agreement_bags'] }}</p>
                <p>Surplus Bags: {{ $overrideData['surplus_bags'] }}</p>
                {{-- <p>Pending Bags: {{ $overrideData['pending_bags'] }}</p> --}}
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
@endif
