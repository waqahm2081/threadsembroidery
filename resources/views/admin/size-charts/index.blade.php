@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <p class="muted" style="margin:0">Size charts linked to garments in the bundle builder.</p>
  <a class="ad-btn" href="{{ route('admin.size-guides.create') }}"><i class="bi bi-plus-lg"></i> Add size chart</a>
</div>

<section class="ad-panel">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>Chart</th>
          <th>Key</th>
          <th>Sizes</th>
          <th>Rows</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($guides as $guide)
          <tr>
            <td>
              <b>{{ $guide->name }}</b>
              <div class="muted">{{ $guide->code }} · sort {{ $guide->sort_order }}</div>
            </td>
            <td><code>{{ $guide->key }}</code></td>
            <td>{{ implode(', ', $guide->sizes ?? []) }}</td>
            <td>{{ count($guide->rows ?? []) }}</td>
            <td><span class="ad-pill {{ $guide->is_active ? 'completed' : 'cancelled' }}">{{ $guide->is_active ? 'Active' : 'Hidden' }}</span></td>
            <td class="ad-actions">
              <a class="ad-btn ghost sm" href="{{ route('admin.size-guides.edit', $guide) }}">Edit</a>
              <form method="POST" action="{{ route('admin.size-guides.destroy', $guide) }}" onsubmit="return confirm('Delete this size chart?')">
                @csrf
                @method('DELETE')
                <button class="ad-btn danger sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">No size charts yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
