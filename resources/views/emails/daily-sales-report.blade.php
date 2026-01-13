<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Daily sales report</title>
    </head>
    <body>
        <h1>Daily sales report</h1>
        <p>Date: {{ $reportDate }}</p>

        @if ($sales->isEmpty())
            <p>No products were sold today.</p>
        @else
            <table cellpadding="6" cellspacing="0" border="1">
                <thead>
                    <tr>
                        <th align="left">Product</th>
                        <th align="right">Quantity</th>
                        <th align="right">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr>
                            <td>{{ $sale->product_name }}</td>
                            <td align="right">{{ $sale->total_quantity }}</td>
                            <td align="right">${{ number_format((float) $sale->total_revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p>Total revenue: ${{ number_format((float) $totalRevenue, 2) }}</p>
        @endif
    </body>
</html>
