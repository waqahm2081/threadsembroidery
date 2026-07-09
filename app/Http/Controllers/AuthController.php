<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');
        $guestSessionId = $request->session()->getId();

        if (! Auth::attempt($credentials, $remember)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid email or password.'], 422);
            }

            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        $userId = Auth::id();
        $request->session()->regenerate();
        $this->cartService->mergeGuestCartForUser($userId, $guestSessionId);

        $redirect = $request->input('redirect', route('user-dashboard'));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Signed in successfully.',
                'redirect' => $redirect,
                'user' => Auth::user()->only(['id', 'name', 'email', 'role']),
            ]);
        }

        return redirect()->intended($redirect);
    }

    public function register(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        $guestSessionId = $request->session()->getId();

        $user = User::create([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_name' => $validated['company_name'] ?? null,
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->cartService->mergeGuestCartForUser($user->id, $guestSessionId);

        $redirect = $request->input('redirect', route('user-dashboard'));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Account created successfully.',
                'redirect' => $redirect,
                'user' => $user->only(['id', 'name', 'email', 'role']),
            ]);
        }

        return redirect($redirect);
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Signed out successfully.']);
        }

        return redirect()->route('home');
    }
}
