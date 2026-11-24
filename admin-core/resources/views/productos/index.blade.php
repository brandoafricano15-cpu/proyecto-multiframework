@extends('layouts.app')
@section('titulo', 'Productos')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Productos</h3>
    <a href="{{ route('productos.create') }}" class="btn btn-success">+ Nuevo producto</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th><th>Nombre</th><th>Categoría</th>
                    <th>Precio</th><th>Stock</th><th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($productos as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->categoria }}</td>
                    <td>${{ number_format($p->precio, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $p->stock < 10 ? 'bg-danger' : 'bg-success' }}">{{ $p->stock }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('productos.edit', $p) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="{{ route('productos.destroy', $p) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4">No hay productos registrados</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $productos->links('pagination::bootstrap-5') }}</div>
@endsection
