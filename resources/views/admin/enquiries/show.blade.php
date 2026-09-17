@extends('admin.layouts.app')

@section('content')
<div class="ad-grid-2">
  <section class="ad-panel">
    <div class="ad-panel-head">
      <h2>Enquiry</h2>
      <span class="ad-pill {{ $enquiry->status }}">{{ $enquiry->displayStatus() }}</span>
    </div>
    <dl class="ad-dl">
      <div><dt>Reference</dt><dd>{{ $enquiry->reference }}</dd></div>
      <div><dt>Name</dt><dd>{{ $enquiry->full_name }}</dd></div>
      <div><dt>Email</dt><dd>{{ $enquiry->email }}</dd></div>
      <div><dt>Phone</dt><dd>{{ $enquiry->phone ?: '—' }}</dd></div>
      <div><dt>Company</dt><dd>{{ $enquiry->company_name ?: '—' }}</dd></div>
      <div><dt>Type</dt><dd>{{ $enquiry->enquiry_type ?: '—' }}</dd></div>
      <div><dt>Quantity</dt><dd>{{ $enquiry->quantity ?: '—' }}</dd></div>
      <div><dt>Method</dt><dd>{{ $enquiry->branding_method ?: '—' }}</dd></div>
      <div><dt>Placements</dt>
        <dd>
          @if(!empty($enquiry->placements))
            {{ is_array($enquiry->placements) ? implode(', ', $enquiry->placements) : $enquiry->placements }}
          @else
            —
          @endif
        </dd>
      </div>
    </dl>

    <h3 style="margin:1.25rem 0 .5rem;font-size:.9rem">Message</h3>
    <p style="white-space:pre-wrap;color:var(--ad-muted)">{{ $enquiry->message ?: '—' }}</p>

    @if($enquiry->files->isNotEmpty())
      <h3 style="margin:1.25rem 0 .5rem;font-size:.9rem">Attached files</h3>
      <ul class="ad-file-list">
        @foreach($enquiry->files as $file)
          <li>
            <a href="{{ $file->url }}" target="_blank" rel="noopener">{{ $file->original_name ?: basename($file->file_path) }}</a>
          </li>
        @endforeach
      </ul>
    @endif
  </section>

  <section class="ad-panel">
    <div class="ad-panel-head"><h2>Admin update</h2></div>
    <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}">
      @csrf
      @method('PUT')
      <div class="ad-field">
        <label class="ad-label" for="status">Status</label>
        <select class="ad-input" id="status" name="status" required>
          @foreach($statuses as $s)
            <option value="{{ $s }}" @selected(old('status', $enquiry->status) === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="quoted_amount">Quoted amount (£)</label>
        <input class="ad-input" id="quoted_amount" type="number" step="0.01" min="0" name="quoted_amount" value="{{ old('quoted_amount', $enquiry->quoted_amount) }}">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="admin_notes">Admin notes</label>
        <textarea class="ad-input" id="admin_notes" name="admin_notes" rows="6">{{ old('admin_notes', $enquiry->admin_notes) }}</textarea>
        <p class="ad-field-hint">Internal only — not emailed to the customer.</p>
      </div>
      <p class="ad-field-hint" style="margin-bottom:.75rem">Changing status (or the quote amount when status is Quoted) emails the customer.</p>
      <button class="ad-btn" type="submit">Save &amp; notify customer</button>
    </form>
  </section>
</div>

<div style="margin-top:1rem">
  <a class="ad-btn ghost" href="{{ route('admin.enquiries.index') }}"><i class="bi bi-arrow-left"></i> Back to enquiries</a>
</div>
@endsection
