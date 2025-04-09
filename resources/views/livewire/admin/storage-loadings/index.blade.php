<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="input-group" style="width: 250px;">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search...">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <div class="classified-data-wrapper" wire:ignore>
                 <input type="text" id="financialYearPicker" class="form-control" placeholder="Select Date" autocomplete="off" style="width: 220px; text-align: center;" />
            </div>
            <div class="tbl-header-actions-bar">
                <button wire:click="create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New
                </button>
                <button wire:click="exportData" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th wire:click="sortBy('rst_number')" style="cursor: pointer;">
                            RST Number
                            @if($sortField === 'rst_number')
                                @if($sortDirection === 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @endif
                        </th>
                        <th>Seed Verity</th>
                        <th>Farmer Id</th>
                        <th>Farmer</th>
                        <th>Village</th>
                        <th>Phone</th>
                        <th>Transporter</th>
                        <th>Vehicle No</th>
                        <th>Cold Storage</th>
                        <th wire:click="sortBy('bag_quantity')" style="cursor: pointer;">
                            {{-- Bag Quantity --}}
                            Received Bags
                            @if($sortField === 'bag_quantity')
                                @if($sortDirection === 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @endif
                        </th>
                        <th>Pending Bags</th>
                        <th>Surplus Bags</th>
                        <th wire:click="sortBy('net_weight')" style="cursor: pointer;">
                            Net Weight
                            @if($sortField === 'net_weight')
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
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($storageLoadings as $storageLoading)
                        <tr>
                            <td>{{ $storageLoading->id }}</td>
                            <td>{{ $storageLoading->rst_number }}</td>
                            <td>{{ $storageLoading->agreement->seedVariety->name }}</td>
                            <td>{{ $storageLoading->agreement->farmer->farmer_id }}</td>
                            <td>{{ $storageLoading->agreement->farmer->name }}</td>
                            <td>{{ $storageLoading->agreement->farmer->village_name }}</td>
                            <td>{{ $storageLoading->agreement->farmerUser->phone }}</td>
                            <td>{{ $storageLoading->transporter->name }}</td>
                            {{-- <td>{{ $storageLoading->vehicle->vehicle_number }}</td> --}}
                            <td>{{ $storageLoading->vehicle_number }}</td>
                            <td>{{ $storageLoading->coldStorage->name }}</td>
                            <td>{{ $storageLoading->bag_quantity }}</td>
                            <td>{{ $storageLoading->pending_bags ?? 0 }}</td>
                            <td>{{ $storageLoading->extra_bags ?? 0 }}</td>
                            <td>{{ number_format($storageLoading->net_weight, 2) }}</td>
                            <td>{{ $storageLoading->created_at->format(env('DATE_FORMATE')) }}</td>
                            <td>{{ $storageLoading->creator->name }}</td>
                            <td>
                                <button wire:click="edit({{ $storageLoading->id }})" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-danger"
                                    onclick="if(confirm('⚠️ WARNING: This will permanently delete the storage loading, And all associated data including:\n\n• Bags Data (will be modified)\n\nThis action cannot be undone. Are you sure you want to proceed?')) { Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('delete', {{ $storageLoading->id }}) }">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="text-center">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $storageLoadings->links() }}
        </div>
    </div>
    @include('livewire.admin.storage-loadings.form')

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

