@extends('admin.layouts.app')

@section('content')
@include('admin.bundles._tabs')

<div class="ad-toolbar">
  <p class="muted" style="margin:0">Placement add-ons available in the bundle builder.</p>
  <a class="ad-btn" href="{{ route('admin.placements.create') }}"><i class="bi bi-plus-lg"></i> Add placement</a>
</div>

<section class="ad-panel">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>Placement</th>
          <th>Method</th>
          <th>Price</th>
          <th>Sizes</th>
          <th>Included</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($placements as $placement)
          <tr>
            <td>
              <b>{{ $placement->label }}</b>
              <div class="muted">{{ $placement->slug }} · sort {{ $placement->sort_order }}</div>
            </td>
            <td>{{ ucfirst($placement->method) }}</td>
            <td>£{{ number_format($placement->price, 2) }}</td>
            <td>{{ implode(', ', $placement->sizes ?? []) ?: '—' }}</td>
            <td>{{ $placement->included_in_bundle ? 'Yes' : 'No' }}</td>
            <td><span class="ad-pill {{ $placement->is_active ? 'completed' : 'cancelled' }}">{{ $placement->is_active ? 'Active' : 'Hidden' }}</span></td>
            <td class="ad-actions">
              <a class="ad-btn ghost sm" href="{{ route('admin.placements.edit', $placement) }}">Edit</a>
              <form method="POST" action="{{ route('admin.placements.destroy', $placement) }}" onsubmit="return confirm('Delete this placement?')">
                @csrf
                @method('DELETE')
                <button class="ad-btn danger sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="muted">No placements yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
