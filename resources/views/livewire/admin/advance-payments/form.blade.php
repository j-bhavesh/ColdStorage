@if($isOpen)
<div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog" style="z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $advancePaymentId ? 'Edit Advance Payment' : 'Create Advance Payment' }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close">
                </button>
            </div>
            <form wire:submit.prevent="{{ $advancePaymentId ? 'update' : 'store' }}">
                <div class="modal-body">
                    <div class="form-group required mb-3">
                        <label>Farmer</label>
                        <select id="farmer_id" wire:model="farmer_id" class="form-control @error('farmer_id') is-invalid @enderror" {{ $advancePaymentId ? 'disabled' : '' }}>
                            <option value="">Select Farmer</option>
                            @foreach($farmers as $farmer)
                                <option value="{{ $farmer->id }}">
                                    {{ $farmer->name }} ({{ $farmer->farmer_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('farmer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Amount</label>
                        <input type="number" step="0.01" wire:model="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="Enter amount">
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Payment Date</label>
                        <input type="date" wire:model="payment_date" class="form-control @error('payment_date') is-invalid @enderror">
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Taken By</label>
                        <select wire:model.live="taken_by" class="form-control @error('taken_by') is-invalid @enderror">
                            <option value="self">Self</option>
                            <option value="other">Other</option>
                        </select>
                        @error('taken_by')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($taken_by === 'other')
                        <div class="form-group required mb-3">
                            <label>Taken By Name</label>
                            <input type="text" wire:model="taken_by_name" class="form-control @error('taken_by_name') is-invalid @enderror" placeholder="Enter name">
                            @error('taken_by_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $advancePaymentId ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif 