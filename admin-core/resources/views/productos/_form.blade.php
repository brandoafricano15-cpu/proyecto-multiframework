<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}"
           class="form-control @error('nombre') is-invalid @enderror">
    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Categoría</label>
    <input type="text" name="categoria" value="{{ old('categoria', $producto->categoria ?? '') }}"
           class="form-control @error('categoria') is-invalid @enderror">
    @error('categoria') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Precio</label>
        <input type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio ?? '') }}"
               class="form-control @error('precio') is-invalid @enderror">
        @error('precio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}"
               class="form-control @error('stock') is-invalid @enderror">
        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
