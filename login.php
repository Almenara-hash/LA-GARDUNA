<?php
session_start();
include('includes/conexion.php');

$mensaje = "";

// Si el usuario envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Buscar usuario por email
    $stmt = $conn->prepare("SELECT id_usuario, nombre, email, password, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario["password"])) {

            // SESIONES CORRECTAS (las que usa TODO el proyecto)
            $_SESSION["usuario_id"]     = $usuario["id_usuario"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];
            $_SESSION["usuario_rol"]    = $usuario["rol"];

            header("Location: index.php");
            exit;

        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "No existe un usuario con ese correo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar sesión | La Garduña</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width: 400px;">

    <h1 class="text-center fw-bold mb-4">Iniciar sesión</h1>

    <div class="text-center mb-3">
        <a href="index.php" class="btn btn-secondary btn-sm">Volver</a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-danger alert-auto text-center"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST" class="p-4 bg-white shadow-sm rounded">

        <div class="mb-3">
            <label class="form-label fw-semibold">Correo electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>

        <p class="text-center mt-3">
            ¿No tienes cuenta?
            <a href="register.php">Regístrate</a>
        </p>

    </form>
</div>

<script src="public/js/app.js"></script>

</body>
</html>



