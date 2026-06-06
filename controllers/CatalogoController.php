<?php
// controllers/CatalogoController.php

require_once 'models/Producto.php';

class CatalogoController {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function index() {
        // 1. Instanciamos el modelo
        $productoModel = new Producto($this->db);
        
        // 2. Ejecutamos el algoritmo para traer los datos
        $productos = $productoModel->obtenerTodos();
        
        // 3. Cargamos la vista (el archivo HTML). 
        // Al requerirlo aquí, la vista tendrá acceso a la variable $productos.
        require_once 'views/catalogo.php';
    }
}