@extends('emails.layout')

@section('content')
  <h1 style="margin:0 0 8px 0;font-size:22px;line-height:1.3;color:#1a1a1a;">
    {{ $isAdmin ? 'New uniform shop order received' : 'Thanks for your uniform order!' }}
  </h1>
  <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;color:#4a4743;">
    @if($isAdmin)
      A new school uniform order has been placed. Order reference <strong style="color:#1a1a1a;">{{ $order->order_number }}</strong>.
    @else
      We’ve received your uniform order <strong style="color:#1a1a1a;">{{ $order->order_number }}</strong>. You’ll hear from us as it progresses.
    @endif
  </p>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;background:#fff7f2;border:1px solid #f2d4c4;border-radius:8px;">
    <tr>
      <td style="padding:14px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td style="font-size:12px;color:#8a857d;">
              @if($order->school) School: <strong style="color:#1a1a1a;">{{ $order->school->name }}</strong><br />@endif
              Order total
            </td>
            <td align="right" style="font-size:20px;font-weight:bold;color:#e85820;vertical-align:bottom;">£{{ number_format((float) $order->total, 2) }}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <h2 style="margin:0 0 10px 0;font-size:15px;color:#1a1a1a;">Order items</h2>
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;margin-bottom:18px;">
    <tr>
      <td style="padding:8px 10px;background:#faf8f5;border:1px solid #ebe6df;font-size:11px;color:#8a857d;text-transform:uppercase;">Product</td>
      <td style="padding:8px 10px;background:#faf8f5;border:1px solid #ebe6df;font-size:11px;color:#8a857d;text-transform:uppercase;">Size</td>
      <td style="padding:8px 10px;background:#faf8f5;border:1px solid #ebe6df;font-size:11px;color:#8a857d;text-transform:uppercase;" align="center">Qty</td>
      <td style="padding:8px 10px;background:#faf8f5;border:1px solid #ebe6df;font-size:11px;color:#8a857d;text-transform:uppercase;" align="right">Total</td>
    </tr>
    @foreach($order->items as $item)
    <tr>
      <td style="padding:10px;border:1px solid #ebe6df;font-size:13px;color:#1a1a1a;"><strong>{{ $item->product_name }}</strong></td>
      <td style="padding:10px;border:1px solid #ebe6df;font-size:13px;color:#1a1a1a;">{{ $item->size }}</td>
      <td style="padding:10px;border:1px solid #ebe6df;font-size:13px;color:#1a1a1a;" align="center">{{ $item->quantity }}</td>
      <td style="padding:10px;border:1px solid #ebe6df;font-size:13px;color:#1a1a1a;font-weight:bold;" align="right">£{{ number_format((float) $item->line_total, 2) }}</td>
    </tr>
    @endforeach
  </table>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;margin-bottom:18px;">
    <tr>
      <td style="padding:6px 0;font-size:13px;color:#8a857d;">Subtotal</td>
      <td align="right" style="padding:6px 0;font-size:13px;color:#1a1a1a;">£{{ number_format((float) $order->subtotal, 2) }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;font-size:13px;color:#8a857d;">VAT</td>
      <td align="right" style="padding:6px 0;font-size:13px;color:#1a1a1a;">£{{ number_format((float) $order->vat_amount, 2) }}</td>
    </tr>
    <tr>
      <td style="padding:6px 0;font-size:13px;color:#8a857d;">Delivery</td>
      <td align="right" style="padding:6px 0;font-size:13px;color:#1a1a1a;">£{{ number_format((float) $order->delivery_fee, 2) }}</td>
    </tr>
    <tr>
      <td style="padding:10px 0 0 0;font-size:15px;font-weight:bold;color:#1a1a1a;border-top:2px solid #ebe6df;">Total</td>
      <td align="right" style="padding:10px 0 0 0;font-size:15px;font-weight:bold;color:#e85820;border-top:2px solid #ebe6df;">£{{ number_format((float) $order->total, 2) }}</td>
    </tr>
  </table>

  <h2 style="margin:0 0 10px 0;font-size:15px;color:#1a1a1a;">Customer &amp; delivery</h2>
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;margin-bottom:18px;">
    <tr>
      <td style="padding:10px 12px;border:1px solid #ebe6df;background:#faf8f5;font-size:12px;color:#8a857d;width:140px;">Name</td>
      <td style="padding:10px 12px;border:1px solid #ebe6df;font-size:14px;">{{ $order->customer_name }}</td>
    </tr>
    <tr>
      <td style="padding:10px 12px;border:1px solid #ebe6df;background:#faf8f5;font-size:12px;color:#8a857d;">Email</td>
      <td style="padding:10px 12px;border:1px solid #ebe6df;font-size:14px;">{{ $order->customer_email }}</td>
    </tr>
    @if($order->customer_phone)
    <tr>
      <td style="padding:10px 12px;border:1px solid #ebe6df;background:#faf8f5;font-size:12px;color:#8a857d;">Phone</td>
      <td style="padding:10px 12px;border:1px solid #ebe6df;font-size:14px;">{{ $order->customer_phone }}</td>
    </tr>
    @endif
    @if($order->company_name)
    <tr>
      <td style="padding:10px 12px;border:1px solid #ebe6df;background:#faf8f5;font-size:12px;color:#8a857d;">Company</td>
      <td style="padding:10px 12px;border:1px solid #ebe6df;font-size:14px;">{{ $order->company_name }}</td>
    </tr>
    @endif
    <tr>
      <td style="padding:10px 12px;border:1px solid #ebe6df;background:#faf8f5;font-size:12px;color:#8a857d;">Shipping</td>
      <td style="padding:10px 12px;border:1px solid #ebe6df;font-size:14px;">
        {{ collect([$order->shipping_address_line1, $order->shipping_address_line2, $order->shipping_city, $order->shipping_postcode, $order->shipping_country])->filter()->implode(', ') }}
      </td>
    </tr>
  </table>

  @if($order->notes)
  <p style="margin:0 0 16px 0;font-size:13px;color:#4a4743;"><strong>Notes:</strong> {{ $order->notes }}</p>
  @endif

  <table role="presentation" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" bgcolor="#e85820" style="border-radius:8px;">
        <a href="{{ $actionUrl }}" style="display:inline-block;padding:12px 22px;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">
          {{ $isAdmin ? 'View Order in Admin' : 'View Uniform Orders' }}
        </a>
      </td>
    </tr>
  </table>
@endsection
