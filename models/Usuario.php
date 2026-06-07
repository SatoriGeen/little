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
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerTodos() {
        $query = "SELECT * FROM usuarios";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $email, $password, $rol) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        // Usamos $this->conexion aquí
        $stmt = $this->conexion->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $email, $hash, $rol]);
    }
}