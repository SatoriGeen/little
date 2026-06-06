<?php
// controllers/AuthController.php

require_once 'models/Usuario.php';

class AuthController {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Muestra el formulario de login
    public function mostrarLogin() {
        require_once 'views/auth/login.php';
    }

    // Procesa los datos que llegan por POST
    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $usuarioModel = new Usuario($this->db);
            $usuario = $usuarioModel->buscarPorEmail($email);

            // Verificamos si existe el usuario y si la contraseña coincide con el hash
            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                // Autenticación exitosa: Iniciamos sesión
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];
                
                // Redirigimos al panel de administración (que haremos después)
                header("Location: index.php?ruta=dashboard");
                exit();
            } else {
                $error = "Credenciales incorrectas. Intenta de nuevo.";
                require_once 'views/auth/login.php';
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?ruta=login");
        exit();
    }
}