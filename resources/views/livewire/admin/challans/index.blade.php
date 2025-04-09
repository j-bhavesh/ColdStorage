<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="col-md-6 text-end">
                <button wire:click="create()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th wire:click="sortBy('id')" style="cursor: pointer;">
                            ID
                            @if($sortField === 'id')
                                @if($sortDirection === 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @endif
                        </th>
                        <th wire:click="sortBy('challan_number')" style="cursor: pointer;">
                            Challan Number
                            @if($sortField === 'challan_number')
                                @if($sortDirection === 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @endif
                        </th>
                        <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                            Date
                            @if($sortField === 'created_at')
                                @if($sortDirection === 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @endif
                        </th>
                        <th>Farmer</th>
                        <th>Vehicle</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($challans as $challan)
                        <tr>
                            <td>{{ $challan->id }}</td>
                            <td>{{ $challan->challan_number }}</td>
                            <td>{{ $challan->created_at->format(env('DATE_TIME_FORMATE')) }}</td>
                            <td>{{ $challan->farmer->name }}</td>
                            {{-- <td>{{ $challan->vehicle->vehicle_number }}</td> --}}
                            <td>{{ $challan->vehicle_number }}</td>
                            <td>{{ $challan->creator->name }}</td>
                            <td>
                                <button wire:click="edit({{ $challan->id }})" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="if(confirm('⚠️ WARNING: This will permanently delete the challan.\n\nThis action cannot be undone. Are you sure you want to proceed?')) { Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('delete', {{ $challan->id }}) }">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $challans->links() }}
        </div>
    </div>
    @include('livewire.admin.challans.form')

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

@push('scripts')
<script>
$(document).ready(function(){
    
    function initSelect2() {
        $('#farmer_id').select2({
            placeholder: 'Search and select farmer...',
            // allowClear: true,
            width: '100%',
            dropdownParent: $('.modal-content'),
            containerCssClass: 'farmers-select2-container',
            selectionCssClass: 'farmers-select2-selection',
            dropdownCssClass: 'farmers-select2-dropdown',
            maximumSelectionLength: 2, // AJAX triggers only after 2 characters

            ajax: {
                url: "{{ route('admin.farmers.search') }}",
                dataType: 'json',
                delay: 250, // wait 250ms after typing
                data: function (params) {
                    return {
                        search: params.term, // user input
                        module: 'challan'
                    };
                },
                processResults: function (data) {
                    // Map results to Select2 format
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        // Set initial value if editing
        let selectedId = @this.farmer_id ? @this.farmer_id : null;
        let selectedText = @this.farmer_name ? @this.farmer_name : null;

        if (selectedId && selectedText) {
            let option = new Option(selectedText, selectedId, true, true);
            $('#farmer_id').append(option).trigger('change');
        }

        // Sync back to Livewire when user selects
        $('#farmer_id').off('change.select2').on('change.select2', function (e) {
            @this.set('farmer_id', $(this).val());
        });
    }

    initSelect2();

    // Re-init every time Livewire updates DOM
    Livewire.hook('message.processed', (message, component) => {
        initSelect2();
    });

    Livewire.hook('element.updated', (el, component) => {
        if ($(el).find('#farmer_id').length > 0) {
            initSelect2();
        }
    });

    Livewire.hook('morph.updated', (el, component) => {
        initSelect2();
    });
});
</script>
@endpush