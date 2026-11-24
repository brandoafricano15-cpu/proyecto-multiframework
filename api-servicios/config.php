<?php
// Configuracion del microservicio (misma BD que usa Laravel)
return [
    'db' => [
        'host'    => '127.0.0.1',
        'puerto'  => 3306,
        'nombre'  => 'proyecto_multiframework',
        'usuario' => 'root',
        'clave'   => '',
    ],
    // cambiar esta clave en un proyecto real
    'jwt_secreto'  => 'cambia-esta-clave-por-algo-largo-y-secreto-2025',
    'jwt_duracion' => 3600, // 1 hora
    'cors_origen'  => '*',
];
