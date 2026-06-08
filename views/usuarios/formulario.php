<div style="max-width: 600px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h2>Nuevo Usuario</h2>
    <form action="index.php?ruta=usuarios_guardar" method="POST">
        <div style="margin-bottom: 15px;">
            <label>Nombre Completo:</label>
            <input type="text" name="nombre" required style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label>Email (Usuario):</label>
            <input type="email" name="email" required style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 15px;">
            <label>Contraseña:</label>
            <input type="password" name="password" required style="width: 100%; padding: 8px;">
        </div>
        <div style="margin-bottom: 20px;">
            <label>Rol:</label>
            <select name="rol" style="width: 100%; padding: 8px;">
                <option value="cajero">Cajero</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn-primario">Guardar Usuario</button>
        <a href="index.php?ruta=usuarios" style="margin-left: 10px; color: #666;">Cancelar</a>
    </form>
</div>