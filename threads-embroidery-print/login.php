<?php
$page_title = 'Login / Sign Up | Threads Client Portal';
$page_description = 'Log in to your Threads client portal — saved artwork, order history and one-click reorders.';
$page_active = '';
$page_styles = ['pages.css', 'portal.css'];
$page_scripts = ['portal.js'];
$mount = ['active' => '', 'onReady' => 'THREADS.initLogin'];
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
?>
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
      <div class="demo-hint"><i class="bi bi-info-circle"></i> <span>Demo portal. Use <b>demo@school.co.uk</b> / <b>demo1234</b>, or <button id="fillDemo" style="text-decoration:underline;color:inherit">fill it for me</button>. Real secure logins wire in on launch.</span></div>

      <form id="loginForm" novalidate>
        <div class="field-group"><label class="lbl">Email</label><input class="input" name="email" type="email" required placeholder="you@company.co.uk"></div>
        <div class="field-group"><label class="lbl">Password</label><input class="input" name="password" type="password" required placeholder="••••••••"></div>
        <button class="btn btn--block btn--lg" type="submit"><i class="bi bi-box-arrow-in-right"></i> Log In</button>
      </form>

      <form id="signupForm" novalidate style="display:none">
        <div class="field-group"><label class="lbl">Full Name</label><input class="input" name="name" required placeholder="Your name"></div>
        <div class="field-group"><label class="lbl">Organisation</label><input class="input" name="org" placeholder="School / club / business"></div>
        <div class="field-group"><label class="lbl">Email</label><input class="input" name="email" type="email" required placeholder="you@company.co.uk"></div>
        <div class="field-group"><label class="lbl">Password</label><input class="input" name="password" type="password" required placeholder="Min. 6 characters"></div>
        <button class="btn btn--block btn--lg" type="submit"><i class="bi bi-person-plus"></i> Create Account</button>
      </form>
    </div></div>
  </div>
</main>
<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/scripts.php';
