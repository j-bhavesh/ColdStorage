<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search Packaging Distributions...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
            <button wire:click="create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Packaging Distribution
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th wire:click="sortBy('farmer_id')" class="cursor-pointer">
                        Farmer
                        @if($sortField === 'farmer_id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('bag_quantity')" class="cursor-pointer">
                        {{-- Bag Quantity --}}
                        Supplied Bags
                        @if($sortField === 'bag_quantity')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('vehicle_number')" class="cursor-pointer">
                        Vehicle Number
                        @if($sortField === 'vehicle_number')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('distribution_date')" class="cursor-pointer">
                        Distribution Date
                        @if($sortField === 'distribution_date')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('received_by')" class="cursor-pointer">
                        Received By
                        @if($sortField === 'received_by')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packagingDistributions as $packagingDistribution)
                    <tr>
                        <td>{{ $packagingDistribution->farmer->name }}</td>
                        <td>{{ $packagingDistribution->bag_quantity }}</td>
                        <td>{{ $packagingDistribution->vehicle_number }}</td>
                        <td>{{ $packagingDistribution->distribution_date->format('Y-m-d') }}</td>
                        <td>{{ $packagingDistribution->received_by }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button wire:click="edit({{ $packagingDistribution->id }})" class="btn btn-info">
                                    <i class="text-white fas fa-edit"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-danger"
                                    onclick="if(confirm('⚠️ WARNING: This will permanently delete the packaging distribution.\n\nThis action cannot be undone. Are you sure you want to proceed?')) { Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('delete', {{ $packagingDistribution->id }}) }">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-3">No Packaging Distributions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($packagingDistributions->hasPages())
        <div class="card-footer clearfix">
            {{ $packagingDistributions->links() }}
        </div>
    @endif

    @include('livewire.admin.packaging-distributions.form', [
        'isOpen' => $isOpen,
        'packagingDistributionId' => $packagingDistributionId,
        'farmers' => $farmers
    ])

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

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</div>
