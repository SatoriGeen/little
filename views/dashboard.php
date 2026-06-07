<div style="display: flex; gap: 20px;">
    
    <div style="flex: 2;">
        
        <div style="margin-bottom: 20px; display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
            <?php foreach ($departamentos as $depto): ?>
                <?php 
                    $dept_actual = $_GET['dept'] ?? 1;
                    $es_activo = ($dept_actual == $depto['id_departamento']);
                ?>
                <a href="index.php?ruta=dashboard&dept=<?php echo $depto['id_departamento']; ?>" 
                   style="flex: 1; min-width: 120px; text-align: center; padding: 12px; border-radius: 4px; text-decoration: none; font-weight: bold; white-space: nowrap;
                   <?php echo $es_activo ? 'background-color: #e91e63; color: white;' : 'background-color: #fce4ec; color: #e91e63;'; ?>">
                    <?php echo htmlspecialchars($depto['nombre']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <h2>Inventario Disponible</h2>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Vender</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong><br>
                            <small><?php echo htmlspecialchars($producto['marca'] ?? ''); ?></small>
                        </td>
                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                        <td class="<?php echo ($producto['stock'] < 25) ? 'stock-bajo' : ''; ?>">
                            <?php echo $producto['stock']; ?>
                        </td>
                        <td>
                            <form action="index.php?ruta=agregar_carrito" method="POST" style="display: flex; gap: 5px;">
                                <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">

                                <input type="hidden" name="id_departamento" value="<?php echo $_GET['dept'] ?? 1; ?>">

                                <input type="number" step="0.001" min="0.001" name="cantidad" value="1" style="width: 70px; padding: 6px;">
                                <button type="submit" style="padding: 6px 12px; background: #e91e63; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">+</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; align-self: flex-start; position: sticky; top: 20px;">
        <h2 style="margin-top: 0;">Ticket Actual</h2>
        
        <?php 
        // 1. Inicializamos total en 0 siempre
        $total = 0; 
        
        if (!empty($_SESSION['carrito'])): ?>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($_SESSION['carrito'] as $id => $item): 
                    $subtotal = $item['precio'] * $item['cantidad'];
                    $total += $subtotal;
                ?>
                    <li style="border-bottom: 1px dashed #ccc; padding: 10px 0;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong><?php echo htmlspecialchars($item['nombre']); ?></strong>
                            <a href="index.php?ruta=eliminar_item&id=<?php echo $id; ?>&dept=<?php echo $_GET['dept'] ?? 1; ?>" 
                                style="color: red; font-size: 12px;">
                                Eliminar
                            </a>
                        </div>
                        <?php echo $item['cantidad']; ?> x $<?php echo number_format($item['precio'], 2); ?> 
                        = <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h3 style="text-align: right;">Total: $<?php echo number_format($total, 2); ?></h3>
            <a href="index.php?ruta=cobrar" style="display: block; background: #4CAF50; color: white; text-align: center; padding: 10px; border-radius: 4px; text-decoration: none; font-weight: bold;">
                Cobrar Ticket
            </a>
        <?php else: ?>
            <p>Ticket vacío.</p>
        <?php endif; ?>
    </div>
</div>