<?php
require_once 'models/Reporte.php';

class ReportesController {
    private $db;
    private $model;

    public function __construct($conexion) {
        $this->db = $conexion;
        $this->model = new Reporte($this->db);
        if ($_SESSION['rol'] !== 'admin') { header("Location: index.php?ruta=dashboard"); exit(); }
    }

    public function index() {
        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin = $_GET['fecha_fin'] ?? null;

        $kpis = $this->model->obtenerKPIs($fecha_inicio, $fecha_fin);
        $hoy = $this->model->obtenerVentasHoy();
        $ventas7Dias = $this->model->obtenerVentasUltimosDias();
        $topProductos = $this->model->obtenerProductosEstrella();
        
        // Lógica de Paginación para el Historial
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $limite = 10; // Mostrar 10 tickets por página
        $offset = ($pagina_actual - 1) * $limite;

        $total_ventas = $this->model->contarHistorialVentas($fecha_inicio, $fecha_fin);
        $total_paginas = ceil($total_ventas / $limite);

        $historial = $this->model->obtenerHistorialVentas($fecha_inicio, $fecha_fin, $limite, $offset);

        require_once 'views/layouts/header.php';
        require_once 'views/reportes/index.php';
        require_once 'views/layouts/footer.php';
    }
}