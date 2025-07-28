@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Edit Order')

@section('content')
    <div class="content-header">
        <h1>Edit Order</h1>
    </div>

    <x-adminlte-card>
        <form action="{{ route('adminorder.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')

            <x-adminlte-input name="order_id" label="Order ID" value="{{ $order->id }}" disabled />

            {{-- Tampilkan Produk dalam Order --}}
            <div class="mb-3">
                <label>Products:</label>
                <ul>
                    @foreach ($order->orderDetails as $detail)
                        <li>
                            @if ($detail->collection)
                                {{ $detail->collection->name }} (Collection)
                            @elseif ($detail->customize)
                                {{ $detail->customize->name }} (Customize)
                            @else
                                Unknown Product
                            @endif
                            - Qty: {{ $detail->quantity }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Pilihan Status --}}
            <div class="mb-3">
                <label for="status">Update Status:</label>
                @if ($order->status === 'paid')
                    <select name="status" id="status" class="form-control" required>
                        <option value="shipped">Shipped</option>
                    </select>
                @else
                    <input type="text" class="form-control" value="{{ ucfirst($order->status) }}" disabled>
                    <input type="hidden" name="status" value="{{ $order->status }}">
                    <small class="text-muted">Status hanya bisa diubah jika saat ini adalah <strong>Paid</strong>.</small>
                @endif
            </div>


            <x-adminlte-button type="submit" theme="success" icon="fas fa-save" label="Update" />
            <a href="{{ route('adminorder.index') }}" class="btn btn-secondary ml-2">Cancel</a>
        </form>
    </x-adminlte-card>
@endsection
