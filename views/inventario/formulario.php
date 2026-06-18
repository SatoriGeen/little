<style>
    .form-card { background: var(--blanco); padding: 35px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--gris-borde); max-width: 800px; margin: 20px auto; }
    .form-header { margin-bottom: 25px; border-bottom: 1px solid var(--gris-borde); padding-bottom: 15px; }
    .form-header h2 { margin: 0; color: var(--gris-oscuro); font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
    .form-header p { margin: 8px 0 0 0; color: var(--gris-texto); font-size: 0.95rem; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-group.full-width { grid-column: span 2; }
    .form-label { font-weight: 600; color: var(--gris-oscuro); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-input { background-color: var(--blanco-puro); color: var(--gris-oscuro); border: 1px solid var(--gris-borde); border-radius: 6px; padding: 12px; font-size: 1rem; transition: 0.3s; }
    .form-input:focus { outline: none; border-color: var(--azul-fuerte); box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.1); }
    .form-actions { margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px; border-top: 1px solid var(--gris-borde); padding-top: 25px; }
</style>

<div class="form-card">
    <div class="form-header">
        <h2><?php echo !empty($id) ? 'Editar Producto' : 'Registrar Nuevo Producto'; ?></h2>
        <p>Complete los detalles técnicos y comerciales del producto para el inventario.</p>
    </div>

    <!-- SEC-03 FIX: Token CSRF + BUG-04 FIX: htmlspecialchars() en el atributo action -->
    <form action="index.php?ruta=<?php echo htmlspecialchars($accion, ENT_QUOTES, 'UTF-8'); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-grid">
            
            <div class="form-group full-width">
                <label class="form-label">Nombre del Producto *</label>
                <input type="text" name="nombre" class="form-input" required 
                       value="<?php echo htmlspecialchars($producto['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                       placeholder="Ej. Refresco Coca-Cola 600ml">
            </div>

            <div class="form-group">
                <label class="form-label">Código de Barras</label>
                <input type="text" name="codigo_barras" class="form-input" 
                       value="<?php echo htmlspecialchars($producto['codigo_barras'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                       placeholder="Escanea o escribe el código">
            </div>

            <div class="form-group">
                <label class="form-label">Precio Público ($) *</label>
                <input type="number" step="0.01" min="0" name="precio" class="form-input" required 
                       value="<?php echo htmlspecialchars($producto['precio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                       placeholder="0.00">
            </div>

            <div class="form-group">
                <label class="form-label">Stock Actual *</label>
                <input type="number" step="0.01" min="0" name="stock" class="form-input" required 
                       value="<?php echo htmlspecialchars($producto['stock'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                       placeholder="Cantidad disponible">
            </div>

            <div class="form-group">
                <label class="form-label">Departamento *</label>
                <select name="id_departamento" class="form-input" required>
                    <?php foreach ($departamentos as $d): ?>
                        <option value="<?php echo (int) $d['id_departamento']; ?>" 
                            <?php echo (($producto['id_departamento'] ?? $_GET['dept'] ?? 1) == $d['id_departamento']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($d['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Marca</label>
                <select name="id_marca" class="form-input">
                    <option value="">-- Sin Marca --</option>
                    <?php foreach ($marcas as $m): ?>
                        <option value="<?php echo (int) $m['id_marca']; ?>" 
                            <?php echo (($producto['id_marca'] ?? '') == $m['id_marca']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Categoría</label>
                <select name="id_categoria" class="form-input">
                    <option value="">-- Sin Categoría --</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?php echo (int) $c['id_categoria']; ?>" 
                            <?php echo (($producto['id_categoria'] ?? '') == $c['id_categoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <div class="form-actions">
            <a href="index.php?ruta=inventario&amp;dept=<?php echo (int) ($_GET['dept'] ?? 1); ?>" class="btn-outline" style="padding: 12px 20px; font-size: 14px;">Cancelar</a>
            <button type="submit" class="btn-primario" style="padding: 12px 25px; font-size: 14px;">Guardar Producto</button>
        </div>
    </form>
</div>