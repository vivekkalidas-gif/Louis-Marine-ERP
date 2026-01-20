<!DOCTYPE html>
<html>
<head>
    <title>Inventory List</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Louis Marine - Inventory List</h2>
        <p>Generated on: {{ date('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Item Name</th>
                <th>Brand/Model</th>
                <th>Unit</th>
                <th>Stock</th>
                <th>Min</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->category->name ?? '-' }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->brand }} {{ $item->model ? '('.$item->model.')' : '' }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->current_stock }}</td>
                <td>{{ $item->minimum_stock_level }}</td>
                <td>{{ ucfirst($item->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
