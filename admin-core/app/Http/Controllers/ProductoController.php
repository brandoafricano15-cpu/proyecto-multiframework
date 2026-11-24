<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('id', 'desc')->paginate(10);
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['usuario_id'] = $request->user()->id;

        Producto::create($datos);

        return redirect()->route('productos.index')->with('ok', 'Producto creado correctamente');
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $producto->update($this->validar($request));

        return redirect()->route('productos.index')->with('ok', 'Producto actualizado');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('ok', 'Producto eliminado');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre'    => 'required|string|max:120',
            'categoria' => 'required|string|max:60',
            'precio'    => 'required|numeric|min:0',
            'stock'     => 'required|integer|min:0',
        ]);
    }
}
