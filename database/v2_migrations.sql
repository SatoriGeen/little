-- ============================================================
-- Migración v2 — Sistema POS Little
-- Ejecutar en MySQL/MariaDB antes de usar las nuevas funciones
-- ============================================================

-- 1. Agregar columnas de método de pago a la tabla ventas
ALTER TABLE ventas
    ADD COLUMN metodo_pago  ENUM('efectivo', 'tarjeta') NOT NULL DEFAULT 'efectivo' AFTER total,
    ADD COLUMN comision     DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER metodo_pago,
    ADD COLUMN total_neto   DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER comision;

-- Sincronizar total_neto con total en registros existentes
UPDATE ventas SET total_neto = total WHERE total_neto = 0;

-- Índice para acelerar reportes por método de pago
ALTER TABLE ventas ADD INDEX idx_metodo_pago (metodo_pago);

-- ============================================================
-- 2. Tabla de devoluciones (cabecera)
-- ============================================================
CREATE TABLE IF NOT EXISTS devoluciones (
    id_devolucion   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_venta        INT UNSIGNED NOT NULL,
    id_usuario      INT UNSIGNED NOT NULL,
    fecha           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_devuelto  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    motivo          VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_dev_venta    FOREIGN KEY (id_venta)   REFERENCES ventas(id_venta)      ON DELETE RESTRICT,
    CONSTRAINT fk_dev_usuario  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)  ON DELETE RESTRICT,
    INDEX idx_dev_venta (id_venta),
    INDEX idx_dev_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. Tabla de detalle de devoluciones (líneas)
-- ============================================================
CREATE TABLE IF NOT EXISTS detalle_devoluciones (
    id_detalle      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_devolucion   INT UNSIGNED NOT NULL,
    id_producto     INT UNSIGNED NOT NULL,
    cantidad        DECIMAL(10,3) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_ddet_devolucion FOREIGN KEY (id_devolucion) REFERENCES devoluciones(id_devolucion) ON DELETE CASCADE,
    CONSTRAINT fk_ddet_producto   FOREIGN KEY (id_producto)   REFERENCES productos(id_producto)     ON DELETE RESTRICT,
    INDEX idx_ddet_devolucion (id_devolucion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Verificación: muestra estructura final
-- ============================================================
SELECT 'Migración v2 completada exitosamente.' AS resultado;
DESCRIBE ventas;
SHOW TABLES LIKE 'devoluciones';
SHOW TABLES LIKE 'detalle_devoluciones';
