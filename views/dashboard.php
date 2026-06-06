<h2>Inventario Actual</h2>

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
                
                <td class="<?php echo ($producto['stock'] < 25) ? 'stock-bajo' : ''; ?>">
                    <?php echo $producto['stock']; ?>
                </td>
                
                <td>
                    <button style="padding: 5px 10px; cursor: pointer;">Vender</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>