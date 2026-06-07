<?php
// controllers/VentasController.php

class VentasController {
    private $db;

    // Ahora recibimos la conexión a la BD
    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function agregarAlCarrito() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_producto'];
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $cantidad = floatval($_POST['cantidad']);
            
            // CAPTURAMOS EL DEPARTAMENTO QUE VIENE DEL FORMULARIO
            $id_dept = $_POST['id_departamento'] ?? 1;

            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }

            if (isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
            } else {
                $_SESSION['carrito'][$id] = [
                    'nombre' => $nombre,
                    'precio' => $precio,
                    'cantidad' => $cantidad
                ];
            }
            
            // REDIRECCIONAMOS CON EL DEPARTAMENTO QUE VENÍA
            header("Location: index.php?ruta=dashboard&dept=" . $id_dept);
            exit();
        }
    }

    public function vaciarCarrito() {
        unset($_SESSION['carrito']);
        header("Location: index.php?ruta=dashboard");
        exit();
    }

    public function eliminarItem() {
        $id = $_GET['id'] ?? 0;
        // Capturamos el departamento actual para regresar al mismo sitio
        $id_dept = $_GET['dept'] ?? 1;

        if (isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);
        }
        
        // Redirigimos al dashboard manteniendo la pestaña seleccionada
        header("Location: index.php?ruta=dashboard&dept=" . $id_dept);
        exit();
    }

    // EL ALGORITMO TRANSACCIONAL
    public function cobrarTicket() {
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?ruta=dashboard");
            exit();
        }

        // 1. Calcular el total del ticket
        $total = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total += ($item['precio'] * $item['cantidad']);
        }
        
        $id_usuario = $_SESSION['id_usuario']; // El cajero que cobra

        try {
            // 2. INICIAR LA TRANSACCIÓN: A partir de aquí, nada es definitivo hasta hacer COMMIT
            $this->db->beginTransaction();

            // 3. Insertar el ticket general en la tabla 'ventas'
            $queryVenta = "INSERT INTO ventas (id_usuario, total) VALUES (:id_usuario, :total)";
            $stmtVenta = $this->db->prepare($queryVenta);
            $stmtVenta->execute([':id_usuario' => $id_usuario, ':total' => $total]);
            
            // Obtenemos el ID de este ticket recién creado
            $id_venta = $this->db->lastInsertId();

            // 4. Recorrer el carrito para guardar detalles y descontar stock
            $queryDetalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) 
                             VALUES (:id_venta, :id_producto, :cantidad, :precio, :subtotal)";
            $stmtDetalle = $this->db->prepare($queryDetalle);

            $queryStock = "UPDATE productos SET stock = stock - :cantidad WHERE id_producto = :id_producto AND stock >= :cantidad";
            $stmtStock = $this->db->prepare($queryStock);

            foreach ($_SESSION['carrito'] as $id_producto => $item) {
                $subtotal = $item['precio'] * $item['cantidad'];
                
                // Guardar detalle
                $stmtDetalle->execute([
                    ':id_venta' => $id_venta,
                    ':id_producto' => $id_producto,
                    ':cantidad' => $item['cantidad'],
                    ':precio' => $item['precio'],
                    ':subtotal' => $subtotal
                ]);

                // Descontar inventario
                $stmtStock->execute([
                    ':cantidad' => $item['cantidad'],
                    ':id_producto' => $id_producto
                ]);

                // Validar que realmente se haya descontado (que no se vendiera más de lo que hay)
                if ($stmtStock->rowCount() == 0) {
                    throw new Exception("Stock insuficiente para el producto: " . $item['nombre']);
                }
            }

            // 5. SI TODO SALIÓ PERFECTO, GUARDAMOS DEFINITIVAMENTE (COMMIT)
            $this->db->commit();
            
            // Vaciamos el carrito porque ya se cobró
            unset($_SESSION['carrito']);
            
            // Podríamos mandar un mensaje de éxito, por ahora redirigimos
            header("Location: index.php?ruta=dashboard");
            exit();

        } catch (Exception $e) {
            // 6. SI HUBO UN ERROR, CANCELAMOS TODO (ROLLBACK) PARA MANTENER LA INTEGRIDAD
            $this->db->rollBack();
            // Para depurar, mostraremos el error. En producción esto se mejora.
            die("Error en la transacción: " . $e->getMessage());
        }
    }
}