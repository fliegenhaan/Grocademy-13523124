<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }
    
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $this->authService->registerUser($validatedData);

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginSuccess = $this->authService->loginUser($credentials, $request);

        if ($loginSuccess) {
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'identifier' => 'Kredensial anda tidak valid.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        $this->authService->logoutUser($request);
        return redirect('/');
    }
}
