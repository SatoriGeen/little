<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0; color: var(--gris-oscuro);">Gestión de Inventario</h2>
    <a href="index.php?ruta=inventario_crear&dept=<?php echo $_GET['dept'] ?? 1; ?>" class="btn-primario">
        + Agregar Nuevo
    </a>
</div>

<div style="margin-bottom: 20px; display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
    <?php foreach ($departamentos as $depto): ?>
        <?php 
            $dept_actual = $_GET['dept'] ?? 1;
            $es_activo = ($dept_actual == $depto['id_departamento']);
        ?>
        <a href="index.php?ruta=inventario&dept=<?php echo $depto['id_departamento']; ?>" 
           style="flex-shrink: 0; min-width: 120px; text-align: center; padding: 10px; border-radius: 4px; text-decoration: none; font-weight: bold;
           <?php echo $es_activo ? 'background-color: var(--azul-fuerte); color: var(--blanco);' : 'background-color: var(--blanco); color: var(--gris-texto); border: 1px solid var(--gris-borde);'; ?>">
            <?php echo htmlspecialchars($depto['nombre']); ?>
        </a>
    <?php endforeach; ?>
</div>

<form method="GET" action="index.php" style="display: flex; gap: 10px; margin-bottom: 20px;">
    <input type="hidden" name="ruta" value="inventario">
    <input type="hidden" name="dept" value="<?php echo $_GET['dept'] ?? 1; ?>">
    <input type="text" name="buscar" placeholder="🔍 Buscar producto o código..." 
           value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>" style="flex: 1;">
    <button type="submit" class="btn-secundario">Buscar</button>
    <?php if (!empty($_GET['buscar'])): ?>
        <a href="index.php?ruta=inventario&dept=<?php echo $_GET['dept'] ?? 1; ?>" class="btn-outline" style="padding: 10px;">✖ Limpiar</a>
    <?php endif; ?>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Producto</th>
            <th>Marca</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($productos)): ?>
            <tr><td colspan="6" style="text-align: center;">No se encontraron productos.</td></tr>
        <?php else: ?>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td>#<?php echo $producto['id_producto']; ?></td>
                    <td><strong style="color: var(--azul-fuerte);"><?php echo htmlspecialchars($producto['nombre']); ?></strong></td>
                    <td><?php echo htmlspecialchars($producto['marca'] ?? 'N/A'); ?></td>
                    <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                    
                    <td class="<?php echo ($producto['stock'] <= 10) ? 'stock-bajo' : ''; ?>">
                        <strong><?php echo round($producto['stock'], 2); ?></strong>
                    </td>
                    
                    <td>
                        <a href="index.php?ruta=inventario_editar&id=<?php echo $producto['id_producto']; ?>" class="btn-editar" style="margin-right: 5px;">Editar</a>
                        <a href="index.php?ruta=inventario_eliminar&id=<?php echo $producto['id_producto']; ?>" onclick="return confirm('¿Estás seguro de eliminar este producto?');" class="btn-eliminar">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($total_paginas) && $total_paginas > 1): ?>
    <div class="paginacion">
        <?php for($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="index.php?ruta=inventario&dept=<?php echo $id_dept; ?>&buscar=<?php echo urlencode($busqueda ?? ''); ?>&pagina=<?php echo $i; ?>" 
               class="<?php echo ($i == $pagina_actual) ? 'activo' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>