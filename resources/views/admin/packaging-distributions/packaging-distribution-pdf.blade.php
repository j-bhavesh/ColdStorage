<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Packaging Distribution Receipt</title>
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
        <div class="farmer-info">{{ $pdPdf->agreement->farmer->name }}</div>
        <div class="farmer-info">{{ $pdPdf->agreement->farmer->village_name }}</div>
        <div class="farmer-info">(Farmer ID: {{ $pdPdf->agreement->farmer->farmer_id }})</div>
        <div class="report-title">Packaging Distribution Receipt</div>
        <div class="filters">
            <span>Date: {{ \Carbon\Carbon::now()->format(env('DATE_FORMATE')) }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Distribution Date</th>
                <th>Farmer Id</th>
                <th>Farmer Name</th>
                <th>Farmer Village</th>
                <th>Farmer Phone</th>
                <th>Seed Veriety</th>
                <th>Total bags</th>
                <th>Supplied bags</th>
                <th>Pending bags</th>
                <th>Vehicle No.</th>
                <th>Received By</th>
            </tr>
        </thead>
        <tbody>
        @if( !empty( $pdPdf ) )    
            <tr>
                <td class="date-cell">{{ $pdPdf->distribution_date->format(env('DATE_FORMATE')) }}</td>
                <td>{{ $pdPdf->agreement->farmer->farmer_id }}</td>
                <td>{{ $pdPdf->agreement->farmer->name }}</td>
                <td>{{ $pdPdf->agreement->farmer->village_name }}</td>
                <td>{{ $pdPdf->agreement->farmer->user->phone }}</td>
                <td>{{ $pdPdf->agreement->seedVariety->name }}</td>
                <td>{{ $pdPdf->agreement->bag_quantity }}</td>
                <td>{{ $pdPdf->bag_quantity }}</td>
                <td>{{ $pdPdf->pending_bags }}</td>
                <td class="vehicle-cell">{{ $pdPdf->vehicle_number ?? '-' }}</td>
                <td>{{ $pdPdf->received_by ?? '-' }}</td>
            </tr>
        @else
            <tr>
                <td colspan="11" class="no-data">No data found</td>
            </tr>
        @endif
        </tbody>
        
    </table>
</body>
</html> 