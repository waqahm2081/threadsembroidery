@extends('emails.layout')

@section('content')
  <h1 style="margin:0 0 12px 0;font-size:22px;line-height:1.3;color:#1a1a1a;">Welcome to Threads, {{ $user->name }}!</h1>
  <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#4a4743;">
    Your account has been created successfully. You can now place bundle orders, manage artwork, and browse the Uniform Shop from your client portal.
  </p>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px 0;background-color:#faf8f5;border:1px solid #ebe6df;border-radius:8px;">
    <tr>
      <td style="padding:16px 18px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td style="font-size:12px;color:#8a857d;text-transform:uppercase;letter-spacing:.04em;padding-bottom:4px;">Account email</td>
          </tr>
          <tr>
            <td style="font-size:15px;color:#1a1a1a;font-weight:bold;">{{ $user->email }}</td>
          </tr>
          @if($user->company_name)
          <tr>
            <td style="font-size:12px;color:#8a857d;text-transform:uppercase;letter-spacing:.04em;padding:12px 0 4px 0;">Organisation</td>
          </tr>
          <tr>
            <td style="font-size:15px;color:#1a1a1a;">{{ $user->company_name }}</td>
          </tr>
          @endif
        </table>
      </td>
    </tr>
  </table>

  <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:8px 0 20px 0;">
    <tr>
      <td align="center" bgcolor="#e85820" style="border-radius:8px;">
        <a href="{{ $portalUrl }}" style="display:inline-block;padding:12px 22px;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">Open Client Portal</a>
      </td>
    </tr>
  </table>

  <p style="margin:0;font-size:13px;line-height:1.6;color:#8a857d;">
    If you have any questions, just reply to this email or contact us at {{ config('site.email') }}.
  </p>
@endsection
