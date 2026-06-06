<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS Catálogo</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #fce4ec; /* Un tono rosa muy suave */
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .login-container { 
            background: #fff; 
            padding: 40px 30px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 350px; 
        }
        .login-container h2 { 
            text-align: center; 
            color: #333; 
            margin-bottom: 20px;
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            color: #666; 
            font-size: 14px;
        }
        .form-group input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
        }
        .form-group input:focus {
            outline: none;
            border-color: #e91e63; /* Rosa oscuro activo */
        }
        .btn { 
            width: 100%; 
            padding: 12px; 
            background-color: #e91e63; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold;
            margin-top: 10px;
        }
        .btn:hover { 
            background-color: #c2185b; 
        }
        .error { 
            color: #d32f2f; 
            background-color: #ffebee; 
            padding: 10px; 
            border-radius: 4px; 
            margin-bottom: 15px; 
            text-align: center; 
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Acceso POS</h2>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="index.php?ruta=login" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Ingresar</button>
        </form>
    </div>
</body>
</html>