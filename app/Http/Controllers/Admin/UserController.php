<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $role = $request->get('role');

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('company_name', 'like', "%{$q}%");
                });
            })
            ->when(in_array($role, [User::ROLE_ADMIN, User::ROLE_CUSTOMER], true), fn ($query) => $query->where('role', $role))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'title' => 'Users',
            'subtitle' => 'Manage customers and admin accounts.',
            'nav' => 'users',
            'users' => $users,
            'q' => $q,
            'role' => $role,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'title' => 'Add User',
            'subtitle' => 'Create a customer or admin account.',
            'nav' => 'users',
            'user' => new User(['role' => User::ROLE_CUSTOMER]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $user = new User;
        $this->fillUser($user, $validated, true);
        $user->save();

        if ($request->hasFile('avatar')) {
            $user->avatar_path = $request->file('avatar')->store('avatars/'.$user->id, 'public');
            $user->save();
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'title' => 'Edit User',
            'subtitle' => $user->email,
            'nav' => 'users',
            'user' => $user,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validated($request, $user);

        if (
            $user->isAdmin()
            && ($validated['role'] ?? '') !== User::ROLE_ADMIN
            && User::query()->where('role', User::ROLE_ADMIN)->count() <= 1
        ) {
            return back()->with('error', 'You cannot demote the last admin account.');
        }

        $this->fillUser($user, $validated, false);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars/'.$user->id, 'public');
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdmin() && User::query()->where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('error', 'You cannot delete the last admin account.');
        }

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        if ($request->input('password') === '' || $request->input('password') === null) {
            $request->request->remove('password');
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'company_name' => ['nullable', 'string', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_CUSTOMER])],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function fillUser(User $user, array $validated, bool $creating): void
    {
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->company_name = $validated['company_name'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        } elseif ($creating) {
            $user->password = Hash::make(str()->random(16));
        }
    }
}
