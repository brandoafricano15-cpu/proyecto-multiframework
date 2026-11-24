@extends('layouts.app')
@section('titulo', 'Editar producto')

@section('contenido')
<div class="card shadow-sm col-md-7 mx-auto">
    <div class="card-body">
        <h4 class="mb-3">Editar producto #{{ $producto->id }}</h4>
        <form method="POST" action="{{ route('productos.update', $producto) }}">
            @csrf @method('PUT')
            @include('productos._form')
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-primary">Actualizar</button>
        </form>
    </div>
</div>
@endsection
