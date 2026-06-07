<?php
// controllers/DashboardController.php

require_once 'models/Producto.php';

class DashboardController {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function index() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login");
            exit();
        }

        $id_dept = $_GET['dept'] ?? 1;
        $productoModel = new Producto($this->db);
        
        $productos = $productoModel->obtenerTodos($id_dept);
        
        // NUEVO: Traemos todos los departamentos para dibujar las pestañas
        $departamentos = $productoModel->obtenerDepartamentos();

        require_once 'views/layouts/header.php';
        require_once 'views/dashboard.php';
        require_once 'views/layouts/footer.php';
    }
}