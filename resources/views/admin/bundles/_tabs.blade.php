@php $tab = $tab ?? 'bundles'; @endphp
<div class="ad-tabs">
  <a href="{{ route('admin.bundles.index') }}" class="{{ $tab === 'bundles' ? 'active' : '' }}">Bundles</a>
  <a href="{{ route('admin.placements.index') }}" class="{{ $tab === 'placements' ? 'active' : '' }}">Placements</a>
  <a href="{{ route('admin.colours.index') }}" class="{{ $tab === 'colours' ? 'active' : '' }}">Colours</a>
</div>
