<style>
    /* Filtros de stock en inventario */
    .stock-filters { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .stock-filter-btn {
        padding: 7px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;
        text-decoration: none; border: 2px solid transparent; transition: 0.2s;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .sf-todos    { background: var(--gris-oscuro); color: white; }
    .sf-sin_stock{ background: #FEE2E2; color: #991B1B; border-color: #FECACA; }
    .sf-critico  { background: #FEF3C7; color: #92400E; border-color: #FDE68A; }
    .sf-bajo     { background: #DBEAFE; color: #1E40AF; border-color: #BFDBFE; }
    .sf-normal   { background: #D1FAE5; color: #065F46; border-color: #A7F3D0; }
    .sf-surtir   { background: #EDE9FE; color: #5B21B6; border-color: #DDD6FE; }
    .sf-activo   { box-shadow: 0 0 0 3px rgba(13,71,161,0.3); transform: scale(1.05); }
    .stock-filter-btn:hover { opacity: 0.85; transform: scale(1.02); }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
    <h2 style="margin: 0; color: var(--gris-oscuro);">📦 Gestión de Inventario</h2>
    <a href="index.php?ruta=inventario_crear&amp;dept=<?php echo (int)($id_dept ?? 1); ?>" class="btn-primario">+ Agregar Nuevo</a>
</div>

<!-- Pestañas de departamentos -->
<div style="margin-bottom: 15px; display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;">
    <?php foreach ($departamentos as $depto): ?>
        <?php $es_activo = (($id_dept ?? 1) == $depto['id_departamento']); ?>
        <a href="index.php?ruta=inventario&amp;dept=<?php echo (int)$depto['id_departamento']; ?>"
           style="flex-shrink:0; min-width:110px; text-align:center; padding:9px; border-radius:4px; text-decoration:none; font-weight:bold;
           <?php echo $es_activo ? 'background-color:var(--azul-fuerte);color:var(--blanco);' : 'background-color:var(--blanco);color:var(--gris-texto);border:1px solid var(--gris-borde);'; ?>">
            <?php echo htmlspecialchars($depto['nombre'], ENT_QUOTES, 'UTF-8'); ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- NUEVO: Filtros de nivel de stock -->
<?php
    $critico_n = (int) ($resumen_stock['critico'] ?? 0);
    $bajo_n    = (int) ($resumen_stock['bajo']    ?? 0);
    $sin_n     = (int) ($resumen_stock['sin_stock']?? 0);
    $normal_n  = (int) ($resumen_stock['normal']  ?? 0);
    $filtro_actual = $filtro_stock ?? 'todos';
    $dept_param    = (int)($id_dept ?? 1);
    $buscar_param  = urlencode($busqueda ?? '');
?>
<div class="stock-filters">
    <a href="index.php?ruta=inventario&amp;dept=<?php echo $dept_param; ?>&amp;buscar=<?php echo $buscar_param; ?>&amp;stock=todos"
       class="stock-filter-btn sf-todos <?php echo ($filtro_actual==='todos')?'sf-activo':''; ?>">
        📋 Todos (<?php echo (int)($resumen_stock['total']??0); ?>)
    </a>
    <?php if ($sin_n > 0): ?>
    <a href="index.php?ruta=inventario&amp;dept=<?php echo $dept_param; ?>&amp;buscar=<?php echo $buscar_param; ?>&amp;stock=sin_stock"
       class="stock-filter-btn sf-sin_stock <?php echo ($filtro_actual==='sin_stock')?'sf-activo':''; ?>">
        🚫 Sin Stock (<?php echo $sin_n; ?>)
    </a>
    <?php endif; ?>
    <?php if ($critico_n > 0): ?>
    <a href="index.php?ruta=inventario&amp;dept=<?php echo $dept_param; ?>&amp;buscar=<?php echo $buscar_param; ?>&amp;stock=critico"
       class="stock-filter-btn sf-critico <?php echo ($filtro_actual==='critico')?'sf-activo':''; ?>">
        🔴 Crítico ≤<?php echo STOCK_UMBRAL_CRITICO; ?> (<?php echo $critico_n; ?>)
    </a>
    <?php endif; ?>
    <?php if ($bajo_n > 0): ?>
    <a href="index.php?ruta=inventario&amp;dept=<?php echo $dept_param; ?>&amp;buscar=<?php echo $buscar_param; ?>&amp;stock=bajo"
       class="stock-filter-btn sf-bajo <?php echo ($filtro_actual==='bajo')?'sf-activo':''; ?>">
        🟡 Bajo (<?php echo $bajo_n; ?>)
    </a>
    <?php endif; ?>
    <a href="index.php?ruta=inventario&amp;dept=<?php echo $dept_param; ?>&amp;buscar=<?php echo $buscar_param; ?>&amp;stock=surtir"
       class="stock-filter-btn sf-surtir <?php echo ($filtro_actual==='surtir')?'sf-activo':''; ?>">
        🛒 A Surtir (<?php echo $sin_n + $critico_n + $bajo_n; ?>)
    </a>
    <a href="index.php?ruta=inventario&amp;dept=<?php echo $dept_param; ?>&amp;buscar=<?php echo $buscar_param; ?>&amp;stock=normal"
       class="stock-filter-btn sf-normal <?php echo ($filtro_actual==='normal')?'sf-activo':''; ?>">
        🟢 Normal (<?php echo $normal_n; ?>)
    </a>
</div>

<!-- Buscador -->
<form method="GET" action="index.php" style="display:flex;gap:10px;margin-bottom:20px;">
    <input type="hidden" name="ruta"  value="inventario">
    <input type="hidden" name="dept"  value="<?php echo (int)($id_dept??1); ?>">
    <input type="hidden" name="stock" value="<?php echo htmlspecialchars($filtro_actual, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="text" name="buscar" placeholder="Buscar por nombre o código..."
           value="<?php echo htmlspecialchars($busqueda ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="flex:1;">
    <button type="submit" class="btn-secundario">Buscar</button>
    <?php if (!empty($busqueda)): ?>
        <a href="index.php?ruta=inventario&amp;dept=<?php echo $dept_param; ?>&amp;stock=<?php echo htmlspecialchars($filtro_actual, ENT_QUOTES, 'UTF-8'); ?>"
           class="btn-outline" style="padding:10px;">✖</a>
    <?php endif; ?>
</form>

<!-- Tabla de productos -->
<table>
    <thead>
        <tr>
            <th>#ID</th>
            <th>Producto</th>
            <th>Marca</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Nivel</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($productos)): ?>
            <tr><td colspan="7" style="text-align:center;padding:30px;color:#94A3B8;">No hay productos con este filtro.</td></tr>
        <?php else: ?>
            <?php
                $umbral_c = defined('STOCK_UMBRAL_CRITICO') ? STOCK_UMBRAL_CRITICO : 5;
                $umbral_b = defined('STOCK_UMBRAL_BAJO')    ? STOCK_UMBRAL_BAJO    : 15;
                foreach ($productos as $producto):
                    $stock = (float) $producto['stock'];
                    if ($stock == 0)           { $nivel_clase = 'sin_stock'; $nivel_label = '🚫 Sin Stock'; }
                    elseif ($stock <= $umbral_c){ $nivel_clase = 'critico';   $nivel_label = '🔴 Crítico'; }
                    elseif ($stock <= $umbral_b){ $nivel_clase = 'bajo';      $nivel_label = '🟡 Bajo'; }
                    else                        { $nivel_clase = 'normal';    $nivel_label = '🟢 OK'; }
            ?>
                <tr>
                    <td style="color:#94A3B8;font-size:12px;">#<?php echo (int)$producto['id_producto']; ?></td>
                    <td><strong style="color:var(--azul-fuerte);"><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                    <td style="font-size:12px;color:#64748B;"><?php echo htmlspecialchars($producto['marca'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><strong>$<?php echo number_format($producto['precio'], 2); ?></strong></td>
                    <td class="<?php echo ($stock <= $umbral_b) ? 'stock-bajo' : ''; ?>">
                        <strong><?php echo round($stock, 2); ?></strong>
                    </td>
                    <td><span class="badge-<?php echo $nivel_clase; ?>"><?php echo $nivel_label; ?></span></td>
                    <td>
                        <a href="index.php?ruta=inventario_editar&amp;id=<?php echo (int)$producto['id_producto']; ?>" class="btn-editar" style="margin-right:4px;">Editar</a>
                        <form class="form-eliminar" action="index.php?ruta=inventario_eliminar" method="POST"
                              onsubmit="return confirm('¿Eliminar este producto?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id"   value="<?php echo (int)$producto['id_producto']; ?>">
                            <input type="hidden" name="dept" value="<?php echo $dept_param; ?>">
                            <button type="submit" class="btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Paginación -->
<?php if (isset($total_paginas) && $total_paginas > 1): ?>
    <div class="paginacion">
        <?php for($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="index.php?ruta=inventario&amp;dept=<?php echo $dept_param; ?>&amp;buscar=<?php echo $buscar_param; ?>&amp;stock=<?php echo htmlspecialchars($filtro_actual, ENT_QUOTES, 'UTF-8'); ?>&amp;pagina=<?php echo $i; ?>"
               class="<?php echo ($i==$pagina_actual)?'activo':''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>