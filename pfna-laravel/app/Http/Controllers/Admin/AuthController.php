<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($credentials['email'] !== env('ADMIN_EMAIL') || $credentials['password'] !== env('ADMIN_PASSWORD')) {
            return back()->withErrors(['email' => 'Thông tin đăng nhập không đúng.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
