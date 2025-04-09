<x-admin-layout>
    @section('title', 'Farmer Agreements - ' . $farmer->name)
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Agreements - {{ $farmer->name }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.farmer',['farmer_id' => $farmer->id]) }}">Farmer Report</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Agreements</li>
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
                            <p><strong>Total Agreements:</strong> {{ $totals['total_agreements'] }}</p>
                            <p><strong>Total Bags:</strong> {{ $totals['total_bags'] }}</p>
                            <p><strong>Total Amount:</strong> ₹{{ number_format($totals['total_amount'], 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agreements Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Agreements Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Agreement ID</th>
                                    <th>Seed Variety</th>
                                    <th>Bag Quantity</th>
                                    <th>Rate</th>
                                    <th>Vighas</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agreements as $agreement)
                                    <tr>
                                        <td>{{ $agreement->id }}</td>
                                        <td>{{ $agreement->seedVariety->name ?? 'N/A' }}</td>
                                        <td>{{ $agreement->bag_quantity }}</td>
                                        <td>₹{{ $agreement->rate_per_kg }}</td>
                                        {{--<td>₹{{ number_format($agreement->bag_quantity * $agreement->rate, 0) }}</td>--}}
                                        <td>{{ $agreement->vighas }}</td>
                                        <td>
                                            @if($agreement->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $agreement->agreement_date->format(env('DATE_FORMATE')) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No agreements found</td>
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