@extends('admin.layouts.app')

@section('content')
@php
  $sizes = old('sizes', $guide->sizes ?? ['XS','S','M','L','XL']);
  $rows = old('rows');
  if (!$rows) {
    $rows = collect($guide->rows ?? [['label' => '', 'values' => []]])->map(function ($r) use ($sizes) {
      $values = array_values($r['values'] ?? []);
      while (count($values) < count($sizes)) $values[] = '';
      $values = array_slice($values, 0, count($sizes));
      return ['label' => $r['label'] ?? '', 'values' => $values];
    })->all();
  }
  if ($rows === []) {
    $rows = [['label' => '', 'values' => array_fill(0, count($sizes), '')]];
  }
@endphp

<section class="ad-panel">
  <form method="POST" action="{{ $mode === 'create' ? route('admin.size-guides.store') : route('admin.size-guides.update', $guide) }}" id="sizeChartForm">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div class="ad-form-grid">
      <div class="ad-field">
        <label class="ad-label" for="name">Name</label>
        <input class="ad-input" id="name" name="name" value="{{ old('name', $guide->name) }}" required>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="code">Code</label>
        <input class="ad-input" id="code" name="code" value="{{ old('code', $guide->code) }}" required>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="key">Key</label>
        <input class="ad-input" id="key" name="key" value="{{ old('key', $guide->key) }}" placeholder="auto from code if blank">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="sort_order">Sort order</label>
        <input class="ad-input" id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $guide->sort_order) }}">
      </div>
      <div class="ad-field full">
        <label class="ad-label" for="note">Note</label>
        <textarea class="ad-input" id="note" name="note" rows="2">{{ old('note', $guide->note) }}</textarea>
      </div>
      <div class="ad-field">
        <label class="ad-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $guide->is_active))> Active on storefront</label>
      </div>
    </div>

    <div class="ad-section-title" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap">
      <span>Size chart table</span>
      <div class="ad-actions">
        <button type="button" class="ad-btn ghost sm" id="addSizeCol"><i class="bi bi-plus"></i> Add size</button>
        <button type="button" class="ad-btn ghost sm" id="addMeasureRow"><i class="bi bi-plus"></i> Add row</button>
      </div>
    </div>
    <p class="muted" style="margin:-.35rem 0 1rem">Edit size labels across the top and measurement values in each cell.</p>

    <div class="ad-matrix-wrap">
      <table class="ad-matrix" id="sizeMatrix">
        <thead>
          <tr>
            <th class="sticky-col">Measurement</th>
            @foreach($sizes as $si => $size)
              <th data-col="{{ $si }}">
                <div class="ad-matrix-head">
                  <input class="ad-input sm" name="sizes[{{ $si }}]" value="{{ $size }}" placeholder="Size" required>
                  <button type="button" class="ad-icon-btn" data-remove-col title="Remove size" @disabled(count($sizes) <= 1)>&times;</button>
                </div>
              </th>
            @endforeach
            <th class="ad-matrix-actions"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $ri => $row)
            <tr data-row="{{ $ri }}">
              <td class="sticky-col">
                <input class="ad-input sm" name="rows[{{ $ri }}][label]" value="{{ $row['label'] ?? '' }}" placeholder="e.g. Chest (½)">
              </td>
              @foreach($sizes as $si => $size)
                <td data-col="{{ $si }}">
                  <input class="ad-input sm" name="rows[{{ $ri }}][values][{{ $si }}]" value="{{ $row['values'][$si] ?? '' }}" placeholder="—">
                </td>
              @endforeach
              <td class="ad-matrix-actions">
                <button type="button" class="ad-icon-btn" data-remove-row title="Remove row">&times;</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="ad-form-actions">
      <a class="ad-btn ghost" href="{{ route('admin.size-guides.index') }}">Cancel</a>
      <button class="ad-btn" type="submit">{{ $mode === 'create' ? 'Create size chart' : 'Save size chart' }}</button>
    </div>
  </form>
