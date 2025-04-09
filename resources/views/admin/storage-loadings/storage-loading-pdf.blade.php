<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Storage Loading</title>
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
        <div class="farmer-info">{{ $slPdf->agreement->farmer->name }}</div>
        <div class="farmer-info">{{ $slPdf->agreement->farmer->village_name }}</div>
        <div class="farmer-info">(Farmer ID: {{ $slPdf->agreement->farmer->farmer_id }})</div>
        <div class="report-title">Storage Loading Receipt</div>
        <div class="filters">
            <span>Date: {{ \Carbon\Carbon::now()->format(env('DATE_FORMATE')) }}</span>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Loading Date</th>
                <th>Farmer Id</th>
                <th>Farmer Name</th>
                <th>Farmer Village</th>
                <th>Farmer Phone</th>
                <th>RST No</th>
                <th>Seed Veriety</th>
                <th>Coldstorage</th>
                <th>Total bags</th>
                <th>Received bags</th>
                <th>Pending bags</th>
                <th>Surplus bags</th>
                <th>Net Weight</th>
                <th>Transporter</th>
                <th>Vehicle No.</th>
            </tr>
        </thead>
        <tbody>
        @if( !empty( $slPdf ) )    
            <tr>
                <td class="date-cell">{{ $slPdf->created_at->format(env('DATE_FORMATE')) }}</td>
                <td>{{ $slPdf->agreement->farmer->farmer_id }}</td>
                <td>{{ $slPdf->agreement->farmer->name }}</td>
                <td>{{ $slPdf->agreement->farmer->village_name }}</td>
                <td>{{ $slPdf->agreement->farmerUser->phone }}</td>
                <td>{{ $slPdf->rst_number }}</td>
                <td>{{ $slPdf->agreement->seedVariety->name }}</td>
                <td>{{ $slPdf->coldStorage->name }}</td>
                <td>{{ $slPdf->agreement->bag_quantity }}</td>
                <td>{{ $slPdf->bag_quantity ?? 0 }}</td>
                <td>{{ $slPdf->pending_bags ?? 0 }}</td>
                <td>{{ $slPdf->extra_bags ?? 0 }}</td>
                <td>{{ number_format($slPdf->net_weight, 2) }}</td>
                <td>{{ $slPdf->transporter->name }}</td>
                <td>{{ $slPdf->vehicle_number }}</td>
            </tr>
        @else
            <tr>
                <td colspan="15" class="no-data">No data found</td>
            </tr>
        @endif
        </tbody>
        
    </table>
</body>
</html> 