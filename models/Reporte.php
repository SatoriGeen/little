<?php
// models/Reporte.php

class Reporte {
    private PDO $conexion;

    public function __construct(PDO $db) {
        $this->conexion = $db;
    }

    public function obtenerKPIs(?string $fecha_inicio = null, ?string $fecha_fin = null): array {
        $query = "SELECT
                    COUNT(*)                       AS total_ventas,
                    COALESCE(SUM(total), 0)        AS ingresos,
                    COALESCE(AVG(total), 0)        AS ticket_promedio,
                    COALESCE(SUM(total_neto), 0)   AS ingresos_netos,
                    COALESCE(SUM(comision), 0)     AS total_comisiones
                  FROM ventas";

        if ($fecha_inicio && $fecha_fin) {
            $query .= " WHERE DATE(fecha) BETWEEN :inicio AND :fin";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
        } else {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
        }
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerVentasHoy(): float {
        $stmt = $this->conexion->prepare("
            SELECT 
                (SELECT COALESCE(SUM(total_neto), 0) FROM ventas WHERE DATE(fecha) = CURDATE()) -
                (SELECT COALESCE(SUM(total_devuelto), 0) FROM devoluciones WHERE DATE(fecha) = CURDATE())
            AS ingresos
        ");
        $stmt->execute();
        return (float) ($stmt->fetch(PDO::FETCH_ASSOC)['ingresos'] ?? 0);
    }

    /**
     * BUG-08 ya corregido: DATE_SUB(CURDATE(), INTERVAL 6 DAY)
     * También muestra total_neto (después de comisiones de tarjeta).
     */
    public function obtenerVentasUltimosDias(): array {
        $stmt = $this->conexion->prepare(
            "SELECT dia, SUM(bruto) as total_dia, SUM(neto) as neto_dia
             FROM (
                 SELECT DATE(fecha) as dia, total as bruto, total_neto as neto 
                 FROM ventas 
                 WHERE DATE(fecha) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                 
                 UNION ALL
                 
                 SELECT DATE(fecha) as dia, -total_devuelto as bruto, -total_devuelto as neto 
                 FROM devoluciones 
                 WHERE DATE(fecha) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             ) as t
             GROUP BY dia
             ORDER BY dia ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosEstrella(): array {
        $stmt = $this->conexion->prepare(
            "SELECT nombre, SUM(cantidad) AS total_vendido
             FROM (
                 SELECT p.id_producto, p.nombre, dv.cantidad 
                 FROM detalle_ventas dv 
                 JOIN productos p ON dv.id_producto = p.id_producto
                 
                 UNION ALL
                 
                 SELECT p.id_producto, p.nombre, -dd.cantidad 
                 FROM detalle_devoluciones dd 
                 JOIN productos p ON dd.id_producto = p.id_producto
             ) as t
             GROUP BY id_producto, nombre
             HAVING total_vendido > 0
             ORDER BY total_vendido DESC
             LIMIT 5"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * NUEVO: Desglose de ingresos por método de pago.
     * Muestra cuánto se percibió en efectivo vs tarjeta
     * y cuánto se pagó en comisiones bancarias.
     */
    public function obtenerResumenPagos(?string $fecha_inicio = null, ?string $fecha_fin = null): array {
        $query = "SELECT
                    metodo_pago,
                    COUNT(*)               AS cantidad_transacciones,
                    SUM(total)             AS total_bruto,
                    SUM(comision)          AS total_comisiones,
                    SUM(total_neto)        AS total_neto
                  FROM ventas";

        if ($fecha_inicio && $fecha_fin) {
            $query .= " WHERE DATE(fecha) BETWEEN :inicio AND :fin";
        }

        $query .= " GROUP BY metodo_pago";

        $stmt = $this->conexion->prepare($query);
        if ($fecha_inicio && $fecha_fin) {
            $stmt->execute([':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
        } else {
            $stmt->execute();
        }

        $filas    = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultado = ['efectivo' => null, 'tarjeta' => null];
        foreach ($filas as $fila) {
            $resultado[$fila['metodo_pago']] = $fila;
        }
        return $resultado;
    }

    /**
     * NUEVO: Total de devoluciones en el periodo.
     */
    public function obtenerTotalDevoluciones(?string $fecha_inicio = null, ?string $fecha_fin = null): float {
        $query = "SELECT COALESCE(SUM(total_devuelto), 0) AS total FROM devoluciones";
        if ($fecha_inicio && $fecha_fin) {
            $query .= " WHERE DATE(fecha) BETWEEN :inicio AND :fin";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
        } else {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
        }
        return (float) ($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function contarHistorialVentas(?string $fecha_inicio = null, ?string $fecha_fin = null): int {
        $query = "SELECT COUNT(*) AS total FROM ventas";
        if ($fecha_inicio && $fecha_fin) {
            $query .= " WHERE DATE(fecha) BETWEEN :inicio AND :fin";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
        } else {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
        }
        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function obtenerHistorialVentas(
        ?string $fecha_inicio = null,
        ?string $fecha_fin    = null,
        int $limite           = 10,
        int $offset           = 0
    ): array {
        $query = "SELECT v.id_venta, v.total, v.metodo_pago, v.comision,
                         v.total_neto, v.fecha, u.nombre AS cajero,
                         COALESCE((SELECT SUM(d.total_devuelto) FROM devoluciones d WHERE d.id_venta = v.id_venta), 0) as devuelto
                  FROM ventas v
                  JOIN usuarios u ON v.id_usuario = u.id_usuario";

        if ($fecha_inicio && $fecha_fin) {
            $query .= " WHERE DATE(v.fecha) BETWEEN :inicio AND :fin";
        }

        $query .= " ORDER BY v.fecha DESC LIMIT :limite OFFSET :offset";

        $stmt = $this->conexion->prepare($query);
        if ($fecha_inicio && $fecha_fin) {
            $stmt->bindValue(':inicio', $fecha_inicio);
            $stmt->bindValue(':fin',    $fecha_fin);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($resultados as &$fila) {
            $devuelto = (float) $fila['devuelto'];
            $total = (float) $fila['total'];
            
            if ($devuelto >= $total && $total > 0) {
                $fila['estado'] = 'Devuelta';
            } elseif ($devuelto > 0) {
                $fila['estado'] = 'Parcial';
            } else {
                $fila['estado'] = 'Normal';
            }
        }
        return $resultados;
    }
}