<?php
// config/database.php

// Requerimos nuestro lector de variables de entorno
require_once 'env.php';

class Database {
    private $conexion;

    public function conectar() {
        $this->conexion = null;

        try {
            // Construimos el DSN (Data Source Name) usando las variables del .env
            $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'] . ";charset=utf8mb4";
            
            // Creamos la instancia de PDO
            $this->conexion = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
            
            // Configuramos PDO para que lance excepciones en caso de error
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $this->conexion;
            
        } catch(PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        return $this->conexion;
    }
}