@extends('admin.layouts.app')

@section('content')
@include('admin.bundles._tabs')

<div class="ad-toolbar">
  <p class="muted" style="margin:0">Packages shown on the storefront bundles page and homepage.</p>
  <a class="ad-btn" href="{{ route('admin.bundles.create') }}"><i class="bi bi-plus-lg"></i> Add bundle</a>
</div>

<section class="ad-panel">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>Bundle</th>
          <th>Price</th>
          <th>Methods</th>
          <th>Items</th>
          <th>Flags</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($bundles as $bundle)
          <tr>
            <td>
              <div>
                <b>{{ $bundle->name }}</b>
                <div class="muted">{{ $bundle->slug }} · sort {{ $bundle->sort_order }}</div>
              </div>
            </td>
            <td>£{{ number_format($bundle->price, 2) }}</td>
            <td>{{ implode(', ', $bundle->methods ?? []) }}</td>
            <td>{{ $bundle->garments_count }} garments · {{ $bundle->includes_count }} includes</td>
            <td>
              @if($bundle->featured)<span class="ad-pill processing">Featured</span>@endif
              @if($bundle->popular)<span class="ad-pill quoted">Popular</span>@endif
              @if($bundle->badge)<span class="ad-chip">{{ $bundle->badge }}</span>@endif
            </td>
            <td>
              <span class="ad-pill {{ $bundle->is_active ? 'completed' : 'cancelled' }}">
                {{ $bundle->is_active ? 'Active' : 'Hidden' }}
              </span>
            </td>
            <td class="ad-actions">
              <a class="ad-btn ghost sm" href="{{ route('admin.bundles.edit', $bundle) }}">Edit</a>
              <form method="POST" action="{{ route('admin.bundles.destroy', $bundle) }}" onsubmit="return confirm('Delete this bundle?')">
                @csrf
                @method('DELETE')
                <button class="ad-btn danger sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="muted">No bundles yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
