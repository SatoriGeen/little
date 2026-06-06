<?php
// index.php

// 1. Iniciamos la sesión
session_start();

// 2. Requerimos las dependencias principales
require_once 'config/database.php';
require_once 'controllers/AuthController.php';

// 3. Inicializamos la base de datos
$database = new Database();
$conexion = $database->conectar();

// 4. Lógica de Enrutamiento (Router manual)
$ruta = $_GET['ruta'] ?? 'login';

switch ($ruta) {
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