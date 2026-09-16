@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET" action="{{ route('admin.bulk-enquiries.index') }}">
    <input class="ad-input" type="search" name="q" value="{{ $q }}" placeholder="Reference, name, email, product…">
    <select class="ad-input" name="status">
      <option value="">All statuses</option>
      @foreach($statuses as $s)
        <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
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
          <th>Reference</th>
          <th>Customer</th>
          <th>Product</th>
          <th>Qty</th>
          <th>Status</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($enquiries as $enquiry)
          <tr>
            <td><a href="{{ route('admin.bulk-enquiries.show', $enquiry) }}"><b>{{ $enquiry->reference }}</b></a></td>
            <td>
              <div>{{ $enquiry->full_name }}</div>
              <span class="muted">{{ $enquiry->email }}</span>
            </td>
            <td>
              <div>{{ $enquiry->product_title }}</div>
              <span class="muted">{{ $enquiry->colour_name ?: '—' }}</span>
            </td>
            <td>{{ number_format($enquiry->quantity) }}</td>
            <td><span class="ad-pill {{ $enquiry->status }}">{{ $enquiry->displayStatus() }}</span></td>
            <td>{{ optional($enquiry->created_at)->format('d M Y') }}</td>
            <td><a class="ad-btn ghost sm" href="{{ route('admin.bulk-enquiries.show', $enquiry) }}">View</a></td>
          </tr>
        @empty
          <tr><td colspan="7" class="muted">No bulk enquiries found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="ad-pager">{{ $enquiries->links() }}</div>
</section>
@endsection