<!-- Select2 Ajax -->
@push('scripts')
<script>
$(document).ready(function(){
    function initSelect2() {
        $('#selected_farmer_id').select2({
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
                        module: 'storage-loading'
                    };
                },
                processResults: function (data) {
                    console.log('Data : ', data);
                    // Map results to Select2 format
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        // Set initial value if editing
        let selectedId = @this.selected_farmer_id ? @this.selected_farmer_id : null;
        let selectedText = @this.selected_farmer_name ? @this.selected_farmer_name : null;

        if (selectedId && selectedText) {
            let option = new Option(selectedText, selectedId, true, true);
            $('#selected_farmer_id').append(option).trigger('change');
        }

        // Sync back to Livewire when user selects
        $('#selected_farmer_id').off('change.select2').on('change.select2', function (e) {
            @this.set('selected_farmer_id', $(this).val());
        });
    }

    initSelect2();

    // Daterange picker JS
    function initFinancialYearPicker() {
        const pickerInput = $('#financialYearPicker');

        // Prevent duplicate picker initialization
        if (!pickerInput.length || pickerInput.data('daterangepicker')) {
            return;
        }

        // Get current year
        const currentYear = moment().year();
        const minYear = currentYear - 1; // Previous year
        const maxYear = currentYear + 3; // Next 3 years (total 5 years including current and previous)

        pickerInput.daterangepicker({
            autoUpdateInput: false,
            opens: 'center',
            linkedCalendars:false,
            showDropdowns: true,
            cancelButtonClasses : 'btn-danger',
            applyButtonClasses : 'btn-success',
            minYear: minYear,
            maxYear: maxYear,
            locale: {
                format: 'DD-MM-YYYY',
                cancelLabel: 'Clear'
            },
        });

        // Get the picker instance
        const picker = pickerInput.data('daterangepicker');

        // Override the updateCalendars method to customize year dropdowns
        if (picker) {
            const originalUpdateCalendars = picker.updateCalendars.bind(picker);
            
            picker.updateCalendars = function() {
                originalUpdateCalendars();
                
                // Fix both calendars to have same year range
                setTimeout(() => {
                    const $calendars = $('.daterangepicker .drp-calendar');
                    
                    $calendars.each(function() {
                        const $yearSelect = $(this).find('select.yearselect');
                        
                        if ($yearSelect.length) {
                            const currentSelectedYear = parseInt($yearSelect.val());
                            
                            // Clear and rebuild options with full range
                            $yearSelect.empty();
                            
                            // Add years from minYear to maxYear (2024-2028)
                            for (let year = minYear; year <= maxYear; year++) {
                                $yearSelect.append(`<option value="${year}">${year}</option>`);
                            }
                            
                            // Restore the selected year if it's in range
                            if (currentSelectedYear >= minYear && currentSelectedYear <= maxYear) {
                                $yearSelect.val(currentSelectedYear);
                            }
                        }
                    });
                }, 0);
            };
        }

        // Customize the date range display on show
        pickerInput.on('show.daterangepicker', function(ev, picker) {
            updateDateRangeLabel(picker);
            // fixRightCalendarYearDropdown();
        });

        // pickerInput.on('showCalendar.daterangepicker', function(ev, picker) {
        //     
        // });

        // Apply selection
        pickerInput.on('apply.daterangepicker', function (ev, pickerData) {
            const start = pickerData.startDate.format('DD-MM-YYYY');
            const end = pickerData.endDate.format('DD-MM-YYYY');

            updateDateRangeLabel(pickerData);
            $(this).val(start + ' to ' + end);
            Livewire.dispatch('yearRangeSelected', { range: start + ' - ' + end });
            // Update Livewire property
            @this.set('selectedYear', `${start} - ${end}`);
        });

        // Update on calendar changes
        $(document).on('mouseup', '.daterangepicker td.available', function() {
            setTimeout(function() {
                const picker = pickerInput.data('daterangepicker');
                if (picker) {
                    updateDateRangeLabel(picker);
                }
            }, 10);
        });

        // Clear selection
        pickerInput.on('cancel.daterangepicker', function (ev, pickerData) {
            const today = moment(); // current date

            // Clear the input box
            $(this).val('');

            // Reset picker start and end dates to today
            pickerData.setStartDate(today);
            pickerData.setEndDate(today);

            // Update the visible date range label to show today's date
            updateDateRangeLabel(pickerData);

            // Clear Livewire property and emit reset
            Livewire.dispatch('yearRangeSelected', { range: '' });
            @this.set('selectedYear', '');
        });

        console.log('✅ Daterangepicker initialized');
    }

    initFinancialYearPicker();


    // Re-init every time Livewire updates DOM
    Livewire.hook('message.processed', (message, component) => {
        initSelect2();
        initFinancialYearPicker();
    });

    Livewire.hook('element.updated', (el, component) => {
        if ($(el).find('.farmer_id').length > 0) {
            initSelect2();
        }
        initFinancialYearPicker();
    });

    Livewire.hook('morph.updated', (el, component) => {
        initSelect2();
        initFinancialYearPicker();
    });

    // Update the selected date lable
    function updateDateRangeLabel(picker) {
        const start = picker.startDate.format('DD-MM-YYYY');
        const end = picker.endDate.format('DD-MM-YYYY');
        
        // Find and update the date range display element
        const $dateRangeDisplay = $('.daterangepicker .drp-selected');
        
        if ($dateRangeDisplay.length) {
            $dateRangeDisplay.html(`<strong>Start Date:</strong> ${start} &nbsp; - &nbsp; <strong>End Date:</strong> ${end}`);
        }
    }
});
</script>
@endpush