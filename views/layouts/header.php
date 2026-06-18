<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Profesional</title>
    <style>
        :root {
            --fondo: #E2E8F0; --blanco: #F8FAFC; --blanco-puro: #FFFFFF;
            --azul-fuerte: #0D47A1; --azul-hover: #1565C0;
            --gris-oscuro: #1E293B; --gris-hover: #334155; --gris-texto: #334155;
            --gris-borde: #CBD5E1;
            --rojo-alerta: #DC2626; --azul-editar: #2563EB;
            --verde: #059669; --naranja: #D97706;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; display: flex; height: 100vh; overflow: hidden; background-color: var(--fondo); color: var(--gris-texto); }

        /* Sidebar */
        .sidebar { flex: 0 0 240px; background-color: var(--gris-oscuro); color: var(--blanco-puro); display: flex; flex-direction: column; box-shadow: 2px 0 5px rgba(0,0,0,0.1); z-index: 10; overflow-y: auto; }
        .sidebar-header { padding: 18px 20px; font-size: 1.1rem; font-weight: bold; background: #0F172A; text-align: center; border-bottom: 1px solid #334155; letter-spacing: 1px; color: var(--blanco-puro); }
        .nav-section { padding: 8px 12px 4px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #64748B; font-weight: bold; }
        .nav-links { list-style: none; padding: 0; margin: 0; }
        .nav-links a { display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #94A3B8; text-decoration: none; border-bottom: 1px solid #1E293B; transition: 0.2s; font-weight: 500; font-size: 0.9rem; }
        .nav-links a:hover { background: var(--azul-fuerte); color: var(--blanco-puro); padding-left: 25px; }
        .nav-links a .nav-icon { font-size: 1rem; width: 20px; text-align: center; }

        /* Contenido */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .top-bar { background: var(--blanco); padding: 12px 25px; border-bottom: 1px solid var(--gris-borde); display: flex; justify-content: flex-end; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); color: var(--gris-oscuro); font-weight: 500; font-size: 0.9rem; }
        .content-body { padding: 25px; }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; background: var(--blanco); margin-top: 10px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid var(--gris-borde); }
        th { background-color: var(--gris-oscuro); color: var(--blanco-puro); padding: 12px 15px; text-align: left; font-size: 12px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        td { padding: 12px 15px; border-bottom: 1px solid var(--gris-borde); color: var(--gris-texto); font-size: 0.9rem; }
        tbody tr:hover { background-color: var(--fondo); }

        /* Formularios */
        input, select { background-color: var(--blanco-puro); color: var(--gris-oscuro); border: 1px solid var(--gris-borde); border-radius: 4px; padding: 10px; font-family: inherit; }
        input:focus, select:focus { outline: none; border-color: var(--azul-fuerte); box-shadow: 0 0 0 3px rgba(13,71,161,0.15); }

        /* Alertas flash */
        .alert { padding: 14px 18px; margin-bottom: 20px; border-radius: 6px; font-weight: bold; border-left: 4px solid; background: var(--blanco); box-shadow: 0 1px 3px rgba(0,0,0,0.1); font-size: 0.9rem; }
        .alert-exito { color: var(--verde); border-color: var(--verde); }
        .alert-error { color: var(--rojo-alerta); border-color: var(--rojo-alerta); }

        /* Paginación */
        .paginacion { display: flex; justify-content: center; gap: 6px; margin-top: 20px; }
        .paginacion a { padding: 7px 12px; background: var(--blanco); color: var(--gris-texto); text-decoration: none; border-radius: 4px; border: 1px solid var(--gris-borde); font-weight: bold; transition: 0.2s; font-size: 0.85rem; }
        .paginacion a:hover { background: var(--fondo); border-color: var(--gris-oscuro); }
        .paginacion a.activo { background: var(--azul-fuerte); color: var(--blanco-puro); border-color: var(--azul-fuerte); }

        /* Botones */
        .btn-primario { background-color: var(--azul-fuerte); color: var(--blanco-puro); padding: 9px 14px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; transition: 0.2s; font-size: 0.9rem; }
        .btn-primario:hover { background-color: var(--azul-hover); box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .btn-secundario { background-color: var(--gris-oscuro); color: var(--blanco-puro); padding: 9px 14px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; transition: 0.2s; font-size: 0.9rem; }
        .btn-secundario:hover { background-color: var(--gris-hover); }
        .btn-outline { background-color: var(--blanco); color: var(--gris-oscuro); border: 1px solid var(--gris-oscuro); padding: 5px 10px; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none; font-size: 12px; transition: 0.2s; }
        .btn-outline:hover { background-color: var(--gris-oscuro); color: var(--blanco-puro); }
        .btn-editar { background-color: var(--azul-editar); color: var(--blanco-puro); padding: 5px 11px; border-radius: 4px; font-weight: bold; text-decoration: none; font-size: 12px; display: inline-block; border: none; transition: 0.2s; cursor: pointer; }
        .btn-editar:hover { background-color: #1D4ED8; box-shadow: 0 2px 4px rgba(37,99,235,0.3); }
        .btn-eliminar { background-color: var(--rojo-alerta); color: var(--blanco-puro); padding: 5px 11px; border-radius: 4px; font-weight: bold; text-decoration: none; font-size: 12px; display: inline-block; border: none; cursor: pointer; transition: 0.2s; }
        .btn-eliminar:hover { background-color: #B91C1C; box-shadow: 0 2px 4px rgba(220,38,38,0.3); }
        .form-eliminar { display: inline; margin: 0; padding: 0; }

        /* Badges de stock */
        .badge-sin_stock { background:#FEE2E2; color:#991B1B; border:1px solid #FECACA; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:bold; }
        .badge-critico   { background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:bold; }
        .badge-bajo      { background:#E0F2FE; color:#075985; border:1px solid #BAE6FD; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:bold; }
        .badge-normal    { background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:bold; }
        .stock-bajo      { color: var(--rojo-alerta) !important; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">Little Details</div>
    <nav>
        <p class="nav-section">Operaciones</p>
        <ul class="nav-links">
            <li><a href="index.php?ruta=dashboard"><span class="nav-icon">💰</span>Ventas</a></li>
            <li><a href="index.php?ruta=inventario"><span class="nav-icon">📦</span>Inventario</a></li>
            <li><a href="index.php?ruta=devoluciones"><span class="nav-icon">↩️</span>Devoluciones</a></li>
        </ul>

        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
        <p class="nav-section">Administración</p>
        <ul class="nav-links">
            <li><a href="index.php?ruta=reportes"><span class="nav-icon">📊</span>Analíticas</a></li>
            <li><a href="index.php?ruta=usuarios"><span class="nav-icon">👥</span>Usuarios</a></li>
            <li><a href="index.php?ruta=configuracion"><span class="nav-icon">⚙️</span>Catálogos</a></li>
        </ul>
        <?php endif; ?>

        <p class="nav-section">Sesión</p>
        <ul class="nav-links">
            <li><a href="index.php?ruta=logout"><span class="nav-icon">🚪</span>Cerrar Sesión</a></li>
        </ul>
    </nav>
</div>

<div class="main-content">
    <div class="top-bar">
        <span>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8'); ?></strong>
        &nbsp;|&nbsp; Rol: <strong><?php echo htmlspecialchars(ucfirst($_SESSION['rol'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
        </span>
    </div>

    <div class="content-body">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert <?php echo ($_SESSION['tipo'] === 'exito') ? 'alert-exito' : 'alert-error'; ?>">
                <?php
                    echo htmlspecialchars($_SESSION['mensaje'], ENT_QUOTES, 'UTF-8');
                    unset($_SESSION['mensaje'], $_SESSION['tipo']);
                ?>
            </div>
        <?php endif; ?>