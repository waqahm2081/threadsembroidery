@extends('emails.layout')

@section('content')
  <h1 style="margin:0 0 12px 0;font-size:22px;line-height:1.3;color:#1a1a1a;">Thanks for your bulk enquiry</h1>
  <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#4a4743;">
    Hi {{ $enquiry->full_name }}, we’ve received your enquiry for <strong>{{ $enquiry->product_title }}</strong>
    (qty {{ number_format($enquiry->quantity) }}). Our team will review the details and get back to you shortly.
  </p>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px 0;background-color:#faf8f5;border:1px solid #ebe6df;border-radius:8px;">
    <tr>
      <td style="padding:16px 18px;">
        <p style="margin:0 0 8px 0;font-size:12px;color:#8a857d;text-transform:uppercase;letter-spacing:.04em;">Reference</p>
        <p style="margin:0 0 14px 0;font-size:16px;font-weight:bold;color:#1a1a1a;">{{ $enquiry->reference }}</p>
        <p style="margin:0 0 4px 0;font-size:14px;color:#4a4743;"><strong>Colour:</strong> {{ $enquiry->colour_name ?: '—' }}</p>
        <p style="margin:0 0 4px 0;font-size:14px;color:#4a4743;"><strong>Sizes:</strong> {{ $enquiry->sizeBreakdownLabel() }}</p>
        <p style="margin:0;font-size:14px;color:#4a4743;"><strong>Decoration:</strong> {{ $enquiry->methodsLabel() }}</p>
      </td>
    </tr>
  </table>

  <p style="margin:0;font-size:13px;line-height:1.6;color:#8a857d;">
    If you need to add anything, reply to this email and include your reference number.
  </p>
@endsection
