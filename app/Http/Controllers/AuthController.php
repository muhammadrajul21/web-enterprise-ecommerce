<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->onlyInput('email');
        }

        if ($user->status !== 'active') {
            return back()
                ->withErrors([
                    'email' => 'Akun Anda sedang tidak aktif.',
                ])
                ->onlyInput('email');
        }

        Auth::login(
            $user,
            $request->boolean('remember')
        );

        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $customerRole = Role::where('name', 'customer')
            ->firstOrFail();

        $user = User::create([
            'role_id' => $customerRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('customer.account');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(User $user)
    {
        return match ($user->role?->name) {
            'admin' => redirect()->route('admin.dashboard'),

            'staff_gudang' => redirect()->route('staff.dashboard'),

            'owner' => redirect()->route('owner.dashboard'),

            'customer' => redirect()->route('customer.account'),

            default => abort(403, 'Role akun tidak dikenali.'),
        };
    }
}