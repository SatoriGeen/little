<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Maquillaje</title>
</head>
<body>
    <h1>Nuestros Productos</h1>
    
    <ul>
        <?php foreach ($productos as $producto): ?>
            <li>
                <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong> 
                (<?php echo htmlspecialchars($producto['marca'] ?? 'Sin marca'); ?>) 
                - $<?php echo htmlspecialchars($producto['precio']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>