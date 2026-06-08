<?php
// index.php

// 1. Iniciamos la sesión
session_start();

// 2. Requerimos las dependencias principales
require_once 'config/database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/VentasController.php';
require_once 'controllers/InventarioController.php'; // <-- Nuevo

// 3. Inicializamos la base de datos
$database = new Database();
$conexion = $database->conectar();

// 4. Lógica de Enrutamiento (Router manual)
$ruta = $_GET['ruta'] ?? 'login';

switch ($ruta) {
    case 'agregar_carrito':
        // AHORA PASAMOS $conexion AL CONTROLADOR
        $ventas = new VentasController($conexion);
        $ventas->agregarAlCarrito();
        break;

    case 'vaciar_carrito':
        $ventas = new VentasController($conexion);
        $ventas->vaciarCarrito();
        break;

    case 'cobrar':
        // NUEVA RUTA PARA PROCESAR LA VENTA
        $ventas = new VentasController($conexion);
        $ventas->cobrarTicket();
        break;
        
    case 'inventario':
        $inventario = new InventarioController($conexion);
        $inventario->index();
        break;

    case 'inventario_crear':
        $inventario = new InventarioController($conexion);
        $inventario->crear();
        break;

    case 'inventario_editar':
        $inventario = new InventarioController($conexion);
        $inventario->editar();
        break;

    case 'eliminar_item':
        $ventas = new VentasController($conexion);
        $ventas->eliminarItem();
        break;

    case 'usuarios':
        require_once 'controllers/UsuarioController.php';
        $uController = new UsuarioController($conexion);
        $uController->index();
        break;

    case 'usuarios_crear':
        require_once 'controllers/UsuarioController.php';
        $uController = new UsuarioController($conexion);
        $uController->crear();
        break;

    case 'usuarios_guardar':
        require_once 'controllers/UsuarioController.php';
        $uController = new UsuarioController($conexion);
        $uController->guardar();
        break;

    // --- MÓDULO DE CONFIGURACIÓN Y CATÁLOGOS ---
    case 'configuracion':
        require_once 'controllers/ConfiguracionController.php';
        $configController = new ConfiguracionController($conexion);
        $configController->index();
        break;
        
    case 'configuracion_guardar':
        require_once 'controllers/ConfiguracionController.php';
        $configController = new ConfiguracionController($conexion);
        $configController->guardar();
        break;
        
    case 'configuracion_eliminar':
        require_once 'controllers/ConfiguracionController.php';
        $configController = new ConfiguracionController($conexion);
        $configController->eliminar();
        break;

    case 'reportes':
        require_once 'controllers/ReportesController.php';
        $reportes = new ReportesController($conexion);
        $reportes->index();
        break;

    case 'inventario_guardar':
        $inventario = new InventarioController($conexion);
        $inventario->guardar();
        break;

    case 'inventario_eliminar':
        $inventario = new InventarioController($conexion);
        $inventario->eliminar();
        break;

    case 'login':
        $auth = new AuthController($conexion);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $auth->procesarLogin();
        } else {
            $auth->mostrarLogin();
        }
        break;

    case 'logout':
        $auth = new AuthController($conexion);
        $auth->logout();
        break;

    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $dashboard = new DashboardController($conexion);
        $dashboard->index();
        break;
    default:
        header("Location: index.php?ruta=login");
        break;
}