<style>
    .config-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; align-items: start; }
    .config-card { background: var(--blanco); padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--gris-borde); }
    .config-card h3 { margin: 0 0 15px 0; color: var(--gris-oscuro); border-bottom: 2px solid var(--fondo); padding-bottom: 10px; font-size: 1.2rem; }
    .config-form { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; background: var(--fondo); padding: 15px; border-radius: 6px; }
    
    /* Tablas pequeñas internas */
    .mini-table th { font-size: 11px; padding: 10px; }
    .mini-table td { font-size: 13px; padding: 10px; }
    
    .scroll-box { max-height: 350px; overflow-y: auto; }
</style>

<div style="margin-bottom: 20px;">
    <h2 style="margin: 0; color: var(--gris-oscuro);">Catálogos Generales</h2>
    <p style="color: var(--gris-texto); margin-top: 5px;">Administre la estructura del inventario (Departamentos, Marcas y Categorías).</p>
</div>

<div class="config-grid">
    
    <!-- TARJETA 1: DEPARTAMENTOS -->
    <div class="config-card">
        <h3>🏬 Departamentos</h3>
        <!-- SEC-03 FIX: Token CSRF en formulario de agregar -->
        <form action="index.php?ruta=configuracion_guardar" method="POST" class="config-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="tipo" value="departamento">
            <input type="text" name="nombre" placeholder="Nuevo Departamento..." class="form-input" required>
            <button type="submit" class="btn-primario">Agregar</button>
        </form>

        <div class="scroll-box">
            <table class="mini-table">
                <thead><tr><th>Nombre</th><th style="width: 60px;">Acción</th></tr></thead>
                <tbody>
                    <?php foreach ($departamentos as $d): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($d['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td>
                                <!-- SEC-04 FIX: Eliminar con formulario POST + CSRF -->
                                <form class="form-eliminar" action="index.php?ruta=configuracion_eliminar" method="POST"
                                      onsubmit="return confirm('¿Borrar este departamento?');">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="tipo" value="departamento">
                                    <input type="hidden" name="id"   value="<?php echo (int) $d['id_departamento']; ?>">
                                    <button type="submit" class="btn-eliminar" style="padding: 4px 8px; font-size: 11px;">✕</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TARJETA 2: MARCAS -->
    <div class="config-card">
        <h3>🏷️ Marcas</h3>
        <form action="index.php?ruta=configuracion_guardar" method="POST" class="config-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="tipo" value="marca">
            <input type="text" name="nombre" placeholder="Nueva Marca..." class="form-input" required>
            <select name="id_departamento" class="form-input" required>
                <option value="">-- Pertenece al Depto --</option>
                <?php foreach ($departamentos as $d): ?>
                    <option value="<?php echo (int) $d['id_departamento']; ?>"><?php echo htmlspecialchars($d['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-primario">Agregar</button>
        </form>

        <div class="scroll-box">
            <table class="mini-table">
                <thead><tr><th>Marca</th><th>Depto</th><th style="width: 60px;">Acción</th></tr></thead>
                <tbody>
                    <?php foreach ($marcas as $m): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td style="font-size: 11px;"><?php echo htmlspecialchars($m['departamento'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form class="form-eliminar" action="index.php?ruta=configuracion_eliminar" method="POST"
                                      onsubmit="return confirm('¿Borrar esta marca?');">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="tipo" value="marca">
                                    <input type="hidden" name="id"   value="<?php echo (int) $m['id_marca']; ?>">
                                    <button type="submit" class="btn-eliminar" style="padding: 4px 8px; font-size: 11px;">✕</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TARJETA 3: CATEGORÍAS -->
    <div class="config-card">
        <h3>📂 Categorías</h3>
        <form action="index.php?ruta=configuracion_guardar" method="POST" class="config-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="tipo" value="categoria">
            <input type="text" name="nombre" placeholder="Nueva Categoría..." class="form-input" required>
            <select name="id_departamento" class="form-input" required>
                <option value="">-- Pertenece al Depto --</option>
                <?php foreach ($departamentos as $d): ?>
                    <option value="<?php echo (int) $d['id_departamento']; ?>"><?php echo htmlspecialchars($d['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-primario">Agregar</button>
        </form>

        <div class="scroll-box">
            <table class="mini-table">
                <thead><tr><th>Categoría</th><th>Depto</th><th style="width: 60px;">Acción</th></tr></thead>
                <tbody>
                    <?php foreach ($categorias as $c): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td style="font-size: 11px;"><?php echo htmlspecialchars($c['departamento'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form class="form-eliminar" action="index.php?ruta=configuracion_eliminar" method="POST"
                                      onsubmit="return confirm('¿Borrar esta categoría?');">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="tipo" value="categoria">
                                    <input type="hidden" name="id"   value="<?php echo (int) $c['id_categoria']; ?>">
                                    <button type="submit" class="btn-eliminar" style="padding: 4px 8px; font-size: 11px;">✕</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>