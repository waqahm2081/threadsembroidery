@extends('admin.layouts.app')

@section('content')
<div class="ad-grid-2">
  <section class="ad-panel">
    <div class="ad-panel-head"><h2>Account details</h2></div>
    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="ad-form-grid">
        <div class="ad-field">
          <label class="ad-label" for="name">Name</label>
          <input class="ad-input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="ad-field">
          <label class="ad-label" for="email">Email</label>
          <input class="ad-input" id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="ad-field">
          <label class="ad-label" for="company_name">Company</label>
          <input class="ad-input" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}">
        </div>
        <div class="ad-field">
          <label class="ad-label" for="phone">Phone</label>
          <input class="ad-input" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
        </div>
        <div class="ad-field full">
          <label class="ad-label" for="avatar">Avatar</label>
          <input class="ad-input" id="avatar" type="file" name="avatar" accept="image/*">
          @if($user->avatar_url)
            <img src="{{ $user->avatar_url }}" alt="" style="width:56px;height:56px;border-radius:50%;object-fit:cover;margin-top:.75rem">
          @endif
        </div>
      </div>
      <button class="ad-btn" type="submit" style="margin-top:1rem">Save profile</button>
    </form>
  </section>

  <section class="ad-panel">
    <div class="ad-panel-head"><h2>Change password</h2></div>
    <form method="POST" action="{{ route('admin.profile.password') }}">
      @csrf
      @method('PUT')
      <div class="ad-field">
        <label class="ad-label" for="current_password">Current password</label>
        <input class="ad-input" id="current_password" type="password" name="current_password" required>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="password">New password</label>
        <input class="ad-input" id="password" type="password" name="password" required minlength="6">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="password_confirmation">Confirm new password</label>
        <input class="ad-input" id="password_confirmation" type="password" name="password_confirmation" required minlength="6">
      </div>
      <button class="ad-btn" type="submit">Update password</button>
    </form>
  </section>
</div>
@endsection