</section>

<script>
(() => {
  const table = document.getElementById('sizeMatrix');
  if (!table) return;
  const theadRow = table.querySelector('thead tr');
  const tbody = table.querySelector('tbody');

  function colCount() {
    return theadRow.querySelectorAll('th[data-col]').length;
  }

  function reindex() {
    const cols = [...theadRow.querySelectorAll('th[data-col]')];
    cols.forEach((th, si) => {
      th.dataset.col = si;
      const input = th.querySelector('input');
      if (input) input.name = `sizes[${si}]`;
      const btn = th.querySelector('[data-remove-col]');
      if (btn) btn.disabled = cols.length <= 1;
    });

    [...tbody.querySelectorAll('tr')].forEach((tr, ri) => {
      tr.dataset.row = ri;
      const label = tr.querySelector('.sticky-col input');
      if (label) label.name = `rows[${ri}][label]`;
      [...tr.querySelectorAll('td[data-col]')].forEach((td, si) => {
        td.dataset.col = si;
        const input = td.querySelector('input');
        if (input) input.name = `rows[${ri}][values][${si}]`;
      });
    });
  }

  document.getElementById('addSizeCol')?.addEventListener('click', () => {
    const si = colCount();
    const th = document.createElement('th');
    th.dataset.col = si;
    th.innerHTML = `<div class="ad-matrix-head">
      <input class="ad-input sm" name="sizes[${si}]" value="" placeholder="Size" required>
      <button type="button" class="ad-icon-btn" data-remove-col title="Remove size">&times;</button>
    </div>`;
    theadRow.insertBefore(th, theadRow.querySelector('.ad-matrix-actions'));

    tbody.querySelectorAll('tr').forEach((tr, ri) => {
      const td = document.createElement('td');
      td.dataset.col = si;
      td.innerHTML = `<input class="ad-input sm" name="rows[${ri}][values][${si}]" value="" placeholder="—">`;
      tr.insertBefore(td, tr.querySelector('.ad-matrix-actions'));
    });
    reindex();
  });

  document.getElementById('addMeasureRow')?.addEventListener('click', () => {
    const ri = tbody.querySelectorAll('tr').length;
    const tr = document.createElement('tr');
    tr.dataset.row = ri;
    let cells = `<td class="sticky-col"><input class="ad-input sm" name="rows[${ri}][label]" value="" placeholder="e.g. Chest (½)"></td>`;
    for (let si = 0; si < colCount(); si++) {
      cells += `<td data-col="${si}"><input class="ad-input sm" name="rows[${ri}][values][${si}]" value="" placeholder="—"></td>`;
    }
    cells += `<td class="ad-matrix-actions"><button type="button" class="ad-icon-btn" data-remove-row title="Remove row">&times;</button></td>`;
    tr.innerHTML = cells;
    tbody.appendChild(tr);
    reindex();
  });

  table.addEventListener('click', (e) => {
    const removeCol = e.target.closest('[data-remove-col]');
    if (removeCol) {
      if (colCount() <= 1) return;
      const th = removeCol.closest('th[data-col]');
      const idx = [...theadRow.querySelectorAll('th[data-col]')].indexOf(th);
      th?.remove();
      tbody.querySelectorAll('tr').forEach(tr => {
        const cells = tr.querySelectorAll('td[data-col]');
        cells[idx]?.remove();
      });
      reindex();
      return;
    }

    const removeRow = e.target.closest('[data-remove-row]');
    if (removeRow) {
      const rows = tbody.querySelectorAll('tr');
      if (rows.length <= 1) {
        const tr = removeRow.closest('tr');
        tr.querySelectorAll('input').forEach(i => i.value = '');
        return;
      }
      removeRow.closest('tr')?.remove();
      reindex();
    }
  });
})();
</script>
@endsection
