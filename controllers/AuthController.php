<?php
// controllers/AuthController.php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private PDO $db;

    public function __construct(PDO $conexion) {
        $this->db = $conexion;
    }

    public function mostrarLogin(): void {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Procesa las credenciales de login enviadas por POST.
     * El CSRF ya es validado en index.php antes de llamar a este método.
     * SEC-03 FIX: csrf_validate() se llama en el router (index.php).
     */
    public function procesarLogin(): void {
        // Sanitizar: trim + filter para email
        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Todos los campos son obligatorios.";
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        $usuarioModel = new Usuario($this->db);
        $usuario      = $usuarioModel->buscarPorEmail($email);

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            // Autenticación exitosa
            // Regenerar el ID de sesión para prevenir session fixation attacks
            session_regenerate_id(true);
            // Regenerar también el token CSRF
            csrf_regenerate();

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre'];
            $_SESSION['rol']        = $usuario['rol'];

            header("Location: index.php?ruta=dashboard");
            exit();
        } else {
            // Mensaje genérico: no revelar si el email existe o no
            $error = "Credenciales incorrectas. Intenta de nuevo.";
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout(): void {
        // Limpiar todos los datos de sesión
        $_SESSION = [];
        // Destruir la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: index.php?ruta=login");
        exit();
    }
}