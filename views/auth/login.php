<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Seguro</title>
    <style>
        /* Paleta Corporativa "Soft UI" */
        :root {
            --fondo: #E2E8F0;
            --blanco: #F8FAFC;
            --blanco-puro: #FFFFFF;
            --azul-fuerte: #0D47A1; 
            --azul-hover: #1565C0;
            --gris-oscuro: #1E293B; 
            --gris-texto: #334155; 
            --gris-borde: #CBD5E1;
            --rojo-alerta: #DC2626;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--fondo); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            color: var(--gris-texto);
        }

        .login-container { 
            background: var(--blanco); 
            padding: 40px 35px; 
            border-radius: 8px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
            border: 1px solid var(--gris-borde);
            width: 100%; 
            max-width: 380px; 
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 { 
            color: var(--gris-oscuro); 
            margin: 0 0 5px 0;
            font-size: 1.8rem;
        }

        .login-header p {
            margin: 0;
            font-size: 0.95rem;
            color: #64748B;
        }

        .form-group { 
            margin-bottom: 20px; 
        }

        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            color: var(--gris-oscuro); 
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input { 
            width: 100%; 
            padding: 12px; 
            background-color: var(--blanco-puro);
            color: var(--gris-oscuro);
            border: 1px solid var(--gris-borde); 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 1rem;
            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--azul-fuerte); 
            box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.15);
        }

        .btn { 
            width: 100%; 
            padding: 14px; 
            background-color: var(--azul-fuerte); 
            color: var(--blanco-puro); 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 1rem; 
            font-weight: bold;
            margin-top: 10px;
            transition: 0.2s;
        }

        .btn:hover { 
            background-color: var(--azul-hover); 
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .error { 
            color: #B91C1C; 
            background-color: rgba(239, 68, 68, 0.1); 
            border: 1px solid var(--rojo-alerta);
            padding: 12px; 
            border-radius: 6px; 
            margin-bottom: 20px; 
            text-align: center; 
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 25px;
            font-size: 0.8rem;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Login</h2>
            <p>Ingrese sus credenciales</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="error">⚠️<?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="index.php?ruta=login" method="POST">
            <div class="form-group">
                <label for="email">Usuario / Correo</label>
                <input type="email" id="email" name="email" required autocomplete="email" placeholder="admin@pos.com">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>
        
        <div class="footer-text">
            Sistema de Gestión Empresarial v1.0
        </div>
    </div>
</body>
</html>