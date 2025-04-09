<!-- Modal -->
@if($isOpen)
    <div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog" style="z-index: 1050;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $seedsBookingId ? 'Edit Seeds booking' : 'Create Seeds booking' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close">
                    </button>
                </div>

                <form wire:submit.prevent="{{ $seedsBookingId ? 'update' : 'store' }}">
                    <div class="modal-body">
                        @if (session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div class="form-group required mb-3">
                            <label>Select Farmer</label>
                            <select id="farmer_id" wire:model="farmer_id" class="form-control farmer-select  @error('farmer_id') is-invalid @enderror" {{ $seedsBookingId ? 'disabled' : '' }}>
                                <option value="">Select a farmer</option>
                                @foreach($farmers as $farmer)
                                    <option value="{{ $farmer->id }}">{{ $farmer->name }} ({{ $farmer->farmer_id }}) - {{ $farmer->village_name }}</option>
                                @endforeach
                            </select>
                            @error('farmer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group required mb-3">
                            <label>Select Company</label>
                            <select wire:model="company_id" class="form-control @error('company_id') is-invalid @enderror">
                                <option value="">Select a company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group required mb-3">
                            <label>Select Seed Variety</label>
                            <select wire:model.live="seed_variety_id" class="form-control @error('seed_variety_id') is-invalid @enderror">
                                <option value="">Select a seed variety</option>
                                @foreach($seedVarieties as $seedVariety)
                                    <option value="{{ $seedVariety->id }}">{{ $seedVariety->name }}</option>
                                @endforeach
                            </select>
                            @error('seed_variety_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Bag Quantity</label>
                            <input type="text" wire:model="bag_quantity" class="form-control @error('bag_quantity') is-invalid @enderror">
                            @error('bag_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Booking Type</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                        type="radio" 
                                        wire:model.live="booking_type" 
                                        name="booking_type" 
                                        id="booking_type_debit" 
                                        value="debit" 
                                        checked>
                                    <label class="form-check-label" for="booking_type_debit">
                                        Debit
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                        type="radio" 
                                        wire:model.live="booking_type" 
                                        name="booking_type" 
                                        id="booking_type_cash" 
                                        value="cash">
                                    <label class="form-check-label" for="booking_type_cash">
                                        Cash
                                    </label>
                                </div>
                            </div>
                            @error('booking_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New added bag rate -->
                        <div class="form-group mb-3">
                            <label>Bag rate</label>
                            <input type="text" wire:model.live="bag_rate" class="form-control @error('bag_rate') is-invalid @enderror">
                            @error('bag_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Booking amount</label>
                            <input type="text" 
                            wire:model="booking_amount" 
                            class="form-control @error('booking_amount') is-invalid @enderror"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            wire:keydown.debounce.500ms="validateBookingAmount">
                            @error('booking_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" onclick="event.stopPropagation();">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $seedsBookingId ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
