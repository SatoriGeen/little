<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>POS Profesional</title>
    <style>
        /* Paleta Corporativa "Soft UI" - Anti Fatiga Visual */
        :root {
            --fondo: #E2E8F0; /* Gris perla para el fondo principal, absorbe el exceso de luz */
            --blanco: #F8FAFC; /* Blanco mate/hueso para las tarjetas. Elimina el resplandor */
            --blanco-puro: #FFFFFF; /* Blanco puro SOLO para inputs, para que resalten */
            
            --azul-fuerte: #0D47A1; 
            --azul-hover: #1565C0;
            
            --gris-oscuro: #1E293B; /* Gris Carbón elegante para Sidebar y Encabezados */
            --gris-hover: #334155;
            --gris-texto: #334155; /* El texto no es negro puro, es gris muy oscuro para relajar la lectura */
            --gris-borde: #CBD5E1;
            
            --rojo-alerta: #DC2626;
            --azul-editar: #2563EB;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; display: flex; height: 100vh; overflow: hidden; background-color: var(--fondo); color: var(--gris-texto); }
        
        /* Sidebar - Gris Carbón */
        .sidebar { flex: 0 0 260px; background-color: var(--gris-oscuro); color: var(--blanco-puro); display: flex; flex-direction: column; box-shadow: 2px 0 5px rgba(0,0,0,0.1); z-index: 10; }
        .sidebar-header { padding: 20px; font-size: 1.2rem; font-weight: bold; background: #0F172A; text-align: center; border-bottom: 1px solid #334155; letter-spacing: 1px; color: var(--blanco-puro); }
        .nav-links { list-style: none; padding: 0; margin: 0; }
        .nav-links a { display: block; padding: 15px 20px; color: #94A3B8; text-decoration: none; border-bottom: 1px solid #334155; transition: 0.2s; font-weight: 500; }
        .nav-links a:hover { background: var(--azul-fuerte); color: var(--blanco-puro); padding-left: 25px; border-left: 4px solid var(--blanco-puro); }
        
        /* Contenido Principal */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .top-bar { background: var(--blanco); padding: 15px 25px; border-bottom: 1px solid var(--gris-borde); display: flex; justify-content: flex-end; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); color: var(--gris-oscuro); font-weight: 500; }
        .content-body { padding: 25px; }

        /* TABLA REDISEÑADA: Encabezado oscuro para no lastimar la vista */
        table { width: 100%; border-collapse: collapse; background: var(--blanco); margin-top: 10px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid var(--gris-borde); }
        
        /* EL SECRETO: Encabezado oscuro ancla la vista y reduce el área blanca brillante */
        th { background-color: var(--gris-oscuro); color: var(--blanco-puro); padding: 15px; text-align: left; font-size: 13px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        td { padding: 15px; border-bottom: 1px solid var(--gris-borde); color: var(--gris-texto); }
        tbody tr:hover { background-color: var(--fondo); } /* Hover suave */

        /* Formularios */
        input, select { background-color: var(--blanco-puro); color: var(--gris-oscuro); border: 1px solid var(--gris-borde); border-radius: 4px; padding: 10px; font-family: inherit; }
        input:focus, select:focus { outline: none; border-color: var(--azul-fuerte); box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.15); }
        
        /* Alertas */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; border-left: 4px solid; background: var(--blanco); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .alert-exito { color: var(--azul-fuerte); border-color: var(--azul-fuerte); }
        .alert-error { color: var(--rojo-alerta); border-color: var(--rojo-alerta); } 

        /* Paginación */
        .paginacion { display: flex; justify-content: center; gap: 8px; margin-top: 20px; }
        .paginacion a { padding: 8px 12px; background: var(--blanco); color: var(--gris-texto); text-decoration: none; border-radius: 4px; border: 1px solid var(--gris-borde); font-weight: bold; transition: 0.2s; }
        .paginacion a:hover { background: var(--fondo); border-color: var(--gris-oscuro); color: var(--gris-oscuro); }
        .paginacion a.activo { background: var(--azul-fuerte); color: var(--blanco-puro); border-color: var(--azul-fuerte); }
        
        /* Sistema de Botones Base */
        .btn-primario { background-color: var(--azul-fuerte); color: var(--blanco-puro); padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; transition: 0.2s; }
        .btn-primario:hover { background-color: var(--azul-hover); box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        
        .btn-secundario { background-color: var(--gris-oscuro); color: var(--blanco-puro); padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; text-align: center; transition: 0.2s; }
        .btn-secundario:hover { background-color: var(--gris-hover); }

        .btn-outline { background-color: var(--blanco); color: var(--gris-oscuro); border: 1px solid var(--gris-oscuro); padding: 5px 10px; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none; font-size: 12px; transition: 0.2s; }
        .btn-outline:hover { background-color: var(--gris-oscuro); color: var(--blanco-puro); }

        /* Botones Acciones en Tabla */
        .btn-editar { background-color: var(--azul-editar); color: var(--blanco-puro); padding: 6px 12px; border-radius: 4px; font-weight: bold; text-decoration: none; font-size: 13px; display: inline-block; border: none; transition: 0.2s; }
        .btn-editar:hover { background-color: #1D4ED8; color: var(--blanco-puro); box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3); }
        
        .btn-eliminar { background-color: var(--rojo-alerta); color: var(--blanco-puro); padding: 6px 12px; border-radius: 4px; font-weight: bold; text-decoration: none; font-size: 13px; display: inline-block; border: none; transition: 0.2s; }
        .btn-eliminar:hover { background-color: #B91C1C; color: var(--blanco-puro); box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3); }

        .stock-bajo { color: var(--rojo-alerta) !important; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">POS Administración</div>
    <ul class="nav-links">
        <li><a href="index.php?ruta=dashboard">🛒 Ventas (POS)</a></li>
        <li><a href="index.php?ruta=inventario">📦 Inventario</a></li>
        
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <li><a href="index.php?ruta=usuarios">👥 Usuarios</a></li>
        <?php endif; ?>
        
        <li><a href="index.php?ruta=logout" style="color: #ECEFF1; border-top: 1px solid #334155;">🚪 Cerrar Sesión</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar">
        <span>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></strong></span>
    </div>

    <div class="content-body">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert <?php echo ($_SESSION['tipo'] == 'exito') ? 'alert-exito' : 'alert-error'; ?>">
                <?php echo $_SESSION['mensaje']; ?>
            </div>
            <?php 
                unset($_SESSION['mensaje']);
                unset($_SESSION['tipo']);
            ?>
        <?php endif; ?>