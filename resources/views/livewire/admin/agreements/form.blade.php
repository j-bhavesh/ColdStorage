@if($isOpen)
<div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog" style="z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $agreementId ? 'Edit Agreement' : 'Create Agreement' }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close">
                </button>
            </div>
            <form wire:submit.prevent="{{ $agreementId ? 'update' : 'store' }}">
                <div class="modal-body">
                    <div class="form-group required mb-3">
                        <label>Farmer</label>
                        <select id="farmer_id" wire:model="farmer_id" class="form-control farmer_id @error('farmer_id') is-invalid @enderror" {{ $agreementId ? 'disabled' : '' }}>
                            <option value="">Select Farmer</option>
                            @foreach($farmers as $farmer)
                                <option value="{{ $farmer->id }}">{{ $farmer->name }} ({{ $farmer->farmer_id }})</option>
                            @endforeach
                        </select>
                        @error('farmer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        </div>
                        @if($selected_farmer_village)
                            <div class="form-group mb-3">
                                <label>Village</label>
                                <input type="text" class="form-control" value="{{ $selected_farmer_village }}" readonly disabled>
                            </div>
                        @endif
                    <div class="form-group required mb-3">
                        <label>Seed Variety</label>
                        <select wire:model.live="seed_variety_id" class="form-control @error('seed_variety_id') is-invalid @enderror">
                            <option value="">Select Seed Variety</option>
                            @foreach($seedVarieties as $variety)
                                <option value="{{ $variety->id }}">{{ $variety->name }}</option>
                            @endforeach
                        </select>
                        @error('seed_variety_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Rate per KG</label>
                        <input type="number" step="0.01" wire:model="rate_per_kg" class="form-control @error('rate_per_kg') is-invalid @enderror" placeholder="Enter rate per kg">
                        @error('rate_per_kg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Agreement Date</label>
                        <input type="date" wire:model="agreement_date" class="form-control @error('agreement_date') is-invalid @enderror">
                        @error('agreement_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Vighas</label>
                        <input type="number" step="0.01" wire:model="vighas" class="form-control @error('vighas') is-invalid @enderror" placeholder="Enter vighas">
                        @error('vighas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Bag Quantity</label>
                        <input type="number" wire:model="bag_quantity" class="form-control @error('bag_quantity') is-invalid @enderror" placeholder="Enter bag quantity">
                        @error('bag_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $agreementId ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
