<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions Export</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f8f9fa; font-weight: 700; }
    </style>
</head>
<body>
    <h3>Daftar Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Booking Code</th>
                <th>Customer</th>
                <th>Payment Method</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
                <tr>
                    <td>#{{ $t->id }}</td>
                    <td>{{ $t->booking->booking_code ?? 'N/A' }}</td>
                    <td>{{ $t->booking->customer->name ?? $t->booking->customer_name ?? 'N/A' }}</td>
                    <td>{{ ucfirst(str_replace('_',' ', $t->payment_method)) }}</td>
                    <td>{{ number_format($t->total, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($t->status) }}</td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
