@extends('layouts.app')
@section('titulo', 'Iniciar sesión')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-3">Iniciar sesión</h4>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="recordar" class="form-check-input" id="recordar">
                        <label for="recordar" class="form-check-label">Recordarme</label>
                    </div>
                    <button class="btn btn-success w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
