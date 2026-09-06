@extends('admin.layouts.app')

@section('content')
@include('admin.bundles._tabs')

<div class="ad-toolbar">
  <p class="muted" style="margin:0">Master colour palette for bundle garments. Assign colours per garment when editing a bundle.</p>
  <a class="ad-btn" href="{{ route('admin.colours.create') }}"><i class="bi bi-plus-lg"></i> Add colour</a>
</div>

<section class="ad-panel">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>Colour</th>
          <th>Hex</th>
          <th>Sort</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($colours as $colour)
          <tr>
            <td>
              <span style="display:inline-flex;align-items:center;gap:.55rem">
                <span style="width:18px;height:18px;border-radius:50%;background:{{ $colour->hex }};border:1px solid rgba(255,255,255,.15);flex:none"></span>
                <b>{{ $colour->name }}</b>
              </span>
              <div class="muted">{{ $colour->slug }}</div>
            </td>
            <td><code>{{ $colour->hex }}</code></td>
            <td>{{ $colour->sort_order }}</td>
            <td><span class="ad-pill {{ $colour->is_active ? 'completed' : 'cancelled' }}">{{ $colour->is_active ? 'Active' : 'Hidden' }}</span></td>
            <td class="ad-actions">
              <a class="ad-btn ghost sm" href="{{ route('admin.colours.edit', $colour) }}">Edit</a>
              <form method="POST" action="{{ route('admin.colours.destroy', $colour) }}" onsubmit="return confirm('Delete this colour?')">
                @csrf
                @method('DELETE')
                <button class="ad-btn danger sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted">No colours yet. Run the bundle seeder or add colours here.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
