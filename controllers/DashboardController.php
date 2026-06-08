<?php
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
        $busqueda = trim($_GET['buscar'] ?? ''); 
        
        // Configuración de Paginación
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $limite = 8; // Productos por página en Ventas
        $offset = ($pagina_actual - 1) * $limite;

        $productoModel = new Producto($this->db);
        
        $total_productos = $productoModel->contarTodos($id_dept, $busqueda);
        $total_paginas = ceil($total_productos / $limite);

        $productos = $productoModel->obtenerTodos($id_dept, $busqueda, $limite, $offset); 
        $departamentos = $productoModel->obtenerDepartamentos();

        require_once 'views/layouts/header.php';
        require_once 'views/dashboard.php';
        require_once 'views/layouts/footer.php';
    }
}