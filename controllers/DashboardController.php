<?php
// controllers/DashboardController.php

require_once 'models/Producto.php';

class DashboardController {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function index() {
        // Validamos que el usuario esté logueado
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login");
            exit();
        }

        // Instanciamos el modelo y traemos los productos
        $productoModel = new Producto($this->db);
        $productos = $productoModel->obtenerTodos();

        // Ensamblamos la interfaz
        require_once 'views/layouts/header.php';
        require_once 'views/dashboard.php';
        require_once 'views/layouts/footer.php';
    }
}