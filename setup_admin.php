<?php
// setup_admin.php (Archivo temporal para crear al primer usuario)

require_once 'config/database.php';

$database = new Database();
$conexion = $database->conectar();

// Los datos de nuestro primer administrador
$nombre = "Administrador Principal";
$email = "admin@pos.com";
$password_plana = "admin123"; 

// Aquí aplicamos el algoritmo de encriptación bcrypt (nativo de PHP)
$password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

try {
    $query = "INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (:nombre, :email, :password_hash, 'admin')";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);
    
    if ($stmt->execute()) {
        echo "Usuario administrador creado con éxito.<br>";
        echo "Email: " . $email . "<br>";
        echo "Contraseña: " . $password_plana . "<br>";
        echo "<br>Hash guardado en BD: " . $password_hash . "<br>";
        echo "<br><a href='index.php'>Ir al Login</a>";
    }
} catch(PDOException $e) {
    echo "Error (probablemente el usuario ya existe): " . $e->getMessage();
}