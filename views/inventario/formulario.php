<h2><?php echo $producto ? 'Editar Producto' : 'Agregar Nuevo Producto'; ?></h2>

<div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 600px;">
    
    <form action="index.php?ruta=<?php echo $accion; ?>" method="POST">
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Código de Barras (Opcional):</label>
            <input type="text" name="codigo_barras" style="width: 100%; padding: 8px; box-sizing: border-box; border: 2px solid #ccc; border-radius: 4px;" 
                   value="<?php echo $producto ? htmlspecialchars($producto['codigo_barras'] ?? '') : ''; ?>"
                   placeholder="Haz clic aquí y escanea el producto...">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Nombre del Producto:</label>
            <input type="text" name="nombre" required style="width: 100%; padding: 8px; box-sizing: border-box;" 
                   value="<?php echo $producto ? htmlspecialchars($producto['nombre']) : ''; ?>">
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Precio ($):</label>
                <input type="number" step="0.01" name="precio" required style="width: 100%; padding: 8px; box-sizing: border-box;"
                       value="<?php echo $producto ? $producto['precio'] : ''; ?>">
            </div>
            
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Stock Inicial (Acepta decimales):</label>
                <input type="number" step="0.001" name="stock" required style="width: 100%; padding: 8px; box-sizing: border-box;"
                       value="<?php echo $producto ? $producto['stock'] : '0'; ?>">
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Marca:</label>
                <select name="id_marca" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="">Seleccione una marca...</option>
                    <?php foreach ($marcas as $marca): ?>
                        <option value="<?php echo $marca['id_marca']; ?>" 
                            <?php echo ($producto && $producto['id_marca'] == $marca['id_marca']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($marca['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="id_departamento" value="<?php echo $id_dept; ?>">
        
        </div>

        <div style="text-align: right;">
            <a href="index.php?ruta=inventario" style="padding: 10px 15px; text-decoration: none; color: #333; margin-right: 10px;">Cancelar</a>
            <button type="submit" class="btn-primario">Guardar Producto</button>
        </div>

    </form>
</div>