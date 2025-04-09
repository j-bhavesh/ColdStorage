<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search companies...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
            <button wire:click="create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Company
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th wire:click="sortBy('id')" class="cursor-pointer">
                        ID
                        @if ($sortField === 'id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('name')" class="cursor-pointer">
                        Name
                        @if ($sortField === 'name')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('contact_person')" class="cursor-pointer">
                        Contact Person
                        @if ($sortField === 'contact_person')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('contact_number')" class="cursor-pointer">
                        Contact Number
                        @if ($sortField === 'contact_number')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Created By</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td>{{ $company->id }}</td>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->contact_person }}</td>
                        <td>{{ $company->contact_number }}</td>
                        <td>{{ $company->creator->name }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button wire:click="edit({{ $company->id }})" class="btn btn-info">
                                    <i class="text-white fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="if(confirm('⚠️ WARNING: This will permanently delete the company and ALL associated data including:\n\n• Seed Distributions\n• Seeds Bookings\n\nThis action cannot be undone. Are you sure you want to proceed?')) { Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('delete', {{ $company->id }}) }">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3">No companies found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($companies->hasPages())
        <div class="card-footer clearfix">
            {{ $companies->links() }}
        </div>
    @endif

    <!-- Modal -->
    @if($isOpen)
    <div class="modal show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog" style="z-index: 1050;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $company_id ? 'Edit Company' : 'Create Company' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close">
                    </button>
                </div>
                <form wire:submit.prevent="{{ $company_id ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="form-group required">
                            <label>Company Name</label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group required">
                            <label>Contact Person</label>
                            <input type="text" wire:model="contact_person" class="form-control @error('contact_person') is-invalid @enderror">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group required">
                            <label>Contact Number</label>
                            <input type="text" wire:model="contact_number" class="form-control @error('contact_number') is-invalid @enderror">
                            @error('contact_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" wire:model="address" class="form-control @error('address') is-invalid @enderror">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $company_id ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Success Message Toast -->
@if (session()->has('message'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('message') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

<!-- Error Message Toast -->
@if (session()->has('error'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="toast show align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif