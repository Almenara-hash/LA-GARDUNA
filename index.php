<?php
session_start();
include('includes/conexion.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>La Garduña | Inicio</title>
<link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<h1>Bienvenido a La Garduña</h1>

<?php if (!isset($_SESSION["usuario"])): ?>
    <p><a href="login.php">Inicia sesión</a> o <a href="register.php">Regístrate</a></p>
<?php else: ?>
    <?php if (isset($_SESSION["usuario"]) && $_SESSION["rol"] === "cliente"): ?>
    <p><a href="reservar.php">Reservar una cita</a></p>
<?php endif; ?>

    <p>Hola, <strong><?= $_SESSION["usuario"] ?></strong> </p>
    <?php if ($_SESSION["rol"] === "admin"): ?>
        <p><a href="admin.php">Ir al panel de administración</a></p>
    <?php endif; ?>
    <p><a href="logout.php">Cerrar sesión</a></p>
<?php endif; ?>
</body>
</html>

