@extends('admin.layouts.app')

@section('content')
<section class="ad-panel">
  <div class="ad-grid-2" style="gap:1.5rem">
    <div>
      <h3 style="margin-top:0">Enquiry details</h3>
      <p><b>Reference:</b> {{ $enquiry->reference }}</p>
      <p><b>Status:</b> {{ $enquiry->displayStatus() }}</p>
      <p><b>Submitted:</b> {{ optional($enquiry->created_at)->format('d M Y H:i') }}</p>
      <hr>
      <p><b>Name:</b> {{ $enquiry->full_name }}</p>
      <p><b>Email:</b> <a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></p>
      <p><b>Phone:</b> {{ $enquiry->phone ?: '—' }}</p>
      <p><b>Company:</b> {{ $enquiry->company_name ?: '—' }}</p>
      <hr>
      <p><b>Product:</b> {{ $enquiry->product_title }}</p>
      <p><b>Style code:</b> {{ $enquiry->product_style_code ?: '—' }}</p>
      <p><b>Colour:</b> {{ $enquiry->colour_name ?: '—' }} @if($enquiry->colour_hex)<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:{{ $enquiry->colour_hex }};vertical-align:middle;margin-left:4px;border:1px solid #ddd"></span>@endif</p>
      <p><b>Quantity:</b> {{ number_format($enquiry->quantity) }}</p>
      <p><b>Sizes:</b> {{ $enquiry->sizeBreakdownLabel() }}</p>
      <p><b>Decoration:</b> {{ $enquiry->methodsLabel() }}</p>
      <p><b>Logo setup:</b> {{ $enquiry->logo_setup ? 'Yes' : 'No' }}</p>
      @if($enquiry->estimated_subtotal !== null)
        <p><b>Est. unit:</b> £{{ number_format((float) $enquiry->estimated_unit_price, 2) }}</p>
        <p><b>Est. subtotal (ex VAT):</b> £{{ number_format((float) $enquiry->estimated_subtotal, 2) }}</p>
      @endif
      @if($enquiry->message)
        <hr>
        <p><b>Customer message</b></p>
        <p style="white-space:pre-wrap">{{ $enquiry->message }}</p>
      @endif
    </div>

    <div>
      <h3 style="margin-top:0">Update</h3>
      <form method="POST" action="{{ route('admin.bulk-enquiries.update', $enquiry) }}">
        @csrf
        @method('PUT')
        <label class="ad-label">Status</label>
        <select class="ad-input" name="status" style="width:100%;margin-bottom:1rem">
          @foreach($statuses as $s)
            <option value="{{ $s }}" @selected($enquiry->status === $s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
        <label class="ad-label">Admin notes</label>
        <textarea class="ad-input" name="admin_notes" rows="8" style="width:100%">{{ old('admin_notes', $enquiry->admin_notes) }}</textarea>
        <div style="margin-top:1rem">
          <button class="ad-btn" type="submit"><i class="bi bi-check2"></i> Save</button>
          <a class="ad-btn ghost" href="{{ route('admin.bulk-enquiries.index') }}"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
