@extends('adminlte::page')

@section('title', 'Data Orders')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@section('content')
    <div class="content-header">
        <h1>{{ __('adminOrder.orderList') }}</h1>
    </div>

    <x-adminlte-card>

        {{-- Tabel --}}
        <x-adminlte-datatable id="orderTable" :heads="[
            'No.',
            'Order ID',
            'User Name',
            'Address',
            'Payment Method',
            'Status',
            'Total Price',
            'Products',
            'Action',
        ]" striped hoverable bordered with-buttons>

            @foreach ($orders as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->user->name ?? '-' }}</td>
                    <td>
                        @php
                            $address = $order->address;
                        @endphp
                        @if ($address)
                            {{ $address->address }},
                            RT {{ $address->rt }}, RW {{ $address->rw }},
                            {{ $address->kelurahan_desa }},
                            {{ $address->kecamatan }},
                            {{ $address->kota_kabupaten }},
                            {{ $address->provinsi }},
                            {{ $address->kode_pos }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $order->payment_method }}</td>
                    <td>
                        <span
                            class="badge
                            @if ($order->status === 'paid') bg-success
                            @elseif ($order->status === 'pending') bg-warning
                            @elseif ($order->status === 'completed') bg-primary
                            @else bg-secondary @endif">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td>
                        <ul class="mb-0 ps-3">
                            @foreach ($order->orderDetails as $detail)
                                <li>
                                    @if ($detail->collection)
                                        {{ $detail->collection->name }} (x{{ $detail->quantity }})
                                    @elseif ($detail->customize)
                                        {{ $detail->customize->name }} (x{{ $detail->quantity }})
                                    @else
                                        -
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        @if ($order->status === 'paid')
                            <form action="{{ route('adminorder.ship', $order->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary"
                                    onclick="return confirm('{{ __('adminOrder.changeStatusToShip') }}')">
                                    <i class="fas fa-shipping-fast"></i> {{ __('adminOrder.ship') }}
                                </button>
                            </form>
                        @else
                            <span class="text-muted">No Action</span>
                        @endif


                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>

    </x-adminlte-card>

    {{-- Tombol Ekspor --}}
    <a href="{{ route('adminorder.export') }}" class="btn btn-export mb-3">
        {{ __('adminOrder.exportToExcel') }}
    </a>

    {{-- Pesan sukses --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" class="mt-3">
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

@endsection
