<?php
// index.php

require_once 'config/database.php';

$db = new Database();
$conexion = $db->conectar();

if ($conexion) {
    echo "<h1>¡Conexión a la base de datos exitosa! 🚀</h1>";
}