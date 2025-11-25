<?php
session_start();

// Vaciar todas las variables de sesión
$_SESSION = [];

// Destruir sesión
session_unset();
session_destroy();

// Evitar volver atrás después del logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Location: index.php");
exit;
?>

