<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Equipment List</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 2px 0;
            font-size: 12px;
            color: #666;
        }
        .status-valid { color: green; }
        .status-expired { color: red; }
        .status-expiring { color: orange; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Equipment List</h1>
        <p>Generated on {{ date('d M, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Serial / Code</th>
                <th>Category</th>
                <th>Brand/Model</th>
                <th>Calibration Expiry</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->code }}</td>
                <td>{{ $item->type->name ?? '-' }}</td>
                <td>{{ $item->brand }} {{ $item->model ? '/ '.$item->model : '' }}</td>
                <td>
                    @if($item->calibration_required && $item->calibration_expiry_date)
                        {{ \Carbon\Carbon::parse($item->calibration_expiry_date)->format('d M, Y') }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if(!$item->calibration_required)
                        N/A
                    @elseif($item->status === 'valid')
                        <span class="status-valid">Valid</span>
                    @elseif($item->status === 'expiring_soon')
                        <span class="status-expiring">Expiring Soon</span>
                    @elseif($item->status === 'expired')
                        <span class="status-expired">Expired</span>
                    @else
                        Pending
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
