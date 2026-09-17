@extends('emails.layout')

@section('content')
  <h1 style="margin:0 0 8px 0;font-size:22px;line-height:1.3;color:#1a1a1a;">
    Your order has been updated
  </h1>
  <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
    Hi {{ $customerName ?: 'there' }}, we’ve updated your {{ strtolower($orderTypeLabel) }} order
    <strong style="color:#1a1a1a;">{{ $orderNumber }}</strong>.
  </p>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;background:#fff7f2;border:1px solid #f2d4c4;border-radius:8px;">
    <tr>
      <td style="padding:14px 16px;">
        <div style="font-size:12px;color:#8a857d;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Current status</div>
        <div style="font-size:20px;font-weight:bold;color:#e85820;">{{ $statusLabel }}</div>
        @if($trackingNumber)
          <div style="margin-top:10px;font-size:13px;color:#4a4743;">
            Tracking number: <strong style="color:#1a1a1a;">{{ $trackingNumber }}</strong>
          </div>
        @endif
      </td>
    </tr>
  </table>

  @if($status === 'dispatched')
    <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
      Your order is on its way. Keep this tracking number handy so you can follow the delivery.
    </p>
  @elseif($status === 'delivered')
    <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
      Your order has been marked as delivered. We hope everything looks great — thank you for choosing {{ $siteName }}.
    </p>
  @elseif($status === 'cancelled')
    <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
      This order has been cancelled. If you think this is a mistake, please reply to this email and we’ll help.
    </p>
  @else
    <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
      We’ll email you again as your order moves through the next stages.
    </p>
  @endif

  @if(!empty($actionUrl))
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:8px 0 6px 0;">
      <tr>
        <td align="center" bgcolor="#e85820" style="border-radius:8px;">
          <a href="{{ $actionUrl }}" style="display:inline-block;padding:12px 22px;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;border-radius:8px;">
            View your order
          </a>
        </td>
      </tr>
    </table>
  @endif
@endsection
