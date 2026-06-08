<?php
class Reporte {
    private $conexion;

    public function __construct($db) { $this->conexion = $db; }

    public function obtenerKPIs($fecha_inicio = null, $fecha_fin = null) {
        $query = "SELECT COUNT(*) as total_ventas, COALESCE(SUM(total), 0) as ingresos, COALESCE(AVG(total), 0) as ticket_promedio FROM ventas";
        if ($fecha_inicio && $fecha_fin) {
            $query .= " WHERE DATE(fecha) BETWEEN :inicio AND :fin";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
        } else {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerVentasHoy() {
        $stmt = $this->conexion->prepare("SELECT COALESCE(SUM(total), 0) as ingresos FROM ventas WHERE DATE(fecha) = CURDATE()");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['ingresos'];
    }

    public function obtenerVentasUltimosDias() {
        $query = "SELECT DATE(fecha) as dia, SUM(total) as total_dia 
                  FROM ventas 
                  WHERE fecha >= DATE(NOW()) - INTERVAL 7 DAY 
                  GROUP BY DATE(fecha) 
                  ORDER BY DATE(fecha) ASC";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosEstrella() {
        $query = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido 
                  FROM detalle_ventas dv
                  JOIN productos p ON dv.id_producto = p.id_producto
                  GROUP BY p.id_producto
                  ORDER BY total_vendido DESC LIMIT 5";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NUEVO: Función para contar el total de tickets para la paginación
    public function contarHistorialVentas($fecha_inicio = null, $fecha_fin = null) {
        $query = "SELECT COUNT(*) as total FROM ventas";
        if ($fecha_inicio && $fecha_fin) {
            $query .= " WHERE DATE(fecha) BETWEEN :inicio AND :fin";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
        } else {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
        }
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    // ACTUALIZADO: Agregamos límites y offset
    public function obtenerHistorialVentas($fecha_inicio = null, $fecha_fin = null, $limite = 10, $offset = 0) {
        $query = "SELECT v.id_venta, v.total, v.fecha, u.nombre as cajero 
                  FROM ventas v 
                  JOIN usuarios u ON v.id_usuario = u.id_usuario";
        
        if ($fecha_inicio && $fecha_fin) {
            $query .= " WHERE DATE(v.fecha) BETWEEN :inicio AND :fin ORDER BY v.fecha DESC LIMIT :limite OFFSET :offset";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindValue(':inicio', $fecha_inicio);
            $stmt->bindValue(':fin', $fecha_fin);
        } else {
            $query .= " ORDER BY v.fecha DESC LIMIT :limite OFFSET :offset";
            $stmt = $this->conexion->prepare($query);
        }
        
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}