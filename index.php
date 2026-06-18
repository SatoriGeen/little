<?php
// index.php — Enrutador principal del sistema POS

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/csrf.php';
require_once __DIR__ . '/helpers/config.php';          // ← NUEVO: constantes globales
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/VentasController.php';
require_once __DIR__ . '/controllers/InventarioController.php';

try {
    $database = new Database();
    $conexion = $database->conectar();
} catch (RuntimeException $e) {
    http_response_code(503);
    die('⚠️ El servicio no está disponible en este momento. Por favor intente más tarde.');
}

$ruta = $_GET['ruta'] ?? 'login';

switch ($ruta) {

    // ── Autenticación ────────────────────────────────────────────────────────
    case 'login':
        $auth = new AuthController($conexion);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_validate();
            $auth->procesarLogin();
        } else {
            $auth->mostrarLogin();
        }
        break;

    case 'logout':
        (new AuthController($conexion))->logout();
        break;

    // ── Dashboard / Ventas ───────────────────────────────────────────────────
    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        (new DashboardController($conexion))->index();
        break;

    case 'agregar_carrito':
        csrf_validate();
        (new VentasController($conexion))->agregarAlCarrito();
        break;

    case 'vaciar_carrito':
        csrf_validate();
        (new VentasController($conexion))->vaciarCarrito();
        break;

    case 'cobrar':
        csrf_validate();
        (new VentasController($conexion))->cobrarTicket();
        break;

    case 'eliminar_item':
        csrf_validate();
        (new VentasController($conexion))->eliminarItem();
        break;

    // ── Inventario ───────────────────────────────────────────────────────────
    case 'inventario':
        (new InventarioController($conexion))->index();
        break;

    case 'inventario_crear':
        (new InventarioController($conexion))->crear();
        break;

    case 'inventario_editar':
        (new InventarioController($conexion))->editar();
        break;

    case 'inventario_guardar':
        csrf_validate();
        (new InventarioController($conexion))->guardar();
        break;

    case 'inventario_eliminar':
        csrf_validate();
        (new InventarioController($conexion))->eliminar();
        break;

    // ── Usuarios ────────────────────────────────────────────────────────────
    case 'usuarios':
        require_once __DIR__ . '/controllers/UsuarioController.php';
        (new UsuarioController($conexion))->index();
        break;

    case 'usuarios_crear':
        require_once __DIR__ . '/controllers/UsuarioController.php';
        (new UsuarioController($conexion))->crear();
        break;

    case 'usuarios_guardar':
        csrf_validate();
        require_once __DIR__ . '/controllers/UsuarioController.php';
        (new UsuarioController($conexion))->guardar();
        break;

    case 'usuarios_eliminar':
        csrf_validate();
        require_once __DIR__ . '/controllers/UsuarioController.php';
        (new UsuarioController($conexion))->eliminar();
        break;

    // ── Configuración ────────────────────────────────────────────────────────
    case 'configuracion':
        require_once __DIR__ . '/controllers/ConfiguracionController.php';
        (new ConfiguracionController($conexion))->index();
        break;

    case 'configuracion_guardar':
        csrf_validate();
        require_once __DIR__ . '/controllers/ConfiguracionController.php';
        (new ConfiguracionController($conexion))->guardar();
        break;

    case 'configuracion_eliminar':
        csrf_validate();
        require_once __DIR__ . '/controllers/ConfiguracionController.php';
        (new ConfiguracionController($conexion))->eliminar();
        break;

    // ── Reportes ────────────────────────────────────────────────────────────
    case 'reportes':
        require_once __DIR__ . '/controllers/ReportesController.php';
        (new ReportesController($conexion))->index();
        break;

    // ── Devoluciones (NUEVO) ─────────────────────────────────────────────────
    case 'devoluciones':
        require_once __DIR__ . '/controllers/DevolucionController.php';
        (new DevolucionController($conexion))->index();
        break;

    case 'devoluciones_procesar':
        csrf_validate();
        require_once __DIR__ . '/controllers/DevolucionController.php';
        (new DevolucionController($conexion))->procesar();
        break;

    // ── Fallback ─────────────────────────────────────────────────────────────
    default:
        header("Location: index.php?ruta=login");
        break;
}