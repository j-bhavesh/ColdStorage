<x-admin-layout>
    @section('title', 'Processing Report')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Processing Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Processing Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Report Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.processing') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="farmer_id">Farmer</label>
                                <select name="farmer_id" id="farmer_id" class="form-control">
                                    <option value="">All Farmers</option>
                                    @foreach($farmers as $farmer)
                                        <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                            {{ $farmer->name }} ({{ $farmer->village_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="cold_storage_id">Cold Storage</label>
                                <select name="cold_storage_id" id="cold_storage_id" class="form-control">
                                    <option value="">All Cold Storages</option>
                                    @foreach($coldStorages as $coldStorage)
                                        <option value="{{ $coldStorage->id }}" {{ request('cold_storage_id') == $coldStorage->id ? 'selected' : '' }}>
                                            {{ $coldStorage->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="transporter_id">Transporter</label>
                                <select name="transporter_id" id="transporter_id" class="form-control">
                                    <option value="">All Transporters</option>
                                    @foreach($transporters as $transporter)
                                        <option value="{{ $transporter->id }}" {{ request('transporter_id') == $transporter->id ? 'selected' : '' }}>
                                            {{ $transporter->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-9 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Generate Report</button>
                                {{--<button type="button" class="btn btn-success me-2" onclick="exportToExcel()">Export Excel</button>--}}
                                <button type="button" class="btn btn-danger me-2" onclick="exportToPdf()">Export PDF</button>
                                <button type="button" class="btn btn-success" onclick="exportToExcel()">Export Excel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Header -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    @if(request('farmer_id') && isset($selectedFarmer))
                        <h4>{{ $selectedFarmer->name }} ({{ $selectedFarmer->farmer_id }})</h4>
                        <h5>{{ $selectedFarmer->village_name }}</h5>
                    @else
                        <h4>Farmer Name (FARMER ID)</h4>
                        <h5>FARMER VILLAGE</h5>
                    @endif
                    <p class="mb-0">Processing Report - {{ request('date_from') ? Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') : 'All Time' }} 
                    @if(request('date_to')) to {{ Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }} @endif</p>
                </div>
            </div>

            <!-- Main Data Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Processing Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>Farmer Id</th>
                                    <th>Farmer Name</th>
                                    <th>Phone</th>
                                    <th>Farmer Village</th>
                                    <th>VEHICLE NO.</th>
                                    <th>RST NO.</th>
                                    <th>TRANSPORTER</th>
                                    <th>BAG</th>
                                    <th>NET WT</th>
                                    {{-- <th>JIN (A)</th>
                                    <th>LILA (B)</th>
                                    <th>NEMATO D (C)</th>
                                    <th>KAPYELA/CHOLAT/VADHIYA (D)</th>
                                    <th>DRY BRUJING/DOR/VESTEJ (E)</th>
                                    <th>BLACK SPOT (F)</th>
                                    <th>TOTAL KAPAT (A to F)</th>
                                    <th>FINAL KAPAT/BAG 5kg VALID</th>
                                    <th>DEDUCTION AS PER REPORT</th> --}}
                                    <th>FINAL WT</th>
                                    <th>RATE</th>
                                    <th>AMOUNT</th>
                                    <th>COLD</th>
                                    <th>VARIETY</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($storageLoadings as $loading)
                                    @php
                                        $totalbags = !empty($loading->extra_bags) ? $loading->extra_bags+ $loading->bag_quantity : $loading->bag_quantity;
                                        $netWeight = $loading->net_weight;
                                        $finalNetWeight = $netWeight - $totalbags;
                                        $amount = number_format($netWeight * $loading->agreement->rate_per_kg ?? 0, 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $loading->created_at->format(env('DATE_FORMATE')) }}</td>
                                        <td>{{ $loading->agreement->farmer->farmer_id }}</td>
                                        <td>{{ $loading->agreement->farmer->name }}</td>
                                        <td>{{ $loading->agreement->farmerUser->phone }}</td>
                                        <td>{{ $loading->agreement->farmer->village_name }}</td>
                                        <td>{{ $loading->vehicle_number ?? 'N/A' }}</td>
                                        <td>{{ $loading->rst_number }}</td>
                                        <td>{{ $loading->transporter->name ?? 'N/A' }}</td>
                                        <td>{{ !empty($loading->extra_bags) ? $loading->extra_bags + $loading->bag_quantity : $loading->bag_quantity }}</td>
                                        <td>{{ $loading->net_weight }}</td>
                                        {{-- <td>0.00 number_format(rand(0, 100) / 100, 2)</td> --}}
                                        {{-- <td>0.00 number_format(rand(0, 100) / 100, 2)</td> --}}
                                        {{-- <td>0.00</td> --}}
                                        {{-- <td>0.00 number_format(rand(0, 100) / 100, 2)</td> --}}
                                        {{-- <td>0.00</td> --}}
                                        {{-- <td>0.00 number_format(rand(0, 100) / 100, 2) </td> --}}
                                        {{-- <td>0.00 number_format(rand(0, 100) / 100, 2)</td> --}}
                                        {{-- <td>0.00</td> --}}
                                        {{-- <td>0</td> --}}
                                        <td>{{ number_format($finalNetWeight, 2) }}</td>
                                        <td>{{ $loading->agreement->rate_per_kg ?? 0 }}</td>
                                        <td>{{ number_format($loading->net_weight * $loading->agreement->rate_per_kg ?? 0, 2) }}</td>
                                        <td>{{ $loading->coldStorage->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->agreement->seedVariety->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan="8"><strong>TOTAL</strong></td>
                                    <td><strong>{{ $totals['total_bags'] }}</strong></td>
                                    <td><strong>{{ number_format($totals['total_net_weight'], 2) }}</strong></td>
                                    {{-- <td colspan="0"></td> --}}
                                    <td><strong>{{ number_format($totals['total_final_weight'], 2) }}</strong></td>
                                    <td ></td>
                                    <td><strong>{{ number_format($totals['total_amount'], 2) }}</strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment Categories -->
            {{-- <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Payment/Expense Categories</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>LENO BAG</strong></td>
                                    <td>₹ <span id="leno-bag">0</span></td>
                                </tr>
                                <tr>
                                    <td><strong>TRANSPORTATION</strong></td>
                                    <td>₹ <span id="transportation">0</span></td>
                                </tr>
                                <tr>
                                    <td><strong>OTHER</strong></td>
                                    <td>₹ <span id="other">0</span></td>
                                </tr>
                                <tr>
                                    <td><strong>FERTILIZER DUE PAYMENT</strong></td>
                                    <td>₹ <span id="fertilizer-due">0</span></td>
                                </tr>
                                <tr>
                                    <td><strong>SEEDS DUE PAYMENT</strong></td>
                                    <td>₹ <span id="seeds-due">0</span></td>
                                </tr>
                                <tr>
                                    <td><strong>ADVANCE</strong></td>
                                    <td>₹ <span id="advance">0</span></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>FINAL PAYBLE AMOUNT</strong></td>
                                    <td>₹ <span id="final-payable">{{ number_format($totals['total_amount'], 0) }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Note -->
            <div class="card mt-3">
                <div class="card-body">
                    <p class="mb-0"><strong>Note:</strong> નોંધ: અનામત મુકેલ માલનું આવક ગાડી ભાડું, લીનો બેગ અને સ્ટોર ભાડું ચૂકવ્યા પછી જ માલ કાઢવાનો</p>
                    <p class="mb-0"><em>Note: The income of the reserved goods, truck rent, lino bag and store rent will be paid only after the goods are removed.</em></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exportToPdf() {
            // Get current form data
            const form = document.querySelector('form');
            const formData = new FormData(form);
            
            // Create a temporary form for PDF export
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = '{{ route("admin.reports.export.pdf") }}';
            tempForm.target = '_blank';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            tempForm.appendChild(csrfToken);
            
            // Add form data
            for (let [key, value] of formData.entries()) {
                if (value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    tempForm.appendChild(input);
                }
            }
            
            document.body.appendChild(tempForm);
            tempForm.submit();
            document.body.removeChild(tempForm);
        }

        function exportToExcel() {
            const form = document.querySelector('form');
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = '{{ route("admin.reports.export.excel") }}';
            tempForm.target = '_blank';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            tempForm.appendChild(csrfToken);

            // Collect each field manually
            ['farmer_id', 'date_from', 'date_to', 'cold_storage_id', 'transporter_id'].forEach(id => {
                const el = document.getElementById(id);
                if (el && el.value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = el.name;
                    input.value = el.value;
                    tempForm.appendChild(input);
                }
            });

            document.body.appendChild(tempForm);
            tempForm.submit();
            document.body.removeChild(tempForm);
        }

    </script>

    <!-- Select2 Ajax -->
    @push('scripts')
    <script>
    $(document).ready(function(){
        function initSelect2() {
            $('#farmer_id').select2({
                placeholder: 'Search and select farmer...',
                allowClear: true,
                width: '100%',
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
                            module: 'potato-agreement'
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

            
        }

        initSelect2();
        
    });
    </script>
    @endpush
</x-admin-layout>