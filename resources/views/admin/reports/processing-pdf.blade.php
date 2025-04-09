<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Processing Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .farmer-info {
            font-size: 11px;
            margin-bottom: 2px;
        }
        .report-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .filters {
            margin-bottom: 10px;
            font-size: 9px;
        }
        .filters span {
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8px;
        }
        th, td {
            border: 1px solid #333;
            padding: 2px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .page-break {
            page-break-before: always;
        }
        .no-data {
            text-align: center;
            padding: 10px;
            font-style: italic;
        }
        .amount-cell {
            text-align: right;
        }
        .date-cell {
            text-align: center;
            white-space: nowrap;
        }
        .vehicle-cell {
            text-align: center;
            white-space: nowrap;
        }
        .number-cell {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($selectedFarmer)
            <div class="farmer-info">{{ $selectedFarmer->name }}</div>
            <div class="farmer-info">{{ $selectedFarmer->village_name }}</div>
            <div class="farmer-info">(Farmer ID: {{ $selectedFarmer->farmer_id }})</div>
        @else
            <div class="company-name">KISAAN AGRO TECH (HENILBHAI)</div>
            <div class="farmer-info">KADIYADRA</div>
        @endif
        <div class="report-title">Processing Report</div>
        <div class="filters">
            @if($request->date_from)
                <span>From: {{ \Carbon\Carbon::parse($request->date_from)->format(env('DATE_FORMATE')) }}</span>
            @endif
            @if($request->date_to)
                <span>To: {{ \Carbon\Carbon::parse($request->date_to)->format(env('DATE_FORMATE')) }}</span>
            @endif
            @if($request->cold_storage_id)
                <span>Cold Storage: {{ \App\Models\ColdStorage::find($request->cold_storage_id)->name ?? 'N/A' }}</span>
            @endif
            @if($request->transporter_id)
                <span>Transporter: {{ \App\Models\Transporter::find($request->transporter_id)->name ?? 'N/A' }}</span>
            @endif
        </div>
    </div>

    <table>
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
                    $rate = $loading->agreement->rate ?? 0;
                    // $amount = ($loading->net_weight ?? 0) * $rate;
                    $totalKapat = ($loading->jin_a ?? 0) + ($loading->lila_b ?? 0) + ($loading->nemato_d ?? 0) + ($loading->kapyela_cholat_vadhiya ?? 0) + ($loading->dry_brujing_dor_vestej ?? 0) + ($loading->black_spot ?? 0);
                    $totalbags = !empty($loading->extra_bags) ? $loading->extra_bags+ $loading->bag_quantity : $loading->bag_quantity;
                    $netWeight = $loading->net_weight;
                    $finalNetWeight = $netWeight - $totalbags;
                    $amount = number_format($netWeight * $loading->agreement->rate_per_kg ?? 0, 2);
                @endphp
                <tr>
                    <td class="date-cell">{{ $loading->created_at->format(env('DATE_FORMATE')) }}</td>
                    <td>{{ $loading->agreement->farmer->farmer_id }}</td>
                    <td>{{ $loading->agreement->farmer->name }}</td>
                    <td>{{ $loading->agreement->farmerUser->phone }}</td>
                    <td>{{ $loading->agreement->farmer->village_name }}</td>
                    <td class="vehicle-cell">{{ $loading->vehicle_number ?? '-' }}</td>
                    <td>{{ $loading->rst_number ?? '-' }}</td>
                    <td>{{ $loading->transporter->name ?? '-' }}</td>
                    <td class="number-cell">{{ !empty($loading->extra_bags) ? $loading->extra_bags + $loading->bag_quantity : $loading->bag_quantity }}</td>
                    <td class="number-cell">{{ number_format($loading->net_weight ?? 0, 2) }}</td>
                    <td class="number-cell">{{ number_format($finalNetWeight ?? 0, 2) }}</td>
                    <td class="number-cell">{{ $loading->agreement->rate_per_kg > 0 ? number_format($loading->agreement->rate_per_kg ?? 0, 2) : '-' }}</td>
                    <td class="amount-cell">{{ $amount > 0 ? $amount : '-' }}</td>
                    <td>{{ $loading->coldStorage->name ?? 'N/A' }}</td>
                    <td>{{ $loading->agreement->seedVariety->name ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="no-data">No data found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="8">TOTAL</th>
                <th class="number-cell">{{ $totals['total_bags'] }}</th>
                <th class="number-cell">{{ number_format($totals['total_net_weight'], 2) }}</th>
                <th class="number-cell">{{ number_format($totals['total_final_weight'], 2) }}</th>
                <th></th>
                <th class="amount-cell">{{ $totals['total_amount'] > 0 ? number_format($totals['total_amount'], 2) : '-' }}</th>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 10px; font-size: 8px;">
        <p><strong>Generated on:</strong> {{ now()->format(env('DATE_TIME_FORMATE')) }}</p>
        <p><strong>Total Records:</strong> {{ $storageLoadings->count() }}</p>
        @if($selectedFarmer)
            <p><strong>Farmer:</strong> {{ $selectedFarmer->name }} ({{ $selectedFarmer->village_name }})</p>
        @endif
    </div>
</body>
</html> 