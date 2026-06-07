<?php
// controllers/InventarioController.php

require_once 'models/Producto.php';

class InventarioController {
    private $db;
    private $productoModel;

    public function __construct($conexion) {
        $this->db = $conexion;
        $this->productoModel = new Producto($this->db);
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login");
            exit();
        }
    }

    public function index() {
        $id_dept = $_GET['dept'] ?? 1;
        
        $productos = $this->productoModel->obtenerTodos($id_dept);
        
        // NUEVO: Traemos todos los departamentos para dibujar las pestañas
        $departamentos = $this->productoModel->obtenerDepartamentos();
        
        require_once 'views/layouts/header.php';
        require_once 'views/inventario/index.php';
        require_once 'views/layouts/footer.php';
    }

    public function crear() {
        // Leemos de la URL en qué pestaña está (1 = Maquillaje, 2 = Snacks)
        $id_dept = $_GET['dept'] ?? 1; 

        // Ahora le pasamos ese ID a nuestro Modelo para que filtre
        $marcas = $this->productoModel->obtenerMarcas($id_dept);
        $categorias = $this->productoModel->obtenerCategorias($id_dept);
        $departamentos = $this->productoModel->obtenerDepartamentos();
        
        $accion = 'inventario_guardar';
        $producto = null;
        
        require_once 'views/layouts/header.php';
        require_once 'views/inventario/formulario.php';
        require_once 'views/layouts/footer.php';
    }

    public function editar() {
        $id = $_GET['id'] ?? 0;
        $producto = $this->productoModel->obtenerPorId($id);
        if (!$producto) {
            header("Location: index.php?ruta=inventario");
            exit();
        }

        // Si estamos editando, el departamento lo dicta el producto guardado
        $id_dept = $producto['id_departamento']; 
        
        $marcas = $this->productoModel->obtenerMarcas($id_dept);
        $categorias = $this->productoModel->obtenerCategorias($id_dept);
        $departamentos = $this->productoModel->obtenerDepartamentos();
        
        $accion = 'inventario_guardar&id=' . $id;
        
        require_once 'views/layouts/header.php';
        require_once 'views/inventario/formulario.php';
        require_once 'views/layouts/footer.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Capturamos todas las variables del formulario
            $codigo_barras = trim($_POST['codigo_barras'] ?? '');
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $stock = $_POST['stock'];
            $id_marca = $_POST['id_marca'];
            $id_categoria = $_POST['id_categoria'];
            $id_departamento = $_POST['id_departamento'];
            
            // 2. Obtenemos el ID de la URL si es edición
            $id = $_GET['id'] ?? null;

            // 3. Ejecutamos la lógica según si es nuevo o edición
            if ($id) {
                $this->productoModel->actualizar($id, $codigo_barras, $nombre, $precio, $stock, $id_marca, $id_categoria, $id_departamento);
                $_SESSION['mensaje'] = "Producto actualizado correctamente.";
                $_SESSION['tipo'] = 'exito';
            } else {
                $this->productoModel->crear($codigo_barras, $nombre, $precio, $stock, $id_marca, $id_categoria, $id_departamento);
                $_SESSION['mensaje'] = "Producto agregado al inventario.";
                $_SESSION['tipo'] = 'exito';
            }
        }
        
        // 4. Redirigimos usando el ID del departamento capturado
        $id_dept = $_POST['id_departamento'] ?? 1;
        header("Location: index.php?ruta=inventario&dept=" . $id_dept);
        exit();
    }

    public function eliminar() {
        $id = $_GET['id'] ?? 0;
        // Para regresar a la misma pestaña después de eliminar, leemos de dónde venía
        $dept = $_GET['dept'] ?? 1; 
        
        if ($id) {
            $this->productoModel->eliminar($id);
        }
        header("Location: index.php?ruta=inventario&dept=" . $dept);
        exit();
    }
}