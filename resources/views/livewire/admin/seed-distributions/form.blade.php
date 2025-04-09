@if($isOpen)
<div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog" style="z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $id ? 'Edit Distribution' : 'Create Distribution' }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close">
                </button>
            </div>
            <form wire:submit.prevent="{{ $id ? 'update' : 'store' }}">
                <div class="modal-body">
                    <div class="form-group required mb-3">
                        <label>Seeds Booking</label>
                        <select id="seeds_booking_id" wire:model.live="seeds_booking_id" class="form-control @error('seeds_booking_id') is-invalid @enderror" {{ $id ? 'disabled' : ''}}>
                            <option value="">Select Seeds Booking</option>
                            @foreach($seedsBookings as $booking)
                                <option value="{{ $booking['id'] }}">{{ $booking['name'] }}</option>
                            @endforeach
                        </select>
                        @error('seeds_booking_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Farmer</label>
                        <input type="text" class="form-control" value="{{ $farmer_name }}" readonly>
                        <input type="hidden" wire:model="farmer_id">
                    </div>

                    <div class="form-group required mb-3">
                        <label>Seed Variety</label>
                        <input type="text" class="form-control" value="{{ $seed_variety_name }}" readonly>
                        <input type="hidden" wire:model="seed_variety_id">
                    </div>

                    <div class="form-group required mb-3">
                        <label>Company</label>
                        <input type="text" class="form-control" value="{{ $company_name }}" readonly>
                        <input type="hidden" wire:model="company_id">
                    </div>

                    <div class="form-group required mb-3">
                        <!-- <label>Bag Quantity</label> -->
                        <label>Supplied Bag(s)</label>
                        <input type="number" wire:model="bag_quantity" class="form-control @error('bag_quantity') is-invalid @enderror" placeholder="Enter supplied bag quantity">
                        @error('bag_quantity')
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
                        <label>Vehicle Number</label>
                        <input type="text" wire:model="vehicle_number" class="form-control @error('vehicle_number') is-invalid @enderror" placeholder="Enter vehicle number">
                        @error('vehicle_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Received By</label>
                        <input type="text" wire:model="received_by" class="form-control @error('received_by') is-invalid @enderror" placeholder="Enter receiver's name">
                        @error('received_by')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal" onclick="event.stopPropagation();">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $id ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif 