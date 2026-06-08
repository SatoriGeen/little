<?php
class Configuracion {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // --- DEPARTAMENTOS ---
    public function obtenerDepartamentos() {
        $stmt = $this->conexion->prepare("SELECT * FROM departamentos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function agregarDepartamento($nombre) {
        $stmt = $this->conexion->prepare("INSERT INTO departamentos (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }
    public function eliminarDepartamento($id) {
        $stmt = $this->conexion->prepare("DELETE FROM departamentos WHERE id_departamento = ?");
        return $stmt->execute([$id]);
    }

    // --- MARCAS ---
    public function obtenerMarcas() {
        // Hacemos JOIN para traer también el nombre del departamento al que pertenece
        $query = "SELECT m.*, d.nombre as departamento FROM marcas m LEFT JOIN departamentos d ON m.id_departamento = d.id_departamento";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function agregarMarca($nombre, $id_departamento) {
        $stmt = $this->conexion->prepare("INSERT INTO marcas (nombre, id_departamento) VALUES (?, ?)");
        return $stmt->execute([$nombre, $id_departamento]);
    }
    public function eliminarMarca($id) {
        $stmt = $this->conexion->prepare("DELETE FROM marcas WHERE id_marca = ?");
        return $stmt->execute([$id]);
    }

    // --- CATEGORÍAS ---
    public function obtenerCategorias() {
        $query = "SELECT c.*, d.nombre as departamento FROM categorias c LEFT JOIN departamentos d ON c.id_departamento = d.id_departamento";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function agregarCategoria($nombre, $id_departamento) {
        $stmt = $this->conexion->prepare("INSERT INTO categorias (nombre, id_departamento) VALUES (?, ?)");
        return $stmt->execute([$nombre, $id_departamento]);
    }
    public function eliminarCategoria($id) {
        $stmt = $this->conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        return $stmt->execute([$id]);
    }
}