<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Gestión de Usuarios</h2>
    <a href="index.php?ruta=usuarios_crear" class="btn-primario">+ Nuevo Usuario</a>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?php echo htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                
                <td><?php echo htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                
                <td>
                    <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; 
                        <?php echo ($u['rol'] === 'admin') ? 'background: #e91e63; color: white;' : 'background: #f1f1f1;'; ?>">
                        <?php echo htmlspecialchars(ucfirst($u['rol']), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </td>
                
                <td>
                    <?php
                        // BUG-05 FIX: La ruta 'usuarios_eliminar' ahora existe en el router.
                        // SEC-04 FIX: La eliminación ahora es un formulario POST con CSRF,
                        // no un enlace GET que podía ser explotado de forma CSRF.
                        // Protección adicional: no se puede eliminar el propio usuario activo.
                        $es_usuario_activo = ($u['id_usuario'] == ($_SESSION['id_usuario'] ?? 0));
                    ?>
                    <?php if (!$es_usuario_activo): ?>
                        <form class="form-eliminar" action="index.php?ruta=usuarios_eliminar" method="POST"
                              onsubmit="return confirm('¿Seguro que deseas eliminar a <?php echo htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8'); ?>?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int) $u['id_usuario']; ?>">
                            <button type="submit" class="btn-eliminar" style="padding: 6px 12px; font-size: 13px;">Eliminar</button>
                        </form>
                    <?php else: ?>
                        <span style="color: #94A3B8; font-size: 12px; font-style: italic;">(Tú mismo)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>