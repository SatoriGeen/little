<?php
// models/Usuario.php

class Usuario {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    public function buscarPorEmail($email) {
        $query = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conexion->prepare($query);
        // Vinculamos el parámetro para evitar Inyecciones SQL
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}