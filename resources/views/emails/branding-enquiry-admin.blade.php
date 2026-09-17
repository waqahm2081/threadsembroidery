@extends('emails.layout')

@section('content')
  <h1 style="margin:0 0 12px 0;font-size:22px;line-height:1.3;color:#1a1a1a;">New branding enquiry</h1>
  <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#4a4743;">
    A customer submitted a branding enquiry from the website.
  </p>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px 0;background-color:#faf8f5;border:1px solid #ebe6df;border-radius:8px;">
    <tr>
      <td style="padding:16px 18px;font-size:14px;line-height:1.6;color:#1a1a1a;">
        <p style="margin:0 0 8px 0;"><strong>Reference:</strong> {{ $enquiry->reference }}</p>
        <p style="margin:0 0 8px 0;"><strong>Name:</strong> {{ $enquiry->full_name }}</p>
        <p style="margin:0 0 8px 0;"><strong>Email:</strong> {{ $enquiry->email }}</p>
        <p style="margin:0 0 8px 0;"><strong>Phone:</strong> {{ $enquiry->phone ?: '—' }}</p>
        <p style="margin:0 0 8px 0;"><strong>Company:</strong> {{ $enquiry->company_name ?: '—' }}</p>
        <p style="margin:0 0 8px 0;"><strong>Type:</strong> {{ $enquiry->enquiry_type ?: '—' }}</p>
        <p style="margin:0 0 8px 0;"><strong>Quantity:</strong> {{ $enquiry->quantity ?: '—' }}</p>
        <p style="margin:0 0 8px 0;"><strong>Method:</strong> {{ $enquiry->branding_method ?: '—' }}</p>
        <p style="margin:0 0 8px 0;"><strong>Placements:</strong>
          @if(!empty($enquiry->placements))
            {{ is_array($enquiry->placements) ? implode(', ', $enquiry->placements) : $enquiry->placements }}
          @else
            —
          @endif
        </p>
        @if($enquiry->message)
          <p style="margin:12px 0 0 0;"><strong>Message:</strong><br>{{ $enquiry->message }}</p>
        @endif
        @if($enquiry->files->isNotEmpty())
          <p style="margin:12px 0 0 0;"><strong>Attachments:</strong> {{ $enquiry->files->count() }} file(s)</p>
        @endif
      </td>
    </tr>
  </table>

  <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:8px 0 0 0;">
    <tr>
      <td align="center" bgcolor="#e85820" style="border-radius:8px;">
        <a href="{{ $adminUrl }}" style="display:inline-block;padding:12px 22px;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">View in Admin</a>
      </td>
    </tr>
  </table>
@endsection
