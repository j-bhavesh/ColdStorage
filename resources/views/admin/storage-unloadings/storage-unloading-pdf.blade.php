<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Storage Unloading</title>
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
        <div class="farmer-info">{{ $sulPdf->unloadingCompany->name }}</div>
        <div class="farmer-info">Contact Person: {{ $sulPdf->unloadingCompany->contact_person }} - {{ $sulPdf->unloadingCompany->contact_number }}</div>
        <div class="farmer-info">Address: {{ $sulPdf->unloadingCompany->address }}</div>
        <div class="report-title">Storage Unloading Receipt</div>
        <div class="filters">
            <span>Date: {{ \Carbon\Carbon::now()->format(env('DATE_FORMATE')) }}</span>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Unloading Date</th>
                <th>Company</th>
                <th>Seed Veriety</th>
                <th>Total bags</th>
                <th>Weight</th>
                <th>Coldstorage</th>
                <th>Transporter</th>
                <th>Vehicle No.</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
        @if( !empty( $sulPdf ) )    
            <tr>
                <td class="date-cell">{{ $sulPdf->created_at->format(env('DATE_FORMATE')) }}</td>
                <td>{{ $sulPdf->unloadingCompany->name }}</td>
                <td>{{ $sulPdf->seedVariety->name }}</td>
                <td>{{ $sulPdf->bag_quantity ?? 0 }}</td>
                <td>{{ number_format($sulPdf->weight, 2) }}</td>
                <td>{{ $sulPdf->coldStorage->name }}</td>
                <td>{{ $sulPdf->transporter->name }}</td>
                <td>{{ $sulPdf->vehicle_number }}</td>
                <td>{{ $sulPdf->location }}</td>
            </tr>
        @else
            <tr>
                <td colspan="9" class="no-data">No data found</td>
            </tr>
        @endif
        </tbody>
        
    </table>
</body>
</html> 