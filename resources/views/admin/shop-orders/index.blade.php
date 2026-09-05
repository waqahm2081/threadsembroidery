@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET">
    <select class="ad-input" name="status" onchange="this.form.submit()">
      <option value="">All Statuses</option>
      @foreach($statuses as $st)
        <option value="{{ $st }}" @selected($status === $st)>{{ ucfirst($st) }}</option>
      @endforeach
    </select>
    <input class="ad-input" name="q" value="{{ $q }}" placeholder="Search order #, name, email…">
    <button class="ad-btn" type="submit"><i class="bi bi-search"></i></button>
  </form>
</div>

<div class="ad-card">
  <table class="ad-table">
    <thead>
      <tr>
        <th>Order</th>
        <th>Customer</th>
        <th>Items</th>
        <th>Total</th>
        <th>Status</th>
        <th>Date</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $order)
      <tr>
        <td><b>{{ $order->order_number }}</b></td>
        <td>
          <div>{{ $order->name }}</div>
          <span class="ad-muted" style="font-size:.78rem">{{ $order->email }}</span>
        </td>
        <td>{{ $order->items_count }}</td>
        <td><b>£{{ number_format($order->total, 2) }}</b></td>
        <td>
          <span class="ad-badge {{ match($order->status){ 'delivered'=>'green','cancelled'=>'red','shipped','processing'=>'blue',default=>'yellow' } }}">
            {{ ucfirst($order->status) }}
          </span>
        </td>
        <td>{{ optional($order->created_at)->format('d M Y') }}</td>
        <td class="ad-actions">
          <a href="{{ route('admin.shop-orders.show', $order) }}" class="ad-btn sm ghost"><i class="bi bi-eye"></i></a>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="ad-empty">No shop orders found.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($orders->hasPages())
  <div class="ad-pagination">{{ $orders->links() }}</div>
  @endif
</div>
@endsection
