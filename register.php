<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('includes/conexion.php');

$mensaje = "";

// Registrar usuario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre   = trim($_POST["nombre"]);
    $email    = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    // Validaciones
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Formato de correo inválido.";
    } elseif ($password !== $password2) {
        $mensaje = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 6) {
        $mensaje = "La contraseña debe tener mínimo 6 caracteres.";
    } else {

        // ¿Correo duplicado?
        $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $mensaje = "El correo ya está registrado.";
        } else {

            // Insertar
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare("
                INSERT INTO usuarios (nombre, email, password, rol)
                VALUES (?, ?, ?, 'cliente')
            ");
            $insert->bind_param("sss", $nombre, $email, $hash);

            if ($insert->execute()) {
                header("Location: login.php?registrado=1");
                exit;
            } else {
                $mensaje = "Error al registrar. Revise la base de datos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro | La Garduña</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">

</head>

<body class="bg-light">

<div class="container py-5" style="max-width: 420px;">

    <h1 class="text-center fw-bold mb-4">Crear cuenta</h1>

    <div class="text-center mb-3">
        <a href="index.php" class="btn btn-secondary btn-sm">Volver</a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-danger alert-auto text-center">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="p-4 bg-white rounded shadow-sm">

        <div class="mb-3">
            <label class="form-label fw-semibold">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Correo electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
            <small class="text-muted">Mínimo 6 caracteres</small>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Repite la contraseña</label>
            <input type="password" name="password2" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Registrarse</button>

        <p class="text-center mt-3">
            ¿Ya tienes una cuenta?
            <a href="login.php">Iniciar sesión</a>
        </p>

    </form>
</div>

<script src="public/js/app.js"></script>

</body>
</html>
