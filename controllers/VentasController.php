<?php
// controllers/VentasController.php

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../helpers/config.php';

class VentasController {
    private PDO $db;

    public function __construct(PDO $conexion) {
        $this->db = $conexion;
    }

    /**
     * Agrega un producto al carrito.
     * SEC-09: El precio se consulta de la BD, no del formulario.
     */
    public function agregarAlCarrito(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?ruta=dashboard"); exit();
        }

        $id_producto = (int) ($_POST['id_producto'] ?? 0);
        $cantidad    = max(0.01, (float) ($_POST['cantidad'] ?? 1));
        $id_dept     = (int) ($_POST['id_departamento'] ?? 1);

        if ($id_producto <= 0) {
            header("Location: index.php?ruta=dashboard&dept=$id_dept"); exit();
        }

        $productoModel = new Producto($this->db);
        $producto = $productoModel->obtenerPorId($id_producto);

        if (!$producto) {
            header("Location: index.php?ruta=dashboard&dept=$id_dept"); exit();
        }

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$id_producto] = [
                'nombre'   => $producto['nombre'],
                'precio'   => (float) $producto['precio'], // ← precio de BD, no del POST
                'cantidad' => $cantidad,
            ];
        }

        header("Location: index.php?ruta=dashboard&dept=$id_dept");
        exit();
    }

    public function vaciarCarrito(): void {
        unset($_SESSION['carrito']);
        header("Location: index.php?ruta=dashboard");
        exit();
    }

    /** SEC-04: eliminarItem usa POST (el token CSRF ya fue validado en index.php). */
    public function eliminarItem(): void {
        $id      = (int) ($_POST['id']   ?? 0);
        $id_dept = (int) ($_POST['dept'] ?? 1);

        if (isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);
        }

        header("Location: index.php?ruta=dashboard&dept=$id_dept");
        exit();
    }

    /**
     * Procesa el cobro del ticket en una transacción atómica.
     *
     * NUEVO: acepta metodo_pago ('efectivo'|'tarjeta') y calcula
     * la comisión bancaria según la constante COMISION_TARJETA.
     * total_neto = total - comision  → es la ganancia real del negocio.
     */
    public function cobrarTicket(): void {
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?ruta=dashboard"); exit();
        }
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login"); exit();
        }

        // Método de pago (validado con whitelist)
        $metodo_pago_raw = $_POST['metodo_pago'] ?? 'efectivo';
        $metodo_pago = in_array($metodo_pago_raw, ['efectivo', 'tarjeta'], true)
            ? $metodo_pago_raw : 'efectivo';

        // Calcular totales
        $total = 0.0;
        foreach ($_SESSION['carrito'] as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        $tasa_comision = ($metodo_pago === 'tarjeta')
            ? (defined('COMISION_TARJETA') ? COMISION_TARJETA : 0.025)
            : 0.0;
        $comision   = round($total * $tasa_comision, 2);
        $total_neto = round($total - $comision, 2);
        $id_usuario = (int) $_SESSION['id_usuario'];

        try {
            $this->db->beginTransaction();

            // Insertar cabecera de venta con método de pago
            $stmtVenta = $this->db->prepare(
                "INSERT INTO ventas (id_usuario, total, metodo_pago, comision, total_neto)
                 VALUES (:id_usuario, :total, :metodo_pago, :comision, :total_neto)"
            );
            $stmtVenta->execute([
                ':id_usuario'  => $id_usuario,
                ':total'       => $total,
                ':metodo_pago' => $metodo_pago,
                ':comision'    => $comision,
                ':total_neto'  => $total_neto,
            ]);
            $id_venta = (int) $this->db->lastInsertId();

            $stmtDetalle = $this->db->prepare(
                "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal)
                 VALUES (:id_venta, :id_producto, :cantidad, :precio, :subtotal)"
            );
            $stmtStock = $this->db->prepare(
                "UPDATE productos SET stock = stock - :cantidad
                 WHERE id_producto = :id_producto AND stock >= :cantidad"
            );

            foreach ($_SESSION['carrito'] as $id_producto => $item) {
                $subtotal = $item['precio'] * $item['cantidad'];

                $stmtDetalle->execute([
                    ':id_venta'    => $id_venta,
                    ':id_producto' => $id_producto,
                    ':cantidad'    => $item['cantidad'],
                    ':precio'      => $item['precio'],
                    ':subtotal'    => $subtotal,
                ]);

                $stmtStock->execute([
                    ':cantidad'    => $item['cantidad'],
                    ':id_producto' => $id_producto,
                ]);

                if ($stmtStock->rowCount() === 0) {
                    throw new Exception("Stock insuficiente para: " . htmlspecialchars($item['nombre']));
                }
            }

            $this->db->commit();
            unset($_SESSION['carrito']);

            $icono = $metodo_pago === 'tarjeta' ? '💳' : '💵';
            $mensaje = "{$icono} Venta #{$id_venta} procesada. Total: $" . number_format($total, 2);
            if ($metodo_pago === 'tarjeta') {
                $mensaje .= " | Comisión: -$" . number_format($comision, 2) . " | Neto: $" . number_format($total_neto, 2);
            }
            $_SESSION['mensaje'] = $mensaje;
            $_SESSION['tipo']    = 'exito';
            header("Location: index.php?ruta=dashboard"); exit();

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['mensaje'] = "Error al procesar la venta: " . htmlspecialchars($e->getMessage());
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=dashboard"); exit();
        }
    }
}