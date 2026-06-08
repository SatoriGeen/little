<?php
require_once 'models/Configuracion.php';

class ConfiguracionController {
    private $db;
    private $model;

    public function __construct($conexion) {
        $this->db = $conexion;
        $this->model = new Configuracion($this->db);
        
        // Solo el admin puede entrar a configuración
        if ($_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "Acceso denegado. Se requieren permisos de Administrador.";
            $_SESSION['tipo'] = 'error';
            header("Location: index.php?ruta=dashboard");
            exit();
        }
    }

    public function index() {
        // Traemos todos los datos para llenar las tarjetas
        $departamentos = $this->model->obtenerDepartamentos();
        $marcas = $this->model->obtenerMarcas();
        $categorias = $this->model->obtenerCategorias();

        require_once 'views/layouts/header.php';
        require_once 'views/configuracion/index.php';
        require_once 'views/layouts/footer.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tipo = $_POST['tipo']; // Puede ser 'departamento', 'marca' o 'categoria'
            $nombre = trim($_POST['nombre']);
            $id_depto = $_POST['id_departamento'] ?? null;

            if ($tipo === 'departamento') {
                $this->model->agregarDepartamento($nombre);
            } elseif ($tipo === 'marca') {
                $this->model->agregarMarca($nombre, $id_depto);
            } elseif ($tipo === 'categoria') {
                $this->model->agregarCategoria($nombre, $id_depto);
            }
            
            $_SESSION['mensaje'] = ucfirst($tipo) . " registrado con éxito.";
            $_SESSION['tipo'] = 'exito';
        }
        header("Location: index.php?ruta=configuracion");
        exit();
    }

    public function eliminar() {
        $tipo = $_GET['tipo'];
        $id = $_GET['id'];

        try {
            if ($tipo === 'departamento') { $this->model->eliminarDepartamento($id); }
            elseif ($tipo === 'marca') { $this->model->eliminarMarca($id); }
            elseif ($tipo === 'categoria') { $this->model->eliminarCategoria($id); }

            $_SESSION['mensaje'] = "Registro eliminado correctamente.";
            $_SESSION['tipo'] = 'exito';
        } catch(PDOException $e) {
            // Protección: Si está siendo usado en un producto, MySQL tira un error que atrapamos aquí
            $_SESSION['mensaje'] = "No se puede eliminar porque hay productos que usan este registro.";
            $_SESSION['tipo'] = 'error';
        }
        header("Location: index.php?ruta=configuracion");
        exit();
    }
}