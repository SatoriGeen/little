<div style="display: flex; gap: 25px; align-items: flex-start; width: 100%;">
    
    <div style="flex: 2; min-width: 0;"> 
        
        <div style="margin-bottom: 20px; display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
            <?php foreach ($departamentos as $depto): ?>
                <?php 
                    $dept_actual = $_GET['dept'] ?? 1;
                    $es_activo = ($dept_actual == $depto['id_departamento']);
                ?>
                <a href="index.php?ruta=dashboard&dept=<?php echo $depto['id_departamento']; ?>" 
                   style="flex-shrink: 0; min-width: 120px; text-align: center; padding: 10px; border-radius: 4px; text-decoration: none; font-weight: bold;
                   <?php echo $es_activo ? 'background-color: var(--azul-fuerte); color: var(--blanco);' : 'background-color: var(--blanco); color: var(--gris-texto); border: 1px solid var(--gris-borde);'; ?>">
                    <?php echo htmlspecialchars($depto['nombre']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <form method="GET" action="index.php" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <input type="hidden" name="ruta" value="dashboard">
            <input type="hidden" name="dept" value="<?php echo $_GET['dept'] ?? 1; ?>">
            <input type="text" name="buscar" placeholder="🔍 Buscar por nombre o código..." 
                   value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>" style="flex: 1;">
            <button type="submit" class="btn-secundario">Buscar</button>
            <?php if (!empty($_GET['buscar'])): ?>
                <a href="index.php?ruta=dashboard&dept=<?php echo $_GET['dept'] ?? 1; ?>" class="btn-outline" style="padding: 10px;">✖ Limpiar</a>
            <?php endif; ?>
        </form>

        <h2 style="margin-top: 0; color: var(--gris-oscuro); font-size: 1.2rem;">Inventario Disponible</h2>
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
                <?php if (empty($productos)): ?>
                    <tr><td colspan="4" style="text-align: center;">No se encontraron productos.</td></tr>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--azul-fuerte);"><?php echo htmlspecialchars($producto['nombre']); ?></strong><br>
                                <small><?php echo htmlspecialchars($producto['marca'] ?? ''); ?></small>
                            </td>
                            <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                            
                            <td class="<?php echo ($producto['stock'] <= 10) ? 'stock-bajo' : ''; ?>">
                                <strong><?php echo round($producto['stock'], 2); ?></strong>
                            </td>
                            
                            <td>
                                <form action="index.php?ruta=agregar_carrito" method="POST" style="display: flex; gap: 5px;">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                    <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                                    <input type="hidden" name="id_departamento" value="<?php echo $_GET['dept'] ?? 1; ?>">
                                    
                                    <input type="number" step="0.01" min="0.01" name="cantidad" value="1" style="width: 70px;">
                                    
                                    <button type="submit" class="btn-primario" style="padding: 6px 12px;">+</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (isset($total_paginas) && $total_paginas > 1): ?>
            <div class="paginacion">
                <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="index.php?ruta=dashboard&dept=<?php echo $id_dept; ?>&buscar=<?php echo urlencode($busqueda ?? ''); ?>&pagina=<?php echo $i; ?>" 
                       class="<?php echo ($i == $pagina_actual) ? 'activo' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="flex: 1; min-width: 300px; background: var(--blanco); padding: 20px; border-radius: 8px; border: 1px solid var(--gris-borde); position: sticky; top: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="margin-top: 0; border-bottom: 1px solid var(--gris-borde); padding-bottom: 10px; color: var(--gris-oscuro);">Ticket Actual</h2>
        <?php 
        $total = 0; 
        if (!empty($_SESSION['carrito'])): ?>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($_SESSION['carrito'] as $id => $item): 
                    $subtotal = $item['precio'] * $item['cantidad'];
                    $total += $subtotal;
                ?>
                    <li style="border-bottom: 1px solid var(--gris-borde); padding: 12px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <strong style="color: var(--gris-oscuro);"><?php echo htmlspecialchars($item['nombre']); ?></strong>
                            <a href="index.php?ruta=eliminar_item&id=<?php echo $id; ?>&dept=<?php echo $_GET['dept'] ?? 1; ?>" style="color: var(--rojo-alerta); font-size: 12px; text-decoration: underline; font-weight: bold;">✕ Quitar</a>
                        </div>
                        <div style="color: var(--gris-texto); font-size: 14px;">
                            <?php echo $item['cantidad']; ?> x $<?php echo number_format($item['precio'], 2); ?> 
                            <strong style="float: right; color: var(--azul-fuerte);">$<?php echo number_format($subtotal, 2); ?></strong>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h2 style="text-align: right; color: var(--gris-oscuro); margin: 20px 0; font-size: 1.5rem;">Total: $<?php echo number_format($total, 2); ?></h2>
            <a href="index.php?ruta=cobrar" class="btn-primario" style="display: block; font-size: 16px; padding: 15px;">COBRAR TICKET</a>
        <?php else: ?>
            <p style="color: var(--gris-texto); text-align: center; margin-top: 40px;">El ticket está vacío.</p>
        <?php endif; ?>
    </div>
</div>