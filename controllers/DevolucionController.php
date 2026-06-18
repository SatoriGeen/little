<?php
// controllers/DevolucionController.php
// ============================================================
// Gestiona la búsqueda de ventas y el procesamiento de
// devoluciones/cambios de producto.
// ============================================================

require_once __DIR__ . '/../models/Devolucion.php';

class DevolucionController {
    private PDO $db;
    private Devolucion $model;

    public function __construct(PDO $conexion) {
        $this->db    = $conexion;
        $this->model = new Devolucion($this->db);

        // Accesible a todos los usuarios autenticados
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login");
            exit();
        }
    }

    /**
     * Vista principal: formulario de búsqueda + resultado.
     * Si se pasa ?folio=N en GET, muestra el detalle de esa venta.
     */
    public function index(): void {
        $venta   = null;
        $detalle = [];
        $error   = null;

        $folio = isset($_GET['folio']) ? (int) $_GET['folio'] : null;

        if ($folio && $folio > 0) {
            $venta = $this->model->buscarVentaPorId($folio);
            if ($venta) {
                $detalle = $this->model->obtenerDetalleVenta($folio);
                if (empty($detalle)) {
                    $error = "Esta venta no tiene productos registrados.";
                }
            } else {
                $error = "No se encontró ninguna venta con el folio #" . str_pad($folio, 5, '0', STR_PAD_LEFT) . ".";
            }
        }

        // Historial reciente de devoluciones
        $historial = $this->model->obtenerHistorial(10, 0);

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/devoluciones/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Procesa la devolución enviada por POST.
     * CSRF validado en index.php.
     */
    public function procesar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?ruta=devoluciones"); exit();
        }

        $id_venta  = (int) ($_POST['id_venta'] ?? 0);
        $motivo    = trim($_POST['motivo'] ?? '');
        $id_usuario = (int) $_SESSION['id_usuario'];

        if ($id_venta <= 0) {
            $_SESSION['mensaje'] = "Folio de venta inválido.";
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=devoluciones"); exit();
        }

        // Construir array de items desde el formulario
        // Formato POST esperado: cantidades[id_producto] = cantidad_a_devolver
        $cantidades_raw = $_POST['cantidades'] ?? [];
        $items = [];
        foreach ($cantidades_raw as $id_producto => $cantidad) {
            $cantidad_float = (float) $cantidad;
            if ($cantidad_float > 0) {
                $items[] = [
                    'id_producto' => (int) $id_producto,
                    'cantidad'    => $cantidad_float,
                ];
            }
        }

        $resultado = $this->model->procesar($id_venta, $items, $motivo, $id_usuario);

        if ($resultado['ok']) {
            $_SESSION['mensaje'] = "✅ " . $resultado['mensaje'] .
                " | Total devuelto: $" . number_format($resultado['total'], 2);
            $_SESSION['tipo'] = 'exito';
        } else {
            $_SESSION['mensaje'] = "❌ " . $resultado['mensaje'];
            $_SESSION['tipo']    = 'error';
        }

        header("Location: index.php?ruta=devoluciones&folio=" . $id_venta);
        exit();
    }
}
