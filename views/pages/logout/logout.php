<?php 

/*=============================================
Cerrar sesión de forma completa y segura
=============================================*/

// Limpiar variable de sesión en memoria
$_SESSION = array();

// Invalidar la cookie de sesión en el navegador
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"], $params["secure"], $params["httponly"]
	);
}

// Destruir la sesión del servidor
if (session_status() == PHP_SESSION_ACTIVE) {
	session_destroy();
}

// Evitar que la sesión pueda ser reutilizada
session_regenerate_id(true);

/*=============================================
Redirigir al login sin cortar la caché del navegador
(Versiones de seguridad frente a "atrás" en móvil)
=============================================*/

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Location: /");
exit;

echo '<script>
window.location = "/";
</script>';


