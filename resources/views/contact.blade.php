@extends('layouts.app')

@section('content')
<main>
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>Contact</span></div>
    <span class="h-eyebrow">Get In Touch</span>
    <h1 class="mt-2">We'd Love to Hear From You</h1>
    <p class="lead">Questions, quotes or a quick chat about your branding — reach out and we'll get straight back to you.</p>
  </div></section>

  <section class="section section--tight"><div class="container">
    <div class="contact-grid">
      @include('partials.contact-cards')
      <div class="form-card" data-reveal data-delay="1">
        <h3 class="h-3" style="margin-bottom:1.2rem">Send a Message</h3>
        @php $profile = auth()->user(); @endphp
        <form data-enquiry="contact" novalidate>
          <div class="field-group two">
            <div><label class="lbl">Full Name <span class="req">*</span></label><input class="input" name="name" required placeholder="Your name" value="{{ old('name', $profile?->name) }}" autocomplete="name"><div class="err-msg">Please enter your name.</div></div>
            <div><label class="lbl">Email <span class="req">*</span></label><input class="input" type="email" name="email" required placeholder="you@email.com" value="{{ old('email', $profile?->email) }}" autocomplete="email"><div class="err-msg">Enter a valid email.</div></div>
          </div>
          <div class="field-group"><label class="lbl">Subject</label><input class="input" name="subject" placeholder="What's it about?"></div>
          <div class="field-group"><label class="lbl">Message <span class="req">*</span></label><textarea class="textarea" name="message" required placeholder="How can we help?"></textarea><div class="err-msg">Please enter a message.</div></div>
          <button class="btn btn--block btn--lg" type="submit"><i class="bi bi-send"></i> Send Message</button>
        </form>
      </div>
    </div>
  </div></section>
</main>
@endsection
