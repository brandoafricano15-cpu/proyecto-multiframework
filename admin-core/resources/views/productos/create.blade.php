@extends('layouts.app')
@section('titulo', 'Nuevo producto')

@section('contenido')
<div class="card shadow-sm col-md-7 mx-auto">
    <div class="card-body">
        <h4 class="mb-3">Nuevo producto</h4>
        <form method="POST" action="{{ route('productos.store') }}">
            @csrf
            @include('productos._form')
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-success">Guardar</button>
        </form>
    </div>
</div>
@endsection
