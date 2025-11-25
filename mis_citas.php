<?php
session_start();
include('includes/conexion.php');

// Solo usuarios logueados
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["usuario_id"];
$mensaje = "";

// ===============================
// CANCELAR CITA (si está pendiente)
// ===============================
if (isset($_GET['cancelar'])) {

    $id_cita = intval($_GET['cancelar']);

    // Comprobar que la cita pertenece al usuario y está pendiente
    $stmt = $conn->prepare("
        SELECT id_disponibilidad 
        FROM citas 
        WHERE id_cita = ? AND id_usuario = ? AND estado = 'pendiente'
    ");
    $stmt->bind_param("ii", $id_cita, $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {

        $fila = $res->fetch_assoc();
        $id_disp = $fila['id_disponibilidad'];

        // Borrar cita
        $del = $conn->prepare("DELETE FROM citas WHERE id_cita = ?");
        $del->bind_param("i", $id_cita);
        $del->execute();

        // Liberar disponibilidad
        $upd = $conn->prepare("UPDATE disponibilidad SET disponible = 1 WHERE id_disponibilidad = ?");
        $upd->bind_param("i", $id_disp);
        $upd->execute();

        $mensaje = "Cita cancelada correctamente.";

    } else {
        $mensaje = "No se puede cancelar esta cita.";
    }
}

// Obtener citas del usuario
$stmt = $conn->prepare("
    SELECT 
        c.id_cita,
        d.fecha,
        d.hora,
        c.servicio,
        c.estado
    FROM citas c
    INNER JOIN disponibilidad d ON c.id_disponibilidad = d.id_disponibilidad
    WHERE c.id_usuario = ?
    ORDER BY d.fecha, d.hora
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis citas | La Garduña</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width: 700px;">

    <h1 class="fw-bold mb-4">Mis citas</h1>

    <a href="index.php" class="btn btn-secondary btn-sm mb-3">Volver al inicio</a>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info alert-auto text-center"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['fecha'] ?></td>
                            <td><?= substr($row['hora'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($row['servicio']) ?></td>
                            <td>
                                <span class="badge 
                                    bg-<?= $row['estado'] == 'confirmada' ? 'success' : 'warning' ?>">
                                    <?= $row['estado'] ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($row['estado'] === 'pendiente'): ?>
                                    <a href="mis_citas.php?cancelar=<?= $row['id_cita'] ?>"
                                       class="btn btn-danger btn-sm"
                                       data-confirm="¿Seguro que deseas cancelar esta cita?">
                                        Cancelar
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>
        </div>

    <?php else: ?>
        <div class="alert alert-info text-center">
            Aún no tienes citas reservadas.
        </div>
    <?php endif; ?>

</div>

<script src="public/js/app.js"></script>

</body>
</html>
