<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $subject ?? config('site.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f1ee;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;-webkit-text-size-adjust:100%;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f1ee;padding:24px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e6e2dc;">
          {{-- Header --}}
          <tr>
            <td align="center" style="background-color:#e85820;padding:22px 24px;">
              <img src="{{ $logoUrl }}" alt="{{ config('site.name') }}" width="180" style="display:block;max-width:180px;height:auto;border:0;" />
            </td>
          </tr>
          {{-- Accent bar --}}
          <tr>
            <td style="background-color:#c4471a;height:4px;font-size:0;line-height:0;">&nbsp;</td>
          </tr>
          {{-- Body --}}
          <tr>
            <td style="padding:32px 28px 24px 28px;">
              @yield('content')
            </td>
          </tr>
          {{-- Footer --}}
          <tr>
            <td style="background-color:#161719;padding:22px 28px;color:#a8a49d;font-size:12px;line-height:1.6;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="color:#f5f3f0;font-size:14px;font-weight:bold;padding-bottom:8px;">{{ config('site.name') }}</td>
                </tr>
                <tr>
                  <td style="color:#a8a49d;font-size:12px;">
                    {{ config('site.address') }}<br />
                    <a href="tel:{{ config('site.phone_tel') }}" style="color:#ff7a45;text-decoration:none;">{{ config('site.phone') }}</a>
                    &nbsp;·&nbsp;
                    <a href="mailto:{{ config('site.email') }}" style="color:#ff7a45;text-decoration:none;">{{ config('site.email') }}</a><br />
                    {{ config('site.hours') }}
                  </td>
                </tr>
                <tr>
                  <td style="color:#6f6b65;font-size:11px;padding-top:14px;">
                    © {{ date('Y') }} {{ config('site.legal_name') }}. All rights reserved.
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
