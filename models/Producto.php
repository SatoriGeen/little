<?php
// models/Producto.php

class Producto {
    private PDO $conexion;

    public function __construct(PDO $db) {
        $this->conexion = $db;
    }

    // ── Helpers internos ─────────────────────────────────────────────────────

    /**
     * Retorna la cláusula SQL de filtro por nivel de stock.
     * Usa las constantes de helpers/config.php (con fallback seguro).
     */
    private function getStockCondicion(?string $filtro_stock): ?string {
        $critico = defined('STOCK_UMBRAL_CRITICO') ? STOCK_UMBRAL_CRITICO : 5;
        $bajo    = defined('STOCK_UMBRAL_BAJO')    ? STOCK_UMBRAL_BAJO    : 15;

        return match($filtro_stock) {
            'sin_stock' => 'p.stock = 0',
            'critico'   => "p.stock > 0 AND p.stock <= {$critico}",
            'bajo'      => "p.stock > {$critico} AND p.stock <= {$bajo}",
            'surtir'    => "p.stock <= {$bajo}",   // sin_stock + critico + bajo (todos los que necesitan reposición)
            'normal'    => "p.stock > {$bajo}",
            default     => null,
        };
    }

    // ── Conteo y listado ──────────────────────────────────────────────────────

    public function contarTodos(
        ?int $id_departamento = null,
        ?string $busqueda     = null,
        ?string $filtro_stock = null
    ): int {
        $query       = "SELECT COUNT(*) as total FROM productos p";
        $condiciones = [];
        $parametros  = [];

        if ($id_departamento !== null) {
            $condiciones[] = "p.id_departamento = :id_dept";
            $parametros[':id_dept'] = $id_departamento;
        }
        if (!empty($busqueda)) {
            $condiciones[] = "(p.nombre LIKE :busqueda OR p.codigo_barras = :busqueda_exacta)";
            $parametros[':busqueda']        = "%" . $busqueda . "%";
            $parametros[':busqueda_exacta'] = $busqueda;
        }
        // NUEVO: filtro por nivel de stock
        $condicionStock = $this->getStockCondicion($filtro_stock);
        if ($condicionStock) {
            $condiciones[] = $condicionStock;
        }

        if (!empty($condiciones)) {
            $query .= " WHERE " . implode(" AND ", $condiciones);
        }

        $stmt = $this->conexion->prepare($query);
        foreach ($parametros as $key => $valor) {
            $stmt->bindValue($key, $valor);
        }
        $stmt->execute();
        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function obtenerTodos(
        ?int $id_departamento = null,
        ?string $busqueda     = null,
        int $limite           = 10,
        int $offset           = 0,
        ?string $filtro_stock = null
    ): array {
        $query = "SELECT p.id_producto, p.codigo_barras, p.nombre, p.precio,
                         p.stock, p.id_departamento, p.id_marca, p.id_categoria,
                         m.nombre as marca
                  FROM productos p
                  LEFT JOIN marcas m ON p.id_marca = m.id_marca";

        $condiciones = [];
        $parametros  = [];

        if ($id_departamento !== null) {
            $condiciones[] = "p.id_departamento = :id_dept";
            $parametros[':id_dept'] = $id_departamento;
        }
        if (!empty($busqueda)) {
            $condiciones[] = "(p.nombre LIKE :busqueda OR p.codigo_barras = :busqueda_exacta)";
            $parametros[':busqueda']        = "%" . $busqueda . "%";
            $parametros[':busqueda_exacta'] = $busqueda;
        }
        // NUEVO: filtro por nivel de stock
        $condicionStock = $this->getStockCondicion($filtro_stock);
        if ($condicionStock) {
            $condiciones[] = $condicionStock;
        }

        if (!empty($condiciones)) {
            $query .= " WHERE " . implode(" AND ", $condiciones);
        }

        $query .= " ORDER BY p.stock ASC, p.nombre ASC LIMIT :limite OFFSET :offset";

        $stmt = $this->conexion->prepare($query);
        foreach ($parametros as $key => $valor) {
            $stmt->bindValue($key, $valor);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * NUEVO: Resumen de productos por nivel de stock.
     * Usado por el widget de alertas del dashboard.
     */
    public function resumenStock(?int $id_departamento = null): array {
        $critico = defined('STOCK_UMBRAL_CRITICO') ? STOCK_UMBRAL_CRITICO : 5;
        $bajo    = defined('STOCK_UMBRAL_BAJO')    ? STOCK_UMBRAL_BAJO    : 15;

        $where  = $id_departamento !== null ? "WHERE id_departamento = ?" : "";
        $params = $id_departamento !== null ? [$id_departamento] : [];

        $query = "SELECT
                    SUM(CASE WHEN stock = 0                                THEN 1 ELSE 0 END) as sin_stock,
                    SUM(CASE WHEN stock > 0 AND stock <= {$critico}        THEN 1 ELSE 0 END) as critico,
                    SUM(CASE WHEN stock > {$critico} AND stock <= {$bajo}  THEN 1 ELSE 0 END) as bajo,
                    SUM(CASE WHEN stock > {$bajo}                          THEN 1 ELSE 0 END) as normal,
                    COUNT(*) as total
                  FROM productos p {$where}";

        $stmt = $this->conexion->prepare($query);
        $stmt->execute($params);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: ['sin_stock' => 0, 'critico' => 0, 'bajo' => 0, 'normal' => 0, 'total' => 0];
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    public function obtenerPorId(int $id): array|false {
        $stmt = $this->conexion->prepare(
            "SELECT p.*, m.nombre as marca_nombre, c.nombre as categoria_nombre
             FROM productos p
             LEFT JOIN marcas m ON p.id_marca = m.id_marca
             LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
             WHERE p.id_producto = :id
             LIMIT 1"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene únicamente el precio real de un producto desde la BD.
     * Usado por VentasController para prevenir price manipulation (SEC-09).
     */
    public function obtenerPrecioPorId(int $id): float|false {
        $stmt = $this->conexion->prepare(
            "SELECT precio FROM productos WHERE id_producto = :id LIMIT 1"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? (float) $row['precio'] : false;
    }

    public function obtenerMarcas(int $id_departamento): array {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM marcas WHERE id_departamento = :id_dept ORDER BY nombre ASC"
        );
        $stmt->bindValue(':id_dept', $id_departamento, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCategorias(int $id_departamento): array {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM categorias WHERE id_departamento = :id_dept ORDER BY nombre ASC"
        );
        $stmt->bindValue(':id_dept', $id_departamento, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDepartamentos(): array {
        $stmt = $this->conexion->prepare("SELECT * FROM departamentos ORDER BY nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(
        string $codigo_barras,
        string $nombre,
        float $precio,
        float $stock,
        ?int $id_marca,
        ?int $id_categoria,
        int $id_departamento
    ): bool {
        if ($precio < 0 || $stock < 0) return false;

        $stmt = $this->conexion->prepare(
            "INSERT INTO productos (codigo_barras, nombre, precio, stock, id_marca, id_categoria, id_departamento)
             VALUES (:cb, :nombre, :precio, :stock, :marca, :cat, :dept)"
        );
        return $stmt->execute([
            ':cb'     => $codigo_barras ?: null,
            ':nombre' => $nombre,
            ':precio' => $precio,
            ':stock'  => $stock,
            ':marca'  => $id_marca     ?: null,
            ':cat'    => $id_categoria ?: null,
            ':dept'   => $id_departamento,
        ]);
    }

    public function actualizar(
        int $id,
        string $codigo_barras,
        string $nombre,
        float $precio,
        float $stock,
        ?int $id_marca,
        ?int $id_categoria,
        int $id_departamento
    ): bool {
        if ($precio < 0 || $stock < 0) return false;

        $stmt = $this->conexion->prepare(
            "UPDATE productos
             SET codigo_barras = :cb, nombre = :nombre, precio = :precio, stock = :stock,
                 id_marca = :marca, id_categoria = :cat, id_departamento = :dept
             WHERE id_producto = :id"
        );
        return $stmt->execute([
            ':cb'     => $codigo_barras ?: null,
            ':nombre' => $nombre,
            ':precio' => $precio,
            ':stock'  => $stock,
            ':marca'  => $id_marca     ?: null,
            ':cat'    => $id_categoria ?: null,
            ':dept'   => $id_departamento,
            ':id'     => $id,
        ]);
    }

    public function eliminar(int $id): bool {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id_producto = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('[Producto] Error al eliminar ID=' . $id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restaura stock de un producto (usado en devoluciones).
     */
    public function restaurarStock(int $id_producto, float $cantidad): bool {
        $stmt = $this->conexion->prepare(
            "UPDATE productos SET stock = stock + :cantidad WHERE id_producto = :id"
        );
        return $stmt->execute([':cantidad' => $cantidad, ':id' => $id_producto]);
    }
}