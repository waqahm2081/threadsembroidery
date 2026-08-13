@extends('layouts.app')

@section('content')
<main>
  <div class="auth-wrap">
    <div class="auth-side">
      <div class="in">
        <h2>Your Team. Your Portal.</h2>
        <p>Everything saved and waiting — for schools, sports teams and businesses that reorder.</p>
        <ul>
          <li><i class="bi bi-images"></i> Saved artwork &amp; logo library</li>
          <li><i class="bi bi-clock-history"></i> Full order history</li>
          <li><i class="bi bi-arrow-repeat"></i> One-click reorder from previous jobs</li>
          <li><i class="bi bi-shield-lock"></i> Your own secure login — no shared PINs</li>
        </ul>
      </div>
    </div>
    <div class="auth-main"><div class="auth-card">
      <div class="auth-tabs"><button class="active" data-tab="login">Log In</button><button data-tab="signup">Sign Up</button></div>
      <p class="auth-hint muted">Create an account to track bundle orders, artwork and reorders.</p>

      <form id="loginForm" novalidate>
        <div class="field-group"><label class="lbl">Email</label><input class="input" name="email" type="email" required placeholder="you@company.co.uk" autocomplete="email"></div>
        <div class="field-group">
          <label class="lbl">Password</label>
          <div class="password-field">
            <input class="input" name="password" type="password" required placeholder="••••••••" autocomplete="current-password">
            <button type="button" class="password-toggle" aria-label="Show password"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <label class="auth-remember"><input type="checkbox" name="remember" value="1"> Remember me</label>
        <button class="btn btn--block btn--lg" type="submit"><i class="bi bi-box-arrow-in-right"></i> Log In</button>
      </form>

      <form id="signupForm" novalidate style="display:none">
        <div class="field-group"><label class="lbl">Full Name</label><input class="input" name="name" required placeholder="Your name" autocomplete="name"></div>
        <div class="field-group"><label class="lbl">Organisation</label><input class="input" name="org" placeholder="School / club / business" autocomplete="organization"></div>
        <div class="field-group"><label class="lbl">Email</label><input class="input" name="email" type="email" required placeholder="you@company.co.uk" autocomplete="email"></div>
        <div class="field-group">
          <label class="lbl">Password</label>
          <div class="password-field">
            <input class="input" name="password" type="password" required placeholder="Min. 6 characters" autocomplete="new-password">
            <button type="button" class="password-toggle" aria-label="Show password"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <button class="btn btn--block btn--lg" type="submit"><i class="bi bi-person-plus"></i> Create Account</button>
      </form>
    </div></div>
  </div>
</main>
@endsection
