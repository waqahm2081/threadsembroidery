@extends('emails.layout')

@section('content')
  <h1 style="margin:0 0 12px 0;font-size:22px;line-height:1.3;color:#1a1a1a;">New user registered</h1>
  <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#4a4743;">
    A new customer account has been created on {{ config('site.name') }}.
  </p>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;margin:16px 0 8px 0;">
    <tr>
      <td style="padding:10px 12px;border:1px solid #ebe6df;background:#faf8f5;font-size:12px;color:#8a857d;width:140px;">Name</td>
      <td style="padding:10px 12px;border:1px solid #ebe6df;font-size:14px;color:#1a1a1a;">{{ $user->name }}</td>
    </tr>
    <tr>
      <td style="padding:10px 12px;border:1px solid #ebe6df;background:#faf8f5;font-size:12px;color:#8a857d;">Email</td>
      <td style="padding:10px 12px;border:1px solid #ebe6df;font-size:14px;color:#1a1a1a;">{{ $user->email }}</td>
    </tr>
    <tr>
      <td style="padding:10px 12px;border:1px solid #ebe6df;background:#faf8f5;font-size:12px;color:#8a857d;">Organisation</td>
      <td style="padding:10px 12px;border:1px solid #ebe6df;font-size:14px;color:#1a1a1a;">{{ $user->company_name ?: '—' }}</td>
    </tr>
    <tr>
      <td style="padding:10px 12px;border:1px solid #ebe6df;background:#faf8f5;font-size:12px;color:#8a857d;">Registered</td>
      <td style="padding:10px 12px;border:1px solid #ebe6df;font-size:14px;color:#1a1a1a;">{{ optional($user->created_at)->format('d M Y, H:i') }}</td>
    </tr>
  </table>

  <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:18px 0 0 0;">
    <tr>
      <td align="center" bgcolor="#e85820" style="border-radius:8px;">
        <a href="{{ $adminUsersUrl }}" style="display:inline-block;padding:12px 22px;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">View Users in Admin</a>
      </td>
    </tr>
  </table>
@endsection
