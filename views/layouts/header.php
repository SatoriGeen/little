<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - Catálogo</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f4f6f8; }
        .navbar { background-color: #e91e63; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; margin-left: 15px; font-weight: bold; }
        .navbar a:hover { text-decoration: underline; }
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8bbd0; color: #333; }
        .stock-bajo { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="navbar">
    <div>
        <strong>POS Catálogo</strong>
    </div>
    <div>
        <span>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
        <a href="index.php?ruta=dashboard">Inicio</a>
        <a href="index.php?ruta=logout">Cerrar Sesión</a>
    </div>
</div>

<div class="container">