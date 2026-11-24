<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

// ---------- conexion PDO ----------
$dsn = "mysql:host={$config['db']['host']};port={$config['db']['puerto']};dbname={$config['db']['nombre']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['db']['usuario'], $config['db']['clave'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// ---------- helper para responder json ----------
function responder(Response $res, array $datos, int $codigo = 200): Response
{
    $res->getBody()->write(json_encode($datos, JSON_UNESCAPED_UNICODE));
    return $res->withHeader('Content-Type', 'application/json')->withStatus($codigo);
}

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// ---------- CORS ----------
$app->add(function (Request $req, Handler $handler) use ($config) {
    $res = $handler->handle($req);
    return $res
        ->withHeader('Access-Control-Allow-Origin', $config['cors_origen'])
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
});

// respuesta a las peticiones preflight
$app->options('/{ruta:.+}', fn (Request $req, Response $res) => $res->withStatus(204));

// ---------- middleware JWT ----------
$verificarToken = function (Request $req, Handler $handler) use ($config) {
    $cabecera = $req->getHeaderLine('Authorization');

    if (!preg_match('/^Bearer\s+(.+)$/i', $cabecera, $m)) {
        return responder(new Slim\Psr7\Response(), ['error' => 'Token no enviado'], 401);
    }

    try {
        $datos = JWT::decode($m[1], new Key($config['jwt_secreto'], 'HS256'));
    } catch (Throwable $e) {
        return responder(new Slim\Psr7\Response(), ['error' => 'Token invalido o vencido'], 401);
    }

    return $handler->handle($req->withAttribute('usuario', $datos));
};

// ---------- rutas ----------

// POST /api/login  -> devuelve el token
$app->post('/api/login', function (Request $req, Response $res) use ($pdo, $config) {
    $body  = $req->getParsedBody() ?? [];
    $email = trim($body['email'] ?? '');
    $clave = $body['password'] ?? '';

    if ($email === '' || $clave === '') {
        return responder($res, ['error' => 'Email y password son obligatorios'], 400);
    }

    $stmt = $pdo->prepare('SELECT id, nombre, email, password FROM usuarios WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch();

    // la clave esta hasheada con bcrypt por Laravel, password_verify la entiende
    if (!$usuario || !password_verify($clave, $usuario['password'])) {
        return responder($res, ['error' => 'Credenciales incorrectas'], 401);
    }

    $ahora = time();
    $token = JWT::encode([
        'iat'    => $ahora,
        'exp'    => $ahora + $config['jwt_duracion'],
        'sub'    => $usuario['id'],
        'nombre' => $usuario['nombre'],
    ], $config['jwt_secreto'], 'HS256');

    return responder($res, [
        'token'   => $token,
        'usuario' => $usuario['nombre'],
        'expira'  => $config['jwt_duracion'],
    ]);
});

// GET /api/productos  (publica) -> acepta ?buscar= y ?categoria=
$app->get('/api/productos', function (Request $req, Response $res) use ($pdo) {
    $params = $req->getQueryParams();

    $sql = 'SELECT id, nombre, categoria, precio, stock FROM productos WHERE 1=1';
    $valores = [];

    if (!empty($params['buscar'])) {
        $sql .= ' AND nombre LIKE :buscar';
        $valores['buscar'] = '%' . $params['buscar'] . '%';
    }
    if (!empty($params['categoria'])) {
        $sql .= ' AND categoria = :categoria';
        $valores['categoria'] = $params['categoria'];
    }
    $sql .= ' ORDER BY nombre ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($valores);

    $productos = $stmt->fetchAll();
    return responder($res, ['total' => count($productos), 'datos' => $productos]);
});

// GET /api/productos/{id}
$app->get('/api/productos/{id:[0-9]+}', function (Request $req, Response $res, array $args) use ($pdo) {
    $stmt = $pdo->prepare('SELECT id, nombre, categoria, precio, stock FROM productos WHERE id = :id');
    $stmt->execute(['id' => $args['id']]);
    $producto = $stmt->fetch();

    if (!$producto) {
        return responder($res, ['error' => 'Producto no encontrado'], 404);
    }
    return responder($res, ['datos' => $producto]);
});

// GET /api/categorias (publica, sirve para llenar el filtro)
$app->get('/api/categorias', function (Request $req, Response $res) use ($pdo) {
    $cats = $pdo->query('SELECT DISTINCT categoria FROM productos ORDER BY categoria')->fetchAll(PDO::FETCH_COLUMN);
    return responder($res, ['datos' => $cats]);
});

// POST /api/productos/descontar  (protegida con JWT)
$app->post('/api/productos/descontar', function (Request $req, Response $res) use ($pdo) {
    $body     = $req->getParsedBody() ?? [];
    $id       = filter_var($body['id'] ?? null, FILTER_VALIDATE_INT);
    $cantidad = filter_var($body['cantidad'] ?? null, FILTER_VALIDATE_INT);

    if (!$id || !$cantidad || $cantidad < 1) {
        return responder($res, ['error' => 'Debes enviar id y cantidad (enteros mayores a 0)'], 400);
    }

    // el UPDATE solo se hace si alcanza el stock
    $stmt = $pdo->prepare('UPDATE productos SET stock = stock - :cant, updated_at = NOW()
                           WHERE id = :id AND stock >= :cant2');
    $stmt->execute(['cant' => $cantidad, 'cant2' => $cantidad, 'id' => $id]);

    if ($stmt->rowCount() === 0) {
        $existe = $pdo->prepare('SELECT stock FROM productos WHERE id = :id');
        $existe->execute(['id' => $id]);
        $fila = $existe->fetch();

        if (!$fila) {
            return responder($res, ['error' => 'Producto no encontrado'], 404);
        }
        return responder($res, [
            'error' => 'Stock insuficiente',
            'stock_actual' => (int) $fila['stock'],
        ], 409);
    }

    $nuevo = $pdo->prepare('SELECT id, nombre, stock FROM productos WHERE id = :id');
    $nuevo->execute(['id' => $id]);

    return responder($res, ['mensaje' => 'Stock actualizado', 'datos' => $nuevo->fetch()], 200);
})->add($verificarToken);

// ---------- errores ----------
$app->addErrorMiddleware(true, true, true);

$app->run();
