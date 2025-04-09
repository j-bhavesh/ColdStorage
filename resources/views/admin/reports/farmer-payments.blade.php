<x-admin-layout>
    @section('title', 'Farmer Payments - ' . $farmer->name)
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Advance Payments - {{ $farmer->name }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.farmer',['farmer_id' => $farmer->id]) }}">Farmer Report</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Payments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <!-- Farmer Info -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Farmer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $farmer->name }}</p>
                            <p><strong>Farmer ID:</strong> {{ $farmer->farmer_id }}</p>
                            <p><strong>Village:</strong> {{ $farmer->village_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Payments:</strong> {{ $totals['total_payments'] }}</p>
                            <p><strong>Total Amount:</strong> ₹{{ number_format($totals['total_amount'], 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Advance Payments Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Taken By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>₹{{ number_format($payment->amount, 0) }}</td>
                                        <td>{{ $payment->created_at->format(env('DATE_FORMATE')) }}</td>
                                        <td>{{ $payment->payment_method ?? 'Cash' }}</td>
                                        <td>
                                            @if($payment->taken_by == 'other')
                                                {{ $payment->taken_by_name }}
                                            @else
                                                {{ $payment->taken_by }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No advance payments found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>