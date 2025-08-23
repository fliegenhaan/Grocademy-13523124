<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function registerUser(array $validatedData): User
    {
        $validatedData['password'] = Hash::make($validatedData['password']);
        
        $user = User::create($validatedData);

        Auth::login($user);

        return $user;
    }

    public function loginUser(array $credentials, Request $request): bool
    {
        $fieldType = filter_var($credentials['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $authCredentials = [
            $fieldType => $credentials['identifier'],
            'password' => $credentials['password']
        ];
        
        if (Auth::attempt($authCredentials)) {
            $request->session()->regenerate();
            return true;
        }

        return false;
    }

    public function logoutUser(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}