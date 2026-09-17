@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET">
    <select class="ad-input" name="school_id" onchange="this.form.submit()">
      <option value="">All Schools</option>
      @foreach($schools as $s)
        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
      @endforeach
    </select>
    <select class="ad-input" name="status" onchange="this.form.submit()">
      <option value="">All Statuses</option>
      @foreach($statuses as $value => $label)
        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
      @endforeach
    </select>
    <input class="ad-input" name="q" value="{{ request('q') }}" placeholder="Search orders…">
    <button class="ad-btn" type="submit"><i class="bi bi-search"></i></button>
  </form>
</div>

<div class="ad-card">
  <table class="ad-table">
    <thead>
      <tr>
        <th>Order</th>
        <th>Customer</th>
        <th>School</th>
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
          <div>{{ $order->customer_name }}</div>
          <span class="ad-muted" style="font-size:.78rem">{{ $order->customer_email }}</span>
        </td>
        <td>{{ $order->school?->name ?? '—' }}</td>
        <td>{{ $order->items_count }}</td>
        <td><b>£{{ number_format($order->total, 2) }}</b></td>
        <td>
          <span class="ad-badge {{ \App\Support\OrderFulfillmentStatus::badgeClass($order->status) }}">
            {{ \App\Support\OrderFulfillmentStatus::label($order->status) }}
          </span>
        </td>
        <td>{{ $order->created_at->format('d M Y') }}</td>
        <td class="ad-actions">
          <a href="{{ route('admin.school-uniform-orders.show', $order) }}" class="ad-btn sm ghost"><i class="bi bi-eye"></i></a>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" class="ad-empty">No uniform orders found.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($orders->hasPages())
  <div class="ad-pagination">{{ $orders->links('vendor.pagination.admin') }}</div>
  @endif
</div>
@endsection
