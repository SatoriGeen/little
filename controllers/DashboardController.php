<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../helpers/config.php';

class DashboardController {
    private PDO $db;

    public function __construct(PDO $conexion) {
        $this->db = $conexion;

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login"); exit();
        }
    }

    public function index(): void {
        $id_dept  = (int) ($_GET['dept'] ?? 1);
        $busqueda = trim($_GET['buscar'] ?? '');

        $pagina_actual = max(1, (int) ($_GET['pagina'] ?? 1));
        $limite = defined('PRODUCTOS_POR_PAGINA_VENTAS') ? PRODUCTOS_POR_PAGINA_VENTAS : 8;
        $offset = ($pagina_actual - 1) * $limite;

        $productoModel = new Producto($this->db);

        $total_productos = $productoModel->contarTodos($id_dept, $busqueda);
        $total_paginas   = (int) ceil($total_productos / $limite);

        $productos     = $productoModel->obtenerTodos($id_dept, $busqueda, $limite, $offset);
        $departamentos = $productoModel->obtenerDepartamentos();

        // NUEVO: Resumen global de stock para el widget de alertas del ticket panel
        $resumen_stock = $productoModel->resumenStock(); // sin filtro de depto para vista global

        // Tasa de comisión para el selector de método de pago (JS la usa)
        $comision_tarjeta = defined('COMISION_TARJETA') ? COMISION_TARJETA : 0.025;

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/dashboard.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}