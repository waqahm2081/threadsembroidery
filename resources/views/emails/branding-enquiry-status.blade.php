@extends('emails.layout')

@section('content')
  <h1 style="margin:0 0 8px 0;font-size:22px;line-height:1.3;color:#1a1a1a;">
    Your branding enquiry has been updated
  </h1>
  <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
    Hi {{ $enquiry->full_name ?: 'there' }}, we’ve updated your branding enquiry
    <strong style="color:#1a1a1a;">{{ $enquiry->reference }}</strong>.
  </p>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;background:#fff7f2;border:1px solid #f2d4c4;border-radius:8px;">
    <tr>
      <td style="padding:14px 16px;">
        <div style="font-size:12px;color:#8a857d;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Current status</div>
        <div style="font-size:20px;font-weight:bold;color:#e85820;">{{ $statusLabel }}</div>
        @if($enquiry->status === 'quoted' && $enquiry->quoted_amount !== null)
          <div style="margin-top:10px;font-size:13px;color:#4a4743;">
            Quoted amount: <strong style="color:#1a1a1a;">£{{ number_format((float) $enquiry->quoted_amount, 2) }}</strong>
          </div>
        @endif
      </td>
    </tr>
  </table>

  @if($enquiry->status === 'quoted')
    <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
      We’ve prepared a quote for your enquiry. Reply to this email if you’d like to proceed or ask any questions.
    </p>
  @elseif($enquiry->status === 'completed')
    <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
      Your enquiry has been marked as completed. Thank you for choosing {{ $siteName }}.
    </p>
  @elseif($enquiry->status === 'cancelled')
    <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
      This enquiry has been cancelled. If you think this is a mistake, reply to this email and we’ll help.
    </p>
  @else
    <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
      We’ll email you again as your enquiry moves through the next stages.
    </p>
  @endif

  <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:8px 0 6px 0;">
    <tr>
      <td align="center" bgcolor="#e85820" style="border-radius:8px;">
        <a href="{{ $portalUrl }}" style="display:inline-block;padding:12px 22px;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;border-radius:8px;">
          View your account
        </a>
      </td>
    </tr>
  </table>
@endsection
