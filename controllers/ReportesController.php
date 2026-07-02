<?php
// controllers/ReportesController.php

require_once __DIR__ . '/../models/Reporte.php';

class ReportesController {
    private PDO $db;
    private Reporte $model;

    public function __construct(PDO $conexion) {
        $this->db    = $conexion;
        $this->model = new Reporte($this->db);

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?ruta=login"); exit();
        }
        if ($_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "Acceso denegado.";
            $_SESSION['tipo']    = 'error';
            header("Location: index.php?ruta=dashboard"); exit();
        }
    }

    public function index(): void {
        $fecha_inicio = null;
        $fecha_fin    = null;

        if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
            $fi = $_GET['fecha_inicio'];
            $ff = $_GET['fecha_fin'];
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fi) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $ff)) {
                $fecha_inicio = $fi;
                $fecha_fin    = $ff;
            }
        }

        $kpis          = $this->model->obtenerKPIs($fecha_inicio, $fecha_fin);
        $hoy           = $this->model->obtenerVentasHoy();
        $ventas7Dias   = $this->model->obtenerVentasUltimosDias();
        $topProductos  = $this->model->obtenerProductosEstrella();

        // NUEVOS: desglose de pagos y total de devoluciones
        $resumen_pagos      = $this->model->obtenerResumenPagos($fecha_inicio, $fecha_fin);
        $total_devoluciones = $this->model->obtenerTotalDevoluciones($fecha_inicio, $fecha_fin);

        // Restar devoluciones a los KPIs principales para mostrar ingresos reales
        if (isset($kpis['ingresos'])) {
            $kpis['ingresos'] -= $total_devoluciones;
        }
        if (isset($kpis['ingresos_netos'])) {
            $kpis['ingresos_netos'] -= $total_devoluciones;
        }

        $pagina_actual = max(1, (int) ($_GET['pagina'] ?? 1));
        $limite        = defined('VENTAS_POR_PAGINA_HISTORIAL') ? VENTAS_POR_PAGINA_HISTORIAL : 10;
        $offset        = ($pagina_actual - 1) * $limite;

        $total_ventas  = $this->model->contarHistorialVentas($fecha_inicio, $fecha_fin);
        $total_paginas = (int) ceil($total_ventas / $limite);
        $historial     = $this->model->obtenerHistorialVentas($fecha_inicio, $fecha_fin, $limite, $offset);

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/reportes/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}