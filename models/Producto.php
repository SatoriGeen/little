<?php
class Producto {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    public function contarTodos($id_departamento = null, $busqueda = null) {
        $query = "SELECT COUNT(*) as total FROM productos p";
        $condiciones = [];
        $parametros = [];

        if ($id_departamento !== null) {
            $condiciones[] = "p.id_departamento = :id_dept";
            $parametros[':id_dept'] = $id_departamento;
        }
        if (!empty($busqueda)) {
            $condiciones[] = "(p.nombre LIKE :busqueda OR p.codigo_barras = :busqueda_exacta)";
            $parametros[':busqueda'] = "%" . $busqueda . "%";
            $parametros[':busqueda_exacta'] = $busqueda;
        }

        if (count($condiciones) > 0) {
            $query .= " WHERE " . implode(" AND ", $condiciones);
        }

        $stmt = $this->conexion->prepare($query);
        foreach ($parametros as $key => $valor) {
            $stmt->bindValue($key, $valor);
        }
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['total'] ?? 0;
    }

    public function obtenerTodos($id_departamento = null, $busqueda = null, $limite = 10, $offset = 0) {
        $query = "SELECT p.id_producto, p.codigo_barras, p.nombre, p.precio, p.stock, m.nombre as marca 
                  FROM productos p 
                  LEFT JOIN marcas m ON p.id_marca = m.id_marca";
        
        $condiciones = [];
        $parametros = [];

        if ($id_departamento !== null) {
            $condiciones[] = "p.id_departamento = :id_dept";
            $parametros[':id_dept'] = $id_departamento;
        }
        if (!empty($busqueda)) {
            $condiciones[] = "(p.nombre LIKE :busqueda OR p.codigo_barras = :busqueda_exacta)";
            $parametros[':busqueda'] = "%" . $busqueda . "%";
            $parametros[':busqueda_exacta'] = $busqueda;
        }

        if (count($condiciones) > 0) {
            $query .= " WHERE " . implode(" AND ", $condiciones);
        }
                  
        $query .= " LIMIT :limite OFFSET :offset";
        
        $stmt = $this->conexion->prepare($query);
        
        foreach ($parametros as $key => $valor) {
            $stmt->bindValue($key, $valor);
        }
        
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE id_producto = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerMarcas($id_departamento) {
        $stmt = $this->conexion->prepare("SELECT * FROM marcas WHERE id_departamento = :id_dept");
        $stmt->bindParam(':id_dept', $id_departamento);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCategorias($id_departamento) {
        $stmt = $this->conexion->prepare("SELECT * FROM categorias WHERE id_departamento = :id_dept");
        $stmt->bindParam(':id_dept', $id_departamento);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDepartamentos() {
        $stmt = $this->conexion->prepare("SELECT * FROM departamentos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($codigo_barras, $nombre, $precio, $stock, $id_marca, $id_categoria, $id_departamento) {
        $query = "INSERT INTO productos (codigo_barras, nombre, precio, stock, id_marca, id_categoria, id_departamento) 
                  VALUES (:codigo_barras, :nombre, :precio, :stock, :id_marca, :id_categoria, :id_departamento)";
        $stmt = $this->conexion->prepare($query);
        return $stmt->execute([
            ':codigo_barras' => $codigo_barras ?: null,
            ':nombre' => $nombre,
            ':precio' => $precio,
            ':stock' => $stock,
            ':id_marca' => $id_marca,
            ':id_categoria' => $id_categoria,
            ':id_departamento' => $id_departamento
        ]);
    }

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

    public function eliminar($id) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id_producto = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) { return false; }
    }
}