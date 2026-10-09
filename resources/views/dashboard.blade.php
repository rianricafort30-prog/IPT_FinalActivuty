@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-secondary">Online Ordering System overview</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            + Create Order
        </a>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
                ['Categories', $categoryCount, 'categories.index'],
                ['Products', $productCount, 'products.index'],
                ['Customers', $customerCount, 'customers.index'],
                ['Orders', $orderCount, 'orders.index'],
            ] as [$label, $count, $route])
            <div class="col-6 col-lg-3">
                <a href="{{ route($route) }}" class="text-decoration-none">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-secondary">{{ $label }}</div>
                            <div class="display-6 fw-bold text-dark">{{ $count }}</div>
                            <span class="small text-primary">Manage {{ strtolower($label) }} →</span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="{{ route('orders.index') }}">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Order No.</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer->name }}</td>
                                <td>{{ $order->ordered_at->format('M d, Y') }}</td>
                                <td>₱{{ number_format($order->total, 2) }}</td>
                                <td><span class="badge text-bg-secondary">
                                        {{ ucfirst($order->status) }}
                                    </span></td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No orders yet. Create your first order.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection