<?php
// config/env.php

function cargarEnv($ruta) {
    // Verificamos que el archivo exista
    if (!file_exists($ruta)) {
        throw new Exception("El archivo .env no existe.");
    }

    // Leemos el archivo línea por línea ignorando las vacías
    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lineas as $linea) {
        // Ignorar los comentarios
        if (strpos(trim($linea), '#') === 0) {
            continue;
        }

        // Separar la clave y el valor
        list($nombre, $valor) = explode('=', $linea, 2);
        
        // Guardar en la variable global $_ENV
        $_ENV[trim($nombre)] = trim($valor);
    }
}

// Ejecutamos la función apuntando a la raíz del proyecto
cargarEnv(__DIR__ . '/../.env');