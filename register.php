<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('includes/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Comprobar si el correo ya existe
    $check = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $mensaje = "❌ El correo ya está registrado.";
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'cliente')");
        $stmt->bind_param("sss", $nombre, $email, $password);
        $stmt->execute();
        $mensaje = "✅ Registro exitoso. Ya puedes iniciar sesión.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro | La Garduña</title>
<link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<h1>Registro de usuario</h1>

<form method="POST" action="">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <input type="submit" value="Registrar">
</form>

<p style="color:green;">
    <?php if (!empty($mensaje)) echo $mensaje; ?>
</p>

<p><a href="login.php">¿Ya tienes cuenta? Inicia sesión</a></p>
</body>
</html>
