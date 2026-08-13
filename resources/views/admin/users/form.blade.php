@extends('admin.layouts.app')

@section('content')
<section class="ad-panel">
  <form method="POST" action="{{ $mode === 'create' ? route('admin.users.store') : route('admin.users.update', $user) }}" enctype="multipart/form-data">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

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
      <div class="ad-field">
        <label class="ad-label" for="role">Role</label>
        <select class="ad-input" id="role" name="role" required>
          <option value="customer" @selected(old('role', $user->role) === 'customer')>Customer</option>
          <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
        </select>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="password">{{ $mode === 'create' ? 'Password' : 'Set new password' }}</label>
        <input class="ad-input" id="password" type="password" name="password" {{ $mode === 'create' ? 'required' : '' }} minlength="6" placeholder="{{ $mode === 'edit' ? 'Leave blank to keep current' : '' }}">
      </div>
      <div class="ad-field full">
        <label class="ad-label" for="avatar">Avatar</label>
        <input class="ad-input" id="avatar" type="file" name="avatar" accept="image/*">
        @if($user->avatar_url)
          <div class="ad-avatar-preview" style="margin-top:.75rem">
            <img src="{{ $user->avatar_url }}" alt="" style="width:56px;height:56px;border-radius:50%;object-fit:cover">
          </div>
        @endif
      </div>
    </div>

    <div class="ad-form-actions">
      <a class="ad-btn ghost" href="{{ route('admin.users.index') }}">Cancel</a>
      <button class="ad-btn" type="submit">{{ $mode === 'create' ? 'Create user' : 'Save changes' }}</button>
    </div>
  </form>
</section>
@endsection
