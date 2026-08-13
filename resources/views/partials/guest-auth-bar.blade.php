@guest
<div class="guest-auth-bar">
  <div class="container guest-auth-bar__inner">
    <div class="guest-auth-bar__copy">
      <i class="bi bi-person-badge"></i>
      <p><b>Create a free account</b> to view order history, save artwork and reorder in one click.</p>
    </div>
    <div class="guest-auth-bar__actions">
      <a class="btn btn--sm" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Log In</a>
      <a class="btn btn--ghost btn--sm" href="{{ route('login') }}#signup"><i class="bi bi-person-plus"></i> Register</a>
    </div>
  </div>
</div>
@endguest
