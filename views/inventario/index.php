<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Gestión de Inventario</h2>
    <a href="index.php?ruta=inventario_crear&dept=<?php echo $_GET['dept'] ?? 1; ?>" style="padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
        + Agregar Nuevo Producto
    </a>
</div>

<div style="margin-bottom: 20px; display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
    
    <?php foreach ($departamentos as $depto): ?>
        <?php 
            $dept_actual = $_GET['dept'] ?? 1;
            $es_activo = ($dept_actual == $depto['id_departamento']);
        ?>
        
        <a href="index.php?ruta=inventario&dept=<?php echo $depto['id_departamento']; ?>" 
           style="flex: 1; min-width: 120px; text-align: center; padding: 12px; border-radius: 4px; text-decoration: none; font-weight: bold; white-space: nowrap;
           <?php echo $es_activo ? 'background-color: #e91e63; color: white;' : 'background-color: #fce4ec; color: #e91e63;'; ?>">
            <?php echo htmlspecialchars($depto['nombre']); ?>
        </a>
        
    <?php endforeach; ?>
    
</div>

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
        <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?php echo $producto['id_producto']; ?></td>
                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                <td><?php echo htmlspecialchars($producto['marca'] ?? 'N/A'); ?></td>
                <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                <td class="<?php echo ($producto['stock'] < 10) ? 'stock-bajo' : ''; ?>">
                    <?php echo $producto['stock']; ?>
                </td>
                <td>
                    <a href="index.php?ruta=inventario_editar&id=<?php echo $producto['id_producto']; ?>" 
                       style="padding: 5px 10px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px; margin-right: 5px;">
                        Editar
                    </a>
                    
                    <a href="index.php?ruta=inventario_eliminar&id=<?php echo $producto['id_producto']; ?>" 
                       onclick="return confirm('¿Estás seguro de eliminar este producto? Esto no se puede deshacer.');"
                       style="padding: 5px 10px; background: #f44336; color: white; text-decoration: none; border-radius: 4px;">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>