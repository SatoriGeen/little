<?php
// helpers/csrf.php
// ============================================================
// Implementación de tokens CSRF para proteger todos los
// formularios y operaciones destructivas del sistema.
// ============================================================

/**
 * Genera (o recupera) el token CSRF de la sesión actual.
 * Usa random_bytes() para máxima entropía criptográfica.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Retorna el campo HTML oculto con el token CSRF listo
 * para insertarse en cualquier formulario.
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8')
        . '">';
}

/**
 * Valida el token CSRF enviado en la petición (POST o GET).
 * Si el token es inválido o falta, termina la ejecución con HTTP 403.
 * Usa hash_equals() para prevenir timing attacks.
 */
function csrf_validate(): void {
    $token_enviado  = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    $token_sesion   = $_SESSION['csrf_token'] ?? '';

    if (empty($token_sesion) || !hash_equals($token_sesion, $token_enviado)) {
        http_response_code(403);
        die('⛔ Solicitud rechazada: token de seguridad inválido o expirado. <a href="index.php">Volver al inicio</a>');
    }
}

/**
 * Regenera el token CSRF (llamar después de login o logout).
 */
function csrf_regenerate(): void {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
