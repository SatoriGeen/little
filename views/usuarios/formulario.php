<style>
    .form-card { background: var(--blanco); padding: 35px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--gris-borde); max-width: 600px; margin: 20px auto; }
    .form-header { margin-bottom: 25px; border-bottom: 1px solid var(--gris-borde); padding-bottom: 15px; }
    .form-header h2 { margin: 0; color: var(--gris-oscuro); font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
    .form-header p { margin: 8px 0 0 0; color: var(--gris-texto); font-size: 0.95rem; }
    .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
    .form-label { font-weight: 600; color: var(--gris-oscuro); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-input { background-color: var(--blanco-puro); color: var(--gris-oscuro); border: 1px solid var(--gris-borde); border-radius: 6px; padding: 12px; font-size: 1rem; transition: 0.3s; }
    .form-input:focus { outline: none; border-color: var(--azul-fuerte); box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.1); }
    .form-actions { margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px; border-top: 1px solid var(--gris-borde); padding-top: 25px; }
</style>

<div class="form-card">
    <div class="form-header">
        <h2>Registrar Nuevo Usuario</h2>
        <p>Crea un acceso para un nuevo cajero o administrador.</p>
    </div>

    <form action="index.php?ruta=usuarios_guardar" method="POST">
        
        <div class="form-group">
            <label class="form-label">Nombre Completo *</label>
            <input type="text" name="nombre" class="form-input" required placeholder="Ej. Juan Pérez">
        </div>

        <div class="form-group">
            <label class="form-label">Email (Usuario de Acceso) *</label>
            <input type="email" name="email" class="form-input" required placeholder="correo@tienda.com">
        </div>

        <div class="form-group">
            <label class="form-label">Contraseña Segura *</label>
            <input type="password" name="password" class="form-input" required placeholder="••••••••">
        </div>

        <div class="form-group">
            <label class="form-label">Nivel de Permisos *</label>
            <select name="rol" class="form-input" required>
                <option value="cajero">Cajero (Solo Ventas)</option>
                <option value="admin">Administrador (Control Total)</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="index.php?ruta=usuarios" class="btn-outline" style="padding: 12px 20px; font-size: 14px;">Cancelar</a>
            <button type="submit" class="btn-primario" style="padding: 12px 25px; font-size: 14px;">💾 Crear Usuario</button>
        </div>
        
    </form>
</div>