<?php
// models/Producto.php

class Producto {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    public function obtenerTodos($id_departamento = null) {
        // La consulta base
        $query = "SELECT p.id_producto, p.nombre, p.precio, p.stock, m.nombre as marca 
                  FROM productos p 
                  LEFT JOIN marcas m ON p.id_marca = m.id_marca";
        
        // Si nos piden un departamento en específico, filtramos con WHERE
        if ($id_departamento !== null) {
            $query .= " WHERE p.id_departamento = :id_dept";
        }
                  
        $stmt = $this->conexion->prepare($query);
        
        // Si hay filtro, vinculamos la variable
        if ($id_departamento !== null) {
            $stmt->bindParam(':id_dept', $id_departamento);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene un solo producto por su ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM productos WHERE id_producto = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ACTUALIZADO: Trae solo las marcas del departamento solicitado
    public function obtenerMarcas($id_departamento) {
        $stmt = $this->conexion->prepare("SELECT * FROM marcas WHERE id_departamento = :id_dept");
        $stmt->bindParam(':id_dept', $id_departamento);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ACTUALIZADO: Trae solo las categorías del departamento solicitado
    public function obtenerCategorias($id_departamento) {
        $stmt = $this->conexion->prepare("SELECT * FROM categorias WHERE id_departamento = :id_dept");
        $stmt->bindParam(':id_dept', $id_departamento);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NUEVA FUNCIÓN: Trae los departamentos
    public function obtenerDepartamentos() {
        $stmt = $this->conexion->prepare("SELECT * FROM departamentos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ALGORITMO ACTUALIZADO: Insertar con código de barras
    public function crear($codigo_barras, $nombre, $precio, $stock, $id_marca, $id_categoria, $id_departamento) {
        $query = "INSERT INTO productos (codigo_barras, nombre, precio, stock, id_marca, id_categoria, id_departamento) 
                  VALUES (:codigo_barras, :nombre, :precio, :stock, :id_marca, :id_categoria, :id_departamento)";
        $stmt = $this->conexion->prepare($query);
        return $stmt->execute([
            ':codigo_barras' => $codigo_barras ?: null, // Lógica para respetar el NULL de MySQL
            ':nombre' => $nombre,
            ':precio' => $precio,
            ':stock' => $stock,
            ':id_marca' => $id_marca,
            ':id_categoria' => $id_categoria,
            ':id_departamento' => $id_departamento
        ]);
    }

    // ALGORITMO ACTUALIZADO: Editar con código de barras
    public function actualizar($id, $codigo_barras, $nombre, $precio, $stock, $id_marca, $id_categoria, $id_departamento) {
        $query = "UPDATE productos SET codigo_barras = :codigo_barras, nombre = :nombre, precio = :precio, 
                  stock = :stock, id_marca = :id_marca, id_categoria = :id_categoria, id_departamento = :id_departamento 
                  WHERE id_producto = :id";
        $stmt = $this->conexion->prepare($query);
        return $stmt->execute([
            ':codigo_barras' => $codigo_barras ?: null,
            ':nombre' => $nombre,
            ':precio' => $precio,
            ':stock' => $stock,
            ':id_marca' => $id_marca,
            ':id_categoria' => $id_categoria,
            ':id_departamento' => $id_departamento,
            ':id' => $id
        ]);
    }

    // Algoritmo para eliminar un producto (¡Ojo con las relaciones!)
    public function eliminar($id) {
        try {
            $query = "DELETE FROM productos WHERE id_producto = :id";
            $stmt = $this->conexion->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            // Si el producto ya se vendió en algún ticket, la base de datos (Llave Foránea) 
            // no nos dejará borrarlo para proteger la integridad del ticket.
            return false; 
        }
    }
}