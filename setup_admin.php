<?php
// setup_admin.php
// ============================================================
// SEC-02 FIX: Este script fue deshabilitado por seguridad.
// Fue creado para generar el primer usuario administrador y
// NO debe estar accesible públicamente en producción.
//
// Si necesitas crear un usuario admin, hazlo directamente
// desde la consola de MySQL o mediante un script de CLI
// protegido por contraseña fuera del document root de Apache.
// ============================================================

http_response_code(403);
die('⛔ Acceso denegado. Este script ha sido deshabilitado.');