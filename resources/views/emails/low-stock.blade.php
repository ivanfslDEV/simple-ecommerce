<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Low stock alert</title>
    </head>
    <body>
        <h1>Low stock alert</h1>
        <p>The product "{{ $product->name }}" is running low.</p>
        <p>Remaining stock: {{ $remaining }}</p>
        <p>Threshold: {{ $threshold }}</p>
    </body>
</html>
