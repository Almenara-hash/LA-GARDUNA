<?php
session_start();
include('includes/conexion.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["usuario_id"];
$mensaje = "";

// Si el usuario envía el formulario de reserva
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_disponibilidad = intval($_POST["id_disponibilidad"]);
    $servicio = trim($_POST["servicio"]);

    // 1. Comprobar que la disponibilidad existe y está libre
    $check = $conn->prepare("SELECT disponible FROM disponibilidad WHERE id_disponibilidad = ?");
    $check->bind_param("i", $id_disponibilidad);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        $mensaje = "La hora seleccionada no existe.";
    } else {
        $row = $res->fetch_assoc();

        if ($row["disponible"] == 0) {
            $mensaje = "Esa hora ya fue reservada. Actualiza la página.";
        } else {

            // 2. Insertar la cita
            $stmt = $conn->prepare("
                INSERT INTO citas (id_usuario, id_disponibilidad, servicio, estado)
                VALUES (?, ?, ?, 'pendiente')
            ");
            $stmt->bind_param("iis", $id_usuario, $id_disponibilidad, $servicio);
            $stmt->execute();

            // 3. Marcar disponibilidad como ocupada
            $update = $conn->prepare("UPDATE disponibilidad SET disponible = 0 WHERE id_disponibilidad = ?");
            $update->bind_param("i", $id_disponibilidad);
            $update->execute();

            $mensaje = "Cita reservada con éxito.";
        }
    }
}

// 4. Obtener disponibilidades (futuras) de forma segura
$disponibles = $conn->prepare("
    SELECT id_disponibilidad, fecha, hora 
    FROM disponibilidad 
    WHERE disponible = 1 AND fecha >= CURDATE()
    ORDER BY fecha, hora
");
$disponibles->execute();
$result = $disponibles->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reservar cita | La Garduña</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="public/css/style.css">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width: 500px;">

    <h1 class="text-center fw-bold mb-4">Reservar una cita</h1>

    <div class="text-center mb-3">
        <a href="index.php" class="btn btn-secondary btn-sm">Volver al inicio</a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info alert-auto text-center"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>

        <form method="POST" class="p-4 bg-white rounded shadow-sm">

            <!-- Selección de servicio -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Selecciona el servicio</label>
                <select class="form-select" name="servicio" required>
                    <option value="" disabled selected>Elige un servicio</option>
                    <option value="Corte de pelo (12 €)">Corte de pelo — 12 €</option>
                    <option value="Arreglo de barba (4 €)">Arreglo de barba — 4 €</option>
                    <option value="Lavado de cabeza (4 €)">Lavado de cabeza — 4 €</option>
                </select>
            </div>

            <!-- Fechas disponibles -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Selecciona fecha y hora</label>
                <select class="form-select" name="id_disponibilidad" required>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['id_disponibilidad'] ?>">
                            <?= $row['fecha'] ?> — <?= substr($row['hora'], 0, 5) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Reservar cita</button>

        </form>

    <?php else: ?>
        <div class="alert alert-danger text-center">
            No hay horarios disponibles actualmente.
        </div>
    <?php endif; ?>

</div>

<script src="public/js/app.js"></script>

</body>
</html>
