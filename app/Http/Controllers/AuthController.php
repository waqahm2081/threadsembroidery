<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use App\Services\NotificationMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private NotificationMailService $mailNotifications,
    ) {
    }

    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $guestSessionId = $request->session()->getId();

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return $this->failure($request, 'Invalid email or password.');
        }

        $user = Auth::user();
        $request->session()->regenerate();
        $this->cartService->mergeGuestCartForUser($user->id, $guestSessionId);
        $this->claimGuestOrders($user);

        return $this->success($request, 'Welcome back!', route('portal'));
    }

    public function register(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'org' => ['nullable', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(6)],
        ]);

        $guestSessionId = $request->session()->getId();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_name' => $validated['org'] ?? null,
            'role' => User::ROLE_CUSTOMER,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->cartService->mergeGuestCartForUser($user->id, $guestSessionId);
        $this->claimGuestOrders($user);

        $this->mailNotifications->sendUserRegistered($user);

        return $this->success($request, 'Account created successfully.', route('portal'));
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Signed out successfully.',
                'redirect' => route('home'),
            ]);
        }

        return redirect()->route('home');
    }

    private function claimGuestOrders(User $user): void
    {
        Order::query()
            ->whereNull('user_id')
            ->where('email', $user->email)
            ->update(['user_id' => $user->id]);
    }

    private function success(Request $request, string $message, string $redirect): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'redirect' => $redirect,
                'user' => Auth::user()->only(['id', 'name', 'email', 'company_name']),
            ]);
        }

        return redirect()->intended($redirect);
    }

    private function failure(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->withErrors(['email' => $message])->onlyInput('email');
    }
}
