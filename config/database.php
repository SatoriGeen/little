<?php
// config/database.php

// BUG-02 FIX: Usar __DIR__ para ruta absoluta al archivo env.php,
// evitando que falle cuando Apache ejecuta desde un contexto diferente.
require_once __DIR__ . '/env.php';

class Database {
    private ?PDO $conexion = null;

    /**
     * Establece y retorna la conexión PDO.
     * Lanza RuntimeException si la conexión falla (en lugar de hacer echo
     * y retornar null, que causaba Fatal Errors en cascada — LOG-02).
     *
     * @throws RuntimeException Si la conexión a la BD no puede establecerse.
     */
    public function conectar(): PDO {
        try {
            $dsn = "mysql:host=" . $_ENV['DB_HOST']
                 . ";dbname=" . $_ENV['DB_NAME']
                 . ";charset=utf8mb4";

            $this->conexion = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
                // BUG-01 FIX: PDO lanza excepciones en errores, no retorna false
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Devuelve arrays asociativos por defecto
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Evita que PDO emule prepared statements (más seguro)
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            return $this->conexion;

        } catch (PDOException $e) {
            // SEC-07 FIX: No exponer detalles de la BD al cliente.
            // Registrar el error real en el log de Apache/PHP.
            error_log('[DB] Error de conexión: ' . $e->getMessage());

            // Lanzar excepción genérica para detener la ejecución limpiamente.
            throw new RuntimeException(
                'No se pudo conectar a la base de datos. Por favor intente más tarde.'
            );
        }
    }
}