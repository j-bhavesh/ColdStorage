<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Advance Payment Receipt</title>
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
        <div class="farmer-info">{{ $apPdf->farmer->name }}</div>
        <div class="farmer-info">{{ $apPdf->farmer->village_name }}</div>
        <div class="farmer-info">(Farmer ID: {{ $apPdf->farmer->farmer_id }})</div>
        <div class="report-title">Advance Payment Receipt</div>
        <div class="filters">
            <span>Date: {{ \Carbon\Carbon::now()->format(env('DATE_FORMATE')) }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Payment Date</th>
                <th>Farmer Id</th>
                <th>Farmer Name</th>
                <th>Farmer Village</th>
                <th>Farmer Phone</th>
                <th>Amount</th>
                <th>Taken By</th>
                <th>Taken By Name</th>
            </tr>
        </thead>
        <tbody>
        @if( !empty( $apPdf ) )    
            <tr>
                <td class="date-cell">{{ $apPdf->payment_date->format(env('DATE_FORMATE')) }}</td>
                <td>{{ $apPdf->farmer->farmer_id }}</td>
                <td>{{ $apPdf->farmer->name }}</td>
                <td>{{ $apPdf->farmer->village_name }}</td>
                <td>{{ $apPdf->farmer->user->phone }}</td>
                <td>{{ number_format($apPdf->amount, 2) }}</td>
                <td>{{ ucfirst($apPdf->taken_by) }}</td>
                <td>{{ $apPdf->taken_by_name ?? '-' }}</td>
            </tr>
        @else
            <tr>
                <td colspan="8" class="no-data">No data found</td>
            </tr>
        @endif
        </tbody>
        
    </table>
</body>
</html> 