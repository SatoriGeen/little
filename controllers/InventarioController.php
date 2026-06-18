<?php
// controllers/InventarioController.php

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../helpers/config.php';

class InventarioController {
    private PDO $db;
    private Producto $productoModel;

    public function __construct(PDO $conexion) {
        $this->db            = $conexion;
        $this->productoModel = new Producto($this->db);

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login"); exit();
        }
    }

    public function index(): void {
        $id_dept      = (int) ($_GET['dept'] ?? 1);
        $busqueda     = trim($_GET['buscar'] ?? '');

        // NUEVO: Filtro por nivel de stock
        $filtros_validos = ['todos', 'sin_stock', 'critico', 'bajo', 'surtir', 'normal'];
        $filtro_stock = in_array($_GET['stock'] ?? '', $filtros_validos, true)
            ? ($_GET['stock'] ?? 'todos')
            : 'todos';
        $filtro_stock_param = ($filtro_stock === 'todos') ? null : $filtro_stock;

        $pagina_actual  = max(1, (int) ($_GET['pagina'] ?? 1));
        $limite  = defined('PRODUCTOS_POR_PAGINA_INVENTARIO') ? PRODUCTOS_POR_PAGINA_INVENTARIO : 15;
        $offset  = ($pagina_actual - 1) * $limite;

        $total_productos = $this->productoModel->contarTodos($id_dept, $busqueda, $filtro_stock_param);
        $total_paginas   = (int) ceil($total_productos / $limite);

        $productos    = $this->productoModel->obtenerTodos($id_dept, $busqueda, $limite, $offset, $filtro_stock_param);
        $departamentos = $this->productoModel->obtenerDepartamentos();

        // NUEVO: Resumen de niveles de stock para los botones de filtro
        $resumen_stock = $this->productoModel->resumenStock($id_dept);

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/inventario/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function crear(): void {
        $id_dept = (int) ($_GET['dept'] ?? 1);

        $marcas       = $this->productoModel->obtenerMarcas($id_dept);
        $categorias   = $this->productoModel->obtenerCategorias($id_dept);
        $departamentos = $this->productoModel->obtenerDepartamentos();

        $accion   = 'inventario_guardar';
        $producto = null;

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/inventario/formulario.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function editar(): void {
        $id      = (int) ($_GET['id'] ?? 0);
        $producto = $this->productoModel->obtenerPorId($id);

        if (!$producto) {
            header("Location: index.php?ruta=inventario"); exit();
        }

        $id_dept = (int) $producto['id_departamento'];

        $marcas       = $this->productoModel->obtenerMarcas($id_dept);
        $categorias   = $this->productoModel->obtenerCategorias($id_dept);
        $departamentos = $this->productoModel->obtenerDepartamentos();

        $accion = 'inventario_guardar&id=' . $id;

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/inventario/formulario.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?ruta=inventario"); exit();
        }

        $codigo_barras   = trim($_POST['codigo_barras']  ?? '');
        $nombre          = trim($_POST['nombre']          ?? '');
        $precio          = (float) ($_POST['precio']      ?? 0);
        $stock           = (float) ($_POST['stock']       ?? 0);
        $id_marca        = !empty($_POST['id_marca'])        ? (int) $_POST['id_marca']        : null;
        $id_categoria    = !empty($_POST['id_categoria'])    ? (int) $_POST['id_categoria']    : null;
        $id_departamento = (int) ($_POST['id_departamento'] ?? 0);

        $errores = [];
        if (empty($nombre))         $errores[] = "El nombre es obligatorio.";
        if ($precio < 0)            $errores[] = "El precio no puede ser negativo.";
        if ($stock < 0)             $errores[] = "El stock no puede ser negativo.";
        if ($id_departamento <= 0)  $errores[] = "Selecciona un departamento.";

        if (!empty($errores)) {
            $_SESSION['mensaje'] = implode(' ', $errores);
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=inventario&dept=" . $id_departamento); exit();
        }

        $id = !empty($_GET['id']) ? (int) $_GET['id'] : null;

        if ($id) {
            $ok = $this->productoModel->actualizar(
                $id, $codigo_barras, $nombre, $precio, $stock,
                $id_marca, $id_categoria, $id_departamento
            );
            $_SESSION['mensaje'] = $ok ? "Producto actualizado correctamente." : "Error al actualizar.";
        } else {
            $ok = $this->productoModel->crear(
                $codigo_barras, $nombre, $precio, $stock,
                $id_marca, $id_categoria, $id_departamento
            );
            $_SESSION['mensaje'] = $ok ? "Producto agregado al inventario." : "Error al agregar.";
        }

        $_SESSION['tipo'] = $ok ? 'exito' : 'error';
        header("Location: index.php?ruta=inventario&dept=" . $id_departamento);
        exit();
    }

    public function eliminar(): void {
        $id   = (int) ($_POST['id']   ?? 0);
        $dept = (int) ($_POST['dept'] ?? 1);

        if ($id > 0) {
            $ok = $this->productoModel->eliminar($id);
            $_SESSION['mensaje'] = $ok ? "Producto eliminado." : "No se pudo eliminar (puede estar en uso).";
            $_SESSION['tipo']    = $ok ? 'exito' : 'error';
        }

        header("Location: index.php?ruta=inventario&dept=" . $dept);
        exit();
    }
}