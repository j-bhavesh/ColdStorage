<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search Vehicles...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
            <button wire:click="create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Vehicle
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th wire:click="sortBy('id')" class="cursor-pointer">
                        ID
                        @if($sortField === 'id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('vehicle_number')" class="cursor-pointer">
                        Vehicle Number
                        @if($sortField === 'vehicle_number')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Transporter</th>
                    <th wire:click="sortBy('vehicle_type')" class="cursor-pointer">
                        Vehicle Type
                        @if($sortField === 'vehicle_type')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                    <tr>
                        <td>{{ $vehicle->id }}</td>
                        <td>{{ $vehicle->vehicle_number }}</td>
                        <td>{{ $vehicle->transporter->name }}</td>
                        <td>{{ $vehicle->vehicle_type }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button wire:click="edit({{ $vehicle->id }})" class="btn btn-info">
                                    <i class="text-white fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="if(confirm('⚠️ WARNING: This will permanently delete the vehicle and ALL associated data including:\n\n• Challans\n• Storage Loadings\n• Storage Unloadings\n\nThis action cannot be undone. Are you sure you want to proceed?')) { Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('delete', {{ $vehicle->id }}) }">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-3">No vehicles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($vehicles->hasPages())
        <div class="card-footer clearfix">
            {{ $vehicles->links() }}
        </div>
    @endif

    @include('livewire.admin.vehicles.form', [
        'isOpen' => $isOpen
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