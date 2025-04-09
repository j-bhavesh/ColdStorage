@if($isOpen)
<div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog" style="z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $id ? 'Edit Unloading Company' : 'Create Unloading Company' }}</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close">
                </button>
            </div>
            <form wire:submit.prevent="{{ $id ? 'update' : 'store' }}">
                <div class="modal-body">
                    <div class="form-group required mb-3">
                        <label>Company Name</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter company name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Contact Person</label>
                        <input type="text" wire:model="contact_person" class="form-control @error('contact_person') is-invalid @enderror" placeholder="Enter contact person name">
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Contact Number</label>
                        <input type="text" wire:model="contact_number" class="form-control @error('contact_number') is-invalid @enderror" placeholder="Enter contact number">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Address</label>
                        <textarea wire:model="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Enter company address"></textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group required mb-3">
                        <label>Status</label>
                        <select wire:model="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status')
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