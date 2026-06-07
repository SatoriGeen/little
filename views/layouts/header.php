<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>POS Profesional</title>
    <style>
        /* Estructura Global */
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar */
        .sidebar { flex: 0 0 260px; background-color: #2c3e50; color: white; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; font-size: 1.2rem; font-weight: bold; background: #1a252f; text-align: center; }
        .nav-links { list-style: none; padding: 0; margin: 0; }
        .nav-links a { display: block; padding: 15px 20px; color: #bdc3c7; text-decoration: none; border-bottom: 1px solid #34495e; transition: 0.3s; }
        .nav-links a:hover { background: #34495e; color: white; }
        
        /* Contenido Principal */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; background-color: #f4f6f8; }
        
        /* Barra Superior */
        .top-bar { background: white; padding: 15px 25px; border-bottom: 1px solid #ddd; display: flex; justify-content: flex-end; align-items: center; }
        
        /* Contenedor de contenido */
        .content-body { padding: 25px; }

        /* Estilo de Tablas Profesional */
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 10px; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th { background-color: #f8f9fa; color: #333; padding: 15px; text-align: left; border-bottom: 2px solid #dee2e6; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        tbody tr:hover { background-color: #f1f1f1; }
        
        /* Estilo alerta flash */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">POS Administración</div>
    <ul class="nav-links">
        <li><a href="index.php?ruta=dashboard">🛒 Ventas (POS)</a></li>
        <li><a href="index.php?ruta=inventario">📦 Inventario</a></li>
        
        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <li><a href="index.php?ruta=usuarios">👥 Usuarios</a></li>
        <?php endif; ?>
        
        <li><a href="index.php?ruta=logout" style="color: #e74c3c;">🚪 Cerrar Sesión</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar">
        <span>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
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