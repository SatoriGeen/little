<?php
// models/Usuario.php

class Usuario {
    private PDO $conexion;

    public function __construct(PDO $db) {
        $this->conexion = $db;
    }

    /**
     * Busca un usuario por email para el proceso de login.
     * PERF-02 FIX: SELECT explícito en lugar de SELECT *
     * (evita traer datos innecesarios en cada intento de login)
     */
    public function buscarPorEmail(string $email): array|false {
        $query = "SELECT id_usuario, nombre, email, password_hash, rol
                  FROM usuarios
                  WHERE email = :email
                  LIMIT 1";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los usuarios para la vista de administración.
     * PERF-02 FIX: Excluye password_hash que no se necesita en la lista.
     */
    public function obtenerTodos(): array {
        $query = "SELECT id_usuario, nombre, email, rol FROM usuarios ORDER BY nombre ASC";
        $stmt  = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo usuario con contraseña hasheada con bcrypt.
     * SEC-05 FIX: Validación de rol en este método como segunda capa de defensa.
     */
    public function crear(string $nombre, string $email, string $password, string $rol): bool {
        // Segunda capa de validación: solo roles válidos permitidos
        $roles_validos = ['admin', 'cajero'];
        if (!in_array($rol, $roles_validos, true)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conexion->prepare(
            "INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$nombre, $email, $hash, $rol]);
    }

    /**
     * Elimina un usuario por ID.
     * BUG-05 FIX: Método que faltaba y era requerido por la vista.
     */
    public function eliminar(int $id): bool {
        try {
            $stmt = $this->conexion->prepare(
                "DELETE FROM usuarios WHERE id_usuario = :id"
            );
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('[Usuario] Error al eliminar: ' . $e->getMessage());
            return false;
        }
    }
}