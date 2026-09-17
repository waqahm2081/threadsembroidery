@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET" action="{{ route('admin.orders.index') }}">
    <input class="ad-input" type="search" name="q" value="{{ $q }}" placeholder="Order number, email, name…">
    <select class="ad-input" name="status">
      <option value="">All statuses</option>
      @foreach($statuses as $value => $label)
        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="ad-btn ghost" type="submit"><i class="bi bi-search"></i> Filter</button>
  </form>
</div>

<section class="ad-panel">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>Order</th>
          <th>Customer</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td><a href="{{ route('admin.orders.show', $order) }}"><b>{{ $order->order_number }}</b></a></td>
            <td>
              <div>{{ $order->name }}</div>
              <span class="muted">{{ $order->email }}</span>
            </td>
            <td>£{{ number_format($order->total, 2) }}</td>
            <td><span class="ad-pill {{ $order->status }}">{{ \App\Support\OrderFulfillmentStatus::label($order->status) }}</span></td>
            <td>{{ optional($order->created_at)->format('d M Y') }}</td>
            <td><a class="ad-btn ghost sm" href="{{ route('admin.orders.show', $order) }}">View</a></td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">No orders found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="ad-pager">{{ $orders->links() }}</div>
</section>
@endsection
