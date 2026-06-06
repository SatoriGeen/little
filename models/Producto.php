<?php
// models/Producto.php

class Producto {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    public function obtenerTodos() {
        // ¡Aquí agregamos p.stock a la consulta!
        $query = "SELECT p.id_producto, p.nombre, p.precio, p.stock, m.nombre as marca 
                  FROM productos p 
                  LEFT JOIN marcas m ON p.id_marca = m.id_marca";
                  
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}