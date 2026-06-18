<?php
// models/Devolucion.php
// ============================================================
// Modelo que gestiona el ciclo completo de devoluciones:
// búsqueda de venta original, validación de cantidades,
// restauración de stock y registro transaccional.
// ============================================================

class Devolucion {
    private PDO $conexion;

    public function __construct(PDO $db) {
        $this->conexion = $db;
    }

    /**
     * Busca una venta por ID y retorna sus datos generales.
     */
    public function buscarVentaPorId(int $id_venta): array|false {
        $stmt = $this->conexion->prepare(
            "SELECT v.id_venta, v.total, v.metodo_pago, v.comision, v.total_neto, v.fecha,
                    u.nombre as cajero
             FROM ventas v
             JOIN usuarios u ON v.id_usuario = u.id_usuario
             WHERE v.id_venta = :id
             LIMIT 1"
        );
        $stmt->bindValue(':id', $id_venta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el detalle de productos de una venta, incluyendo
     * la cantidad ya devuelta en devoluciones anteriores.
     */
    public function obtenerDetalleVenta(int $id_venta): array {
        // Trae el detalle de la venta y calcula cuánto ya fue devuelto
        $stmt = $this->conexion->prepare(
            "SELECT dv.id_producto, p.nombre, dv.cantidad, dv.precio_unitario, dv.subtotal,
                    COALESCE(
                        (SELECT SUM(dd.cantidad)
                         FROM detalle_devoluciones dd
                         JOIN devoluciones d ON dd.id_devolucion = d.id_devolucion
                         WHERE dd.id_producto = dv.id_producto
                           AND d.id_venta = dv.id_venta
                        ), 0
                    ) AS ya_devuelto
             FROM detalle_ventas dv
             JOIN productos p ON dv.id_producto = p.id_producto
             WHERE dv.id_venta = :id_venta
             ORDER BY p.nombre ASC"
        );
        $stmt->bindValue(':id_venta', $id_venta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Procesa una devolución en una transacción atómica.
     *
     * @param int    $id_venta    ID de la venta original
     * @param array  $items       [['id_producto'=>X, 'cantidad'=>Y, 'precio_unitario'=>Z], ...]
     * @param string $motivo      Motivo de la devolución
     * @param int    $id_usuario  Cajero que procesa la devolución
     * @return array              ['ok' => bool, 'mensaje' => string, 'total' => float]
     */
    public function procesar(int $id_venta, array $items, string $motivo, int $id_usuario): array {
        if (empty($items)) {
            return ['ok' => false, 'mensaje' => 'No se seleccionaron productos para devolver.'];
        }

        // Obtener detalle original para validar cantidades
        $detalle_original = $this->obtenerDetalleVenta($id_venta);
        $detalle_map = [];
        foreach ($detalle_original as $linea) {
            $disponible = $linea['cantidad'] - $linea['ya_devuelto'];
            $detalle_map[$linea['id_producto']] = [
                'disponible'      => (float) $disponible,
                'precio_unitario' => (float) $linea['precio_unitario'],
            ];
        }

        // Validar cantidades antes de iniciar la transacción
        $total_devuelto = 0.0;
        $items_validos  = [];
        foreach ($items as $item) {
            $id_prod   = (int)   $item['id_producto'];
            $cantidad  = (float) $item['cantidad'];

            if ($cantidad <= 0) continue;
            if (!isset($detalle_map[$id_prod])) continue;
            if ($cantidad > $detalle_map[$id_prod]['disponible']) {
                return [
                    'ok'      => false,
                    'mensaje' => "La cantidad a devolver excede lo disponible para un producto.",
                ];
            }

            $precio    = $detalle_map[$id_prod]['precio_unitario'];
            $subtotal  = round($precio * $cantidad, 2);
            $total_devuelto += $subtotal;

            $items_validos[] = [
                'id_producto'    => $id_prod,
                'cantidad'       => $cantidad,
                'precio_unitario'=> $precio,
                'subtotal'       => $subtotal,
            ];
        }

        if (empty($items_validos)) {
            return ['ok' => false, 'mensaje' => 'No se procesó ningún artículo válido.'];
        }

        try {
            $this->conexion->beginTransaction();

            // 1. Insertar cabecera de devolución
            $stmtCab = $this->conexion->prepare(
                "INSERT INTO devoluciones (id_venta, id_usuario, total_devuelto, motivo)
                 VALUES (:venta, :usuario, :total, :motivo)"
            );
            $stmtCab->execute([
                ':venta'   => $id_venta,
                ':usuario' => $id_usuario,
                ':total'   => $total_devuelto,
                ':motivo'  => $motivo ?: null,
            ]);
            $id_devolucion = (int) $this->conexion->lastInsertId();

            // 2. Insertar detalle + restaurar stock por cada ítem
            $stmtDet = $this->conexion->prepare(
                "INSERT INTO detalle_devoluciones (id_devolucion, id_producto, cantidad, precio_unitario, subtotal)
                 VALUES (:dev, :prod, :cant, :precio, :sub)"
            );
            $stmtStock = $this->conexion->prepare(
                "UPDATE productos SET stock = stock + :cantidad WHERE id_producto = :id"
            );

            foreach ($items_validos as $item) {
                $stmtDet->execute([
                    ':dev'    => $id_devolucion,
                    ':prod'   => $item['id_producto'],
                    ':cant'   => $item['cantidad'],
                    ':precio' => $item['precio_unitario'],
                    ':sub'    => $item['subtotal'],
                ]);
                $stmtStock->execute([
                    ':cantidad' => $item['cantidad'],
                    ':id'       => $item['id_producto'],
                ]);
            }

            $this->conexion->commit();

            return [
                'ok'      => true,
                'mensaje' => "Devolución #" . str_pad($id_devolucion, 5, '0', STR_PAD_LEFT) . " procesada.",
                'total'   => $total_devuelto,
            ];

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log('[Devolucion] Error: ' . $e->getMessage());
            return ['ok' => false, 'mensaje' => 'Error interno al procesar la devolución.'];
        }
    }

    /**
     * Historial de todas las devoluciones (para reportes).
     */
    public function obtenerHistorial(int $limite = 20, int $offset = 0): array {
        $stmt = $this->conexion->prepare(
            "SELECT d.id_devolucion, d.id_venta, d.total_devuelto, d.motivo, d.fecha,
                    u.nombre as cajero
             FROM devoluciones d
             JOIN usuarios u ON d.id_usuario = u.id_usuario
             ORDER BY d.fecha DESC
             LIMIT :limite OFFSET :offset"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarHistorial(): int {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) as total FROM devoluciones");
        $stmt->execute();
        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }
}
