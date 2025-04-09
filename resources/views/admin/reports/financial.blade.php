<x-admin-layout>
    @section('title', 'Financial Report')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Financial Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Financial Report</li>
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
                    <form method="GET" action="{{ route('admin.reports.financial') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="date_from">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="date_to">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Generate Report</button>
                                <button type="button" class="btn btn-success me-2" onclick="exportToExcel()">Export Excel</button>
                                <button type="button" class="btn btn-danger" onclick="exportToPdf()">Export PDF</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Header -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <h4>KISAAN AGRO TECH (HENILBHAI)</h4>
                    <h5>KADIYADRA</h5>
                    <p class="mb-0">Financial Report - {{ request('date_from') ? Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') : 'All Time' }} 
                    @if(request('date_to')) to {{ Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }} @endif</p>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Revenue</h5>
                            <h3>₹{{ number_format($totals['total_amount'], 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Bags</h5>
                            <h3>{{ number_format($totals['total_bags'], 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Weight (kg)</h5>
                            <h3>{{ number_format($totals['total_weight'], 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Average Rate</h5>
                            <h3>₹14.75</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Details Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Financial Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>FARMER</th>
                                    <th>SEED VARIETY</th>
                                    <th>BAG QUANTITY</th>
                                    <th>NET WEIGHT (kg)</th>
                                    <th>RATE (₹/kg)</th>
                                    <th>AMOUNT (₹)</th>
                                    <th>COLD STORAGE</th>
                                    <th>TRANSPORTER</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($storageLoadings as $loading)
                                    <tr>
                                        <td>{{ $loading->created_at->format(env('DATE_FORMATE')) }}</td>
                                        <td>{{ $loading->agreement->farmer->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->agreement->seedVariety->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->bag_quantity }}</td>
                                        <td>{{ $loading->net_weight }}</td>
                                        <td>14.75</td>
                                        <td>{{ number_format($loading->net_weight * 14.75, 0) }}</td>
                                        <td>{{ $loading->coldStorage->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->transporter->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan="3"><strong>TOTAL</strong></td>
                                    <td><strong>{{ $totals['total_bags'] }}</strong></td>
                                    <td><strong>{{ $totals['total_weight'] }}</strong></td>
                                    <td></td>
                                    <td><strong>₹{{ number_format($totals['total_amount'], 0) }}</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Expense Breakdown -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Expense Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Transportation Costs</strong></td>
                                    <td>₹ <span id="transportation-cost">0</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Storage Costs</strong></td>
                                    <td>₹ <span id="storage-cost">0</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Packaging Costs</strong></td>
                                    <td>₹ <span id="packaging-cost">0</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Other Expenses</strong></td>
                                    <td>₹ <span id="other-expenses">0</span></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total Expenses</strong></td>
                                    <td>₹ <span id="total-expenses">0</span></td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Net Profit</strong></td>
                                    <td>₹ <span id="net-profit">{{ number_format($totals['total_amount'], 0) }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <canvas id="expenseChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Summary -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Monthly Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Total Loadings</th>
                                    <th>Total Bags</th>
                                    <th>Total Weight (kg)</th>
                                    <th>Total Revenue (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $monthlyData = $storageLoadings->groupBy(function($loading) {
                                        return $loading->created_at->format('Y-m');
                                    });
                                @endphp
                                @foreach($monthlyData as $month => $loadings)
                                    <tr>
                                        <td>{{ Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</td>
                                        <td>{{ $loadings->count() }}</td>
                                        <td>{{ $loadings->sum('bag_quantity') }}</td>
                                        <td>{{ $loadings->sum('net_weight') }}</td>
                                        <td>₹{{ number_format($loadings->sum('net_weight') * 14.75, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Expense Chart
        const ctx = document.getElementById('expenseChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Transportation', 'Storage', 'Packaging', 'Other'],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        function exportToExcel() {
            alert('Excel export functionality to be implemented');
        }

        function exportToPdf() {
            alert('PDF export functionality to be implemented');
        }
    </script>
</x-admin-layout>