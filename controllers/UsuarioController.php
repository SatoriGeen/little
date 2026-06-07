<?php
require_once 'models/Usuario.php';

class UsuarioController {
    private $db;
    private $model;

    public function __construct($conexion) {
        $this->db = $conexion;
        $this->model = new Usuario($this->db);
        
        // Regla de negocio: Solo los admin pueden gestionar usuarios
        if ($_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "Acceso denegado.";
            $_SESSION['tipo'] = 'error';
            header("Location: index.php?ruta=dashboard");
            exit();
        }
    }

    public function index() {
        $usuarios = $this->model->obtenerTodos();
        require_once 'views/layouts/header.php';
        require_once 'views/usuarios/index.php';
        require_once 'views/layouts/footer.php';
    }

    public function crear() {
        require_once 'views/layouts/header.php';
        require_once 'views/usuarios/formulario.php';
        require_once 'views/layouts/footer.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $rol = $_POST['rol'];

            if ($this->model->crear($nombre, $email, $password, $rol)) {
                $_SESSION['mensaje'] = "Usuario creado correctamente.";
                $_SESSION['tipo'] = 'exito';
            } else {
                $_SESSION['mensaje'] = "Error al crear usuario.";
                $_SESSION['tipo'] = 'error';
            }
        }
        header("Location: index.php?ruta=usuarios");
        exit();
    }
}