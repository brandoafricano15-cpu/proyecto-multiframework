<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function formulario()
    {
        return view('auth.login');
    }

    public function entrar(Request $request)
    {
        $datos = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($datos, $request->boolean('recordar'))) {
            $request->session()->regenerate();
            return redirect()->route('productos.index');
        }

        return back()->withErrors(['email' => 'Correo o contraseña incorrectos'])->onlyInput('email');
    }

    public function salir(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
