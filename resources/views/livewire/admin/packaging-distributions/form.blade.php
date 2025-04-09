@if($isOpen)
<div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog" style="z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $packagingDistributionId ? 'Edit Packaging Distribution' : 'Create Packaging Distribution' }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close">
                </button>
            </div>

            <form wire:submit.prevent="{{ $packagingDistributionId ? 'update' : 'store' }}">
                <div class="modal-body">
                    <div class="form-group required mb-3">
                        <label>Farmer(Agreement)</label>
                        <select id="agreement_id" wire:model.live="agreement_id" class="form-control @error('agreement_id') is-invalid @enderror" {{ $packagingDistributionId ? 'disabled' : '' }}>
                            <option value="">Select Agreement</option>
                            @foreach($farmers as $farmer)
                                @if( (empty($packagingDistributionId) && $farmer['remaining_quantity'] > 0) || (!empty($packagingDistributionId)) )
                                    <option value="{{ $farmer['agreement_id'] }}">
                                        {{ $farmer['farmer_name'] }} ({{ $farmer['farmer_id'] }}) -
                                        {{ $farmer['seed_variety_name'] }} -
                                        Agreement #{{ $farmer['agreement_id'] }} -
                                        Remaining: {{ $farmer['remaining_quantity'] }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('agreement_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        {{-- <label>Bag Quantity</label> --}}
                        <label>Supplied Bag(s)</label>
                        <input type="number" wire:model="bag_quantity" class="form-control @error('bag_quantity') is-invalid @enderror" placeholder="Enter bag quantity">
                        @error('bag_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Vehicle Number</label>
                        <input type="text" wire:model="vehicle_number" class="form-control @error('vehicle_number') is-invalid @enderror" placeholder="Enter vehicle number">
                        @error('vehicle_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Distribution Date</label>
                        <input type="date" wire:model="distribution_date" class="form-control @error('distribution_date') is-invalid @enderror">
                        @error('distribution_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Received By</label>
                        <input type="text" wire:model="received_by" class="form-control @error('received_by') is-invalid @enderror" placeholder="Enter receiver name">
                        @error('received_by')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $packagingDistributionId ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
