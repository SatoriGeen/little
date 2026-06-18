<?php
// controllers/UsuarioController.php

require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private PDO $db;
    private Usuario $model;

    public function __construct(PDO $conexion) {
        $this->db    = $conexion;
        $this->model = new Usuario($this->db);

        // SEC-08 FIX: Verificar primero que hay sesión activa, LUEGO verificar el rol.
        // (Antes solo se verificaba el rol, lo que causaba Warning si no había sesión)
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login");
            exit();
        }

        if ($_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "Acceso denegado. Se requieren permisos de Administrador.";
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=dashboard");
            exit();
        }
    }

    public function index(): void {
        $usuarios = $this->model->obtenerTodos();
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/usuarios/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function crear(): void {
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/usuarios/formulario.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Guarda un nuevo usuario.
     * SEC-05 FIX: Validación completa de todos los inputs.
     * CSRF validado en index.php.
     */
    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?ruta=usuarios");
            exit();
        }

        $nombre   = trim($_POST['nombre']   ?? '');
        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $rol      = $_POST['rol'] ?? '';

        // Validaciones
        $errores = [];
        if (empty($nombre)) {
            $errores[] = "El nombre es obligatorio.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El email no tiene un formato válido.";
        }
        if (strlen($password) < 8) {
            $errores[] = "La contraseña debe tener al menos 8 caracteres.";
        }
        // SEC-05 FIX: Validación estricta del campo 'rol' (whitelist)
        $roles_permitidos = ['admin', 'cajero'];
        if (!in_array($rol, $roles_permitidos, true)) {
            $errores[] = "El rol seleccionado no es válido.";
        }

        if (!empty($errores)) {
            $_SESSION['mensaje'] = implode(' | ', $errores);
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=usuarios_crear");
            exit();
        }

        if ($this->model->crear($nombre, $email, $password, $rol)) {
            $_SESSION['mensaje'] = "Usuario '{$nombre}' creado correctamente.";
            $_SESSION['tipo']    = 'exito';
        } else {
            $_SESSION['mensaje'] = "Error al crear el usuario (el email ya puede estar registrado).";
            $_SESSION['tipo']    = 'error';
        }

        header("Location: index.php?ruta=usuarios");
        exit();
    }

    /**
     * BUG-05 FIX: Método que faltaba — elimina un usuario por ID.
     * SEC-04 FIX: Requiere POST + CSRF (validado en index.php).
     * Prevención adicional: no se puede eliminar al propio usuario en sesión.
     */
    public function eliminar(): void {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            header("Location: index.php?ruta=usuarios");
            exit();
        }

        // Protección: no eliminar el propio usuario activo
        if ($id === (int) $_SESSION['id_usuario']) {
            $_SESSION['mensaje'] = "No puedes eliminar tu propia cuenta.";
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=usuarios");
            exit();
        }

        if ($this->model->eliminar($id)) {
            $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
            $_SESSION['tipo']    = 'exito';
        } else {
            $_SESSION['mensaje'] = "No se pudo eliminar el usuario.";
            $_SESSION['tipo']    = 'error';
        }

        header("Location: index.php?ruta=usuarios");
        exit();
    }
}