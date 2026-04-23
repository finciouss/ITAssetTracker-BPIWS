<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:200'],
            'email'     => ['required', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'department'=> ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name'       => $data['email'],
            'full_name'  => $data['full_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'department' => $data['department'] ?? null,
        ]);

        $user->assignRole('Staff');

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
