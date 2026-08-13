@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET" action="{{ route('admin.users.index') }}">
    <input class="ad-input" type="search" name="q" value="{{ $q }}" placeholder="Search name, email, company…">
    <select class="ad-input" name="role">
      <option value="">All roles</option>
      <option value="customer" @selected($role === 'customer')>Customer</option>
      <option value="admin" @selected($role === 'admin')>Admin</option>
    </select>
    <button class="ad-btn ghost" type="submit"><i class="bi bi-search"></i> Filter</button>
  </form>
  <a class="ad-btn" href="{{ route('admin.users.create') }}"><i class="bi bi-plus-lg"></i> Add user</a>
</div>

<section class="ad-panel">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Company</th>
          <th>Role</th>
          <th>Joined</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr>
            <td>
              <div class="ad-user-cell">
                <span class="ad-av sm">
                  @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="">
                  @else
                    {{ $user->initials() }}
                  @endif
                </span>
                <div>
                  <b>{{ $user->name }}</b>
                  <span>{{ $user->email }}</span>
                </div>
              </div>
            </td>
            <td>{{ $user->company_name ?: '—' }}</td>
            <td><span class="ad-pill {{ $user->role }}">{{ ucfirst($user->role ?: 'customer') }}</span></td>
            <td>{{ optional($user->created_at)->format('d M Y') }}</td>
            <td class="ad-actions">
              <a class="ad-btn ghost sm" href="{{ route('admin.users.edit', $user) }}">Edit</a>
              @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                  @csrf
                  @method('DELETE')
                  <button class="ad-btn danger sm" type="submit">Delete</button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted">No users found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="ad-pager">{{ $users->links() }}</div>
</section>
@endsection
