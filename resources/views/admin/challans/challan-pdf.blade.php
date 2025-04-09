<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Challan</title>
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
        <div class="farmer-info">{{ $challans->farmer->name }}</div>
        <div class="farmer-info">{{ $challans->farmer->village_name }}</div>
        <div class="farmer-info">(Farmer ID: {{ $challans->farmer->farmer_id }})</div>
        <div class="report-title">Challan</div>
        <div class="filters">
            <span>Date: {{ \Carbon\Carbon::parse($challans->created_at)->format(env('DATE_FORMATE')) }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Challan No</th>
                <th>DATE</th>
                <th>Farmer Id</th>
                <th>Farmer Name</th>
                <th>Farmer Phone</th>
                <th>Farmer Village</th>
                <th>VEHICLE NO.</th>
            </tr>
        </thead>
        <tbody>
            
        <tr>
            <td>123</td>
            <td class="date-cell">{{ $challans->created_at->format(env('DATE_FORMATE')) }}</td>
            <td>{{ $challans->farmer->farmer_id }}</td>
            <td>{{ $challans->farmer->name }}</td>
            <td>{{ $challans->farmer->user->phone }}</td>
            <td>{{ $challans->farmer->village_name }}</td>
            <td class="vehicle-cell">{{ $challans->vehicle_number ?? '-' }}</td>
        </tr>
            {{-- @forelse($challans as $farmerChallan)
            @empty
                <tr>
                    <td colspan="15" class="no-data">No data found</td>
                </tr>
            @endforelse --}}
        </tbody>
        
    </table>
</body>
</html> 