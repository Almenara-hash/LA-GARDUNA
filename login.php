<?php
session_start();
include('includes/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario["password"])) {

            $_SESSION["usuario"] = $usuario["nombre"];
            $_SESSION["rol"] = $usuario["rol"];
            $_SESSION["id_usuario"] = $usuario["id_usuario"];

            // Redirección según rol
            if ($usuario["rol"] == "admin") {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit;

        } else {
            $mensaje = "❌ Contraseña incorrecta.";
        }
    } else {
        $mensaje = "❌ No existe una cuenta con ese correo.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar sesión | La Garduña</title>
<link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<h1>Inicio de sesión</h1>

<form method="POST" action="">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <input type="submit" value="Iniciar sesión">
</form>

<p style="color:red;">
    <?php if (!empty($mensaje)) echo $mensaje; ?>
</p>

<p><a href="register.php">¿No tienes cuenta? Regístrate</a></p>
</body>
</html>
