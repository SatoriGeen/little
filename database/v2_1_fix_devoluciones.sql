-- ============================================================
-- Migración v2.1 (Corrección) — Tablas de Devoluciones
-- ============================================================
-- El script anterior falló al crear estas tablas porque se
-- intentó usar `INT UNSIGNED` para las llaves foráneas, 
-- mientras que tu base de datos usa `INT(11)` (con signo).
-- 
-- Ejecuta este script para crear las tablas faltantes.
-- ============================================================

-- ============================================================
-- 1. Tabla de devoluciones (cabecera)
-- ============================================================
CREATE TABLE IF NOT EXISTS devoluciones (
    id_devolucion   INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_venta        INT(11) NOT NULL,
    id_usuario      INT(11) NOT NULL,
    fecha           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_devuelto  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    motivo          VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_dev_venta    FOREIGN KEY (id_venta)   REFERENCES ventas(id_venta)      ON DELETE RESTRICT,
    CONSTRAINT fk_dev_usuario  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)  ON DELETE RESTRICT,
    INDEX idx_dev_venta (id_venta),
    INDEX idx_dev_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. Tabla de detalle de devoluciones (líneas)
-- ============================================================
CREATE TABLE IF NOT EXISTS detalle_devoluciones (
    id_detalle      INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_devolucion   INT(11) NOT NULL,
    id_producto     INT(11) NOT NULL,
    cantidad        DECIMAL(10,3) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_ddet_devolucion FOREIGN KEY (id_devolucion) REFERENCES devoluciones(id_devolucion) ON DELETE CASCADE,
    CONSTRAINT fk_ddet_producto   FOREIGN KEY (id_producto)   REFERENCES productos(id_producto)     ON DELETE RESTRICT,
    INDEX idx_ddet_devolucion (id_devolucion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
