<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login | Threads</title>
<link rel="icon" href="{{ asset('assets/img/logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="ad-login">
  <div class="ad-login-card">
    <div class="ad-login-brand">
      <img src="{{ asset('assets/img/logo.png') }}" alt="Threads">
      <span>Admin Console</span>
    </div>
    <h1>Welcome back</h1>
    <p>Sign in to manage customers, orders and branding enquiries.</p>

    @if($errors->any())
      <div class="ad-flash err" style="margin-bottom:1rem">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
      @csrf
      <div class="ad-field">
        <label class="ad-label" for="email">Email</label>
        <input class="ad-input" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="admin@company.co.uk">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="password">Password</label>
        <input class="ad-input" id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
      </div>
      <label style="display:flex;align-items:center;gap:.5rem;margin:0 0 1.2rem;color:var(--ad-muted);font-size:.86rem">
        <input type="checkbox" name="remember" value="1"> Remember me
      </label>
      <button class="ad-btn block" type="submit"><i class="bi bi-shield-lock"></i> Sign in to Admin</button>
    </form>
  </div>
</div>
</body>
</html>
