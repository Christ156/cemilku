<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>

    <h2>Keranjang Belanja</h2>

    @if ($cart && $cart->cartItems && $cart->cartItems->count())

        <ul>
            @foreach ($cart->cartItems as $item)
                <li>
                    <strong>Nama Produk:</strong> {{ $item->collection->name ?? 'Produk tidak ditemukan' }}<br>
                    <strong>Id:</strong> {{ $item->id }}<br>
                    <strong>Harga Satuan:</strong> Rp{{ number_format($item->price, 0, ',', '.') }}<br>
                    <strong>Jumlah:</strong> {{ $item->quantity }}<br>
                    <strong>Total Harga:</strong> Rp{{ number_format($item->total_price, 0, ',', '.') }}<br>

                    @if (!empty($item->collection->image))
                        <img src="{{ asset('assets/collections/' . $item->collection->image) }}" width="100">
                    @endif
                </li>
                <hr>
            @endforeach
        </ul>
    @else
        <p>Tidak ada item di keranjang.</p>
    @endif

    <a href="{{ route('checkout') }}">anjinx</a>

</body>

</html>
