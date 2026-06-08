<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Gestión de Usuarios</h2>
    <a href="index.php?ruta=usuarios_crear" class="btn-primario">+ Nuevo Usuario</a>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                
                <td>
                    <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; 
                        <?php echo ($u['rol'] == 'admin') ? 'background: #e91e63; color: white;' : 'background: #f1f1f1;'; ?>">
                        <?php echo ucfirst($u['rol']); ?>
                    </span>
                </td>
                
                <td>
                    <a href="index.php?ruta=usuarios_eliminar&id=<?php echo $u['id_usuario']; ?>" 
                       onclick="return confirm('¿Seguro que deseas eliminar este usuario?');" 
                       style="color: red; text-decoration: none;">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>