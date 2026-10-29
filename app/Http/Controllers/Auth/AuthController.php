<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request, CartService $cartService): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Check if login identifier is email or phone number
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $credentials = ['email' => strtolower($loginInput), 'password' => $password];
        } else {
            // Normalize Jordanian phone number (remove spaces, convert +962 to 0)
            $phone = preg_replace('/[^0-9]/', '', $loginInput);
            if (str_starts_with($phone, '962')) {
                $phone = '0'.substr($phone, 3);
            }
            $credentials = ['phone' => $phone, 'password' => $password];
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            // Merge guest cart if any existed before login
            $cartService->getActiveCart();

            return redirect()->intended(route('storefront.home'))
                ->with('success', __('messages.login_success'));
        }

        return back()->withErrors([
            'login' => __('messages.invalid_credentials'),
        ])->onlyInput('login');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request, CartService $cartService): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Normalize phone number
        $phone = preg_replace('/[^0-9]/', '', $request->input('phone'));
        if (str_starts_with($phone, '962')) {
            $phone = '0'.substr($phone, 3);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => strtolower($request->input('email')),
            'phone' => $phone,
            'password' => Hash::make($request->input('password')),
            'role' => 'customer',
            'status' => 'active',
            'locale' => app()->getLocale(),
        ]);

        UserProfile::create([
            'user_id' => $user->id,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // Merge guest cart to new customer cart
        $cartService->getActiveCart();

        return redirect()->intended(route('storefront.home'))
            ->with('success', __('messages.register_success'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.home')
            ->with('success', __('messages.logout_success'));
    }
}
