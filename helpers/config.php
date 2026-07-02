<?php
// helpers/config.php
// ============================================================
// Constantes de configuración global del sistema POS.
// Cargado desde index.php antes de cualquier controlador.
// ============================================================

// --- Comisiones de métodos de pago ---
// Tasa de comisión estándar de terminales POS (Clip, iZettle, Bancomer, etc.)
// 4.06% según lo especificado.
if (!defined('COMISION_TARJETA')) define('COMISION_TARJETA', 0.0406); // 4.06%

// --- Umbrales de niveles de stock ---
// Productos con stock <= STOCK_UMBRAL_CRITICO se muestran en rojo urgente
if (!defined('STOCK_UMBRAL_CRITICO')) define('STOCK_UMBRAL_CRITICO', 5);
// Productos con stock <= STOCK_UMBRAL_BAJO se consideran con reposición pendiente
if (!defined('STOCK_UMBRAL_BAJO'))    define('STOCK_UMBRAL_BAJO', 15);

// --- Configuración de paginación ---
if (!defined('PRODUCTOS_POR_PAGINA_VENTAS'))     define('PRODUCTOS_POR_PAGINA_VENTAS', 8);
if (!defined('PRODUCTOS_POR_PAGINA_INVENTARIO')) define('PRODUCTOS_POR_PAGINA_INVENTARIO', 15);
if (!defined('VENTAS_POR_PAGINA_HISTORIAL'))     define('VENTAS_POR_PAGINA_HISTORIAL', 10);

// ============================================================
// 💡 FUNCIONES SUGERIDAS PARA FUTURAS VERSIONES:
//
// 1. CORTE DE CAJA DIARIO (Muy recomendada para este negocio):
//    - Generar un resumen de ventas del día por turno/cajero
//    - Total efectivo + total tarjeta + comisiones del día
//    - Exportar a PDF o impresora térmica
//
// 2. DESCUENTOS POR PRODUCTO O CÓDIGO:
//    - Agregar columna 'descuento' a detalle_ventas
//    - Soporte de cupones o descuentos por cliente frecuente
//
// 3. ALERTAS DE STOCK POR EMAIL (requiere configurar SMTP):
//    - Usar PHPMailer o la función mail() nativa de PHP
//    - Enviar reporte diario de productos críticos al administrador
//
// 4. HISTORIAL DE COMPRAS POR PRODUCTO:
//    - Ver qué tan rápido rota cada producto
//    - Calcular días de inventario disponibles a la tasa de venta actual
//
// 5. MÚLTIPLES SUCURSALES:
//    - Agregar tabla 'sucursales' y columna 'id_sucursal' en ventas/productos
//    - Filtrar reportes por sucursal
// ============================================================
