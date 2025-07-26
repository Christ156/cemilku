@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <!-- Jumlah Snack -->
        <div class="col-md-3 mb-3">
            <x-adminlte-small-box title="Snack Count" text="{{ $snackCount ?? 0 }}" icon="fas fa-cookie-bite"
                class="dashboard-box1" />
        </div>

        <!-- Jumlah Collection -->
        <div class="col-md-3 mb-3">
            <x-adminlte-small-box title="Collection Count" text="{{ $collectionCount ?? 0 }}" icon="fas fa-gift"
                theme="success" class="dashboard-box2" />
        </div>

        <!-- Jumlah Order -->
        <div class="col-md-3 mb-3">
            <x-adminlte-small-box title="Order Count" text="{{ $orderCount ?? 0 }}" icon="fas fa-shopping-cart"
                theme="warning" class="dashboard-box3" />
        </div>

        <!-- Jumlah Pelanggan -->
        <div class="col-md-3 mb-3">
            <x-adminlte-small-box title="User Count" text="{{ $userCount ?? 0 }}" icon="fas fa-users" theme="danger"
                class="dashboard-box4" />
        </div>
    </div>

    <div class="row">
        <!-- Collection Terlaris Minggu Ini -->
        <div class="col-md-6 dashboard-weekly">
            <x-adminlte-card title="This Week's Best Selling Collection" theme="primary" icon="fas fa-star">
                <ul class="list-group list-group-flush">
                    @forelse ($topCollections->filter(fn($c) => $c->total_sold > 0) as $collection)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $collection->name }}
                            <span class="badge badge-primary badge-pill">{{ $collection->total_sold }} sold</span>
                        </li>
                    @empty
                        <li class="list-group-item">There are no sales this week</li>
                    @endforelse
                </ul>

            </x-adminlte-card>
        </div>

        <!-- Grafik Penjualan 7 Hari Terakhir -->
        <div class="col-md-6">
            <x-adminlte-card title="Last 7 Days Sales" theme="info" icon="fas fa-chart-line">
                <canvas id="salesChart"></canvas>
            </x-adminlte-card>
        </div>
    </div>

    <!-- Pesanan Terbaru -->
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card title="Latest Orders" theme="light" icon="fas fa-clock">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? '-' }}</td>
                                <td><span class="badge badge-success">{{ ucfirst($order->status) }}</span></td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Tidak ada pesanan terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($salesChart['labels']) !!},
                datasets: [{
                    label: 'Number of Collections Sold',
                    data: {!! json_encode($salesChart['data']) !!},
                    fill: true,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        });
    </script>
@stop
