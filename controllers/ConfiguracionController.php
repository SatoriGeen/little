<?php
// controllers/ConfiguracionController.php

require_once __DIR__ . '/../models/Configuracion.php';

class ConfiguracionController {
    private PDO $db;
    private Configuracion $model;

    public function __construct(PDO $conexion) {
        $this->db    = $conexion;
        $this->model = new Configuracion($this->db);

        // SEC-08 FIX: Verificar sesión activa ANTES de verificar el rol.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login");
            exit();
        }

        if ($_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "Acceso denegado. Se requieren permisos de Administrador.";
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=dashboard");
            exit();
        }
    }

    public function index(): void {
        $departamentos = $this->model->obtenerDepartamentos();
        $marcas        = $this->model->obtenerMarcas();
        $categorias    = $this->model->obtenerCategorias();

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/configuracion/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Guarda un nuevo ítem de catálogo.
     * SEC-05 FIX: Validación del campo 'tipo' con whitelist.
     * SEC-03 FIX: CSRF validado en index.php.
     */
    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?ruta=configuracion");
            exit();
        }

        // SEC-05 FIX: Whitelist estricta para 'tipo'
        $tipos_validos = ['departamento', 'marca', 'categoria'];
        $tipo  = $_POST['tipo'] ?? '';

        if (!in_array($tipo, $tipos_validos, true)) {
            $_SESSION['mensaje'] = "Tipo de catálogo inválido.";
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=configuracion");
            exit();
        }

        $nombre   = trim($_POST['nombre'] ?? '');
        $id_depto = !empty($_POST['id_departamento']) ? (int) $_POST['id_departamento'] : null;

        if (empty($nombre)) {
            $_SESSION['mensaje'] = "El nombre no puede estar vacío.";
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=configuracion");
            exit();
        }

        if ($tipo === 'departamento') {
            $this->model->agregarDepartamento($nombre);
        } elseif ($tipo === 'marca') {
            if (!$id_depto) {
                $_SESSION['mensaje'] = "Debes seleccionar un departamento para la marca.";
                $_SESSION['tipo']    = 'error';
                header("Location: index.php?ruta=configuracion");
                exit();
            }
            $this->model->agregarMarca($nombre, $id_depto);
        } elseif ($tipo === 'categoria') {
            if (!$id_depto) {
                $_SESSION['mensaje'] = "Debes seleccionar un departamento para la categoría.";
                $_SESSION['tipo']    = 'error';
                header("Location: index.php?ruta=configuracion");
                exit();
            }
            $this->model->agregarCategoria($nombre, $id_depto);
        }

        // SEC-06 FIX: ucfirst($tipo) es seguro aquí porque 'tipo' ya fue validado
        $_SESSION['mensaje'] = ucfirst($tipo) . " registrado con éxito.";
        $_SESSION['tipo']    = 'exito';

        header("Location: index.php?ruta=configuracion");
        exit();
    }

    /**
     * Elimina un ítem de catálogo.
     * SEC-04 FIX: Ahora requiere POST + CSRF (validado en index.php).
     */
    public function eliminar(): void {
        // Leer desde POST (ya no GET)
        $tipos_validos = ['departamento', 'marca', 'categoria'];
        $tipo = $_POST['tipo'] ?? '';
        $id   = (int) ($_POST['id'] ?? 0);

        if (!in_array($tipo, $tipos_validos, true) || $id <= 0) {
            header("Location: index.php?ruta=configuracion");
            exit();
        }

        try {
            if ($tipo === 'departamento') {
                $this->model->eliminarDepartamento($id);
            } elseif ($tipo === 'marca') {
                $this->model->eliminarMarca($id);
            } elseif ($tipo === 'categoria') {
                $this->model->eliminarCategoria($id);
            }

            $_SESSION['mensaje'] = "Registro eliminado correctamente.";
            $_SESSION['tipo']    = 'exito';

        } catch (PDOException $e) {
            error_log('[Configuracion] Error al eliminar: ' . $e->getMessage());
            $_SESSION['mensaje'] = "No se puede eliminar porque hay productos que usan este registro.";
            $_SESSION['tipo']    = 'error';
        }

        header("Location: index.php?ruta=configuracion");
        exit();
    }
}