<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'nombre'   => 'Administrador',
            'email'    => 'admin@correo.com',
            'password' => Hash::make('admin123'),
        ]);

        $lista = [
            ['Teclado mecanico', 'Perifericos', 125000, 25],
            ['Mouse inalambrico', 'Perifericos', 48000, 40],
            ['Monitor 24 pulgadas', 'Pantallas', 520000, 12],
            ['Disco SSD 480GB', 'Almacenamiento', 195000, 18],
        ];

        foreach ($lista as [$nombre, $categoria, $precio, $stock]) {
            Producto::create([
                'nombre' => $nombre,
                'categoria' => $categoria,
                'precio' => $precio,
                'stock' => $stock,
                'usuario_id' => $admin->id,
            ]);
        }
    }
}
