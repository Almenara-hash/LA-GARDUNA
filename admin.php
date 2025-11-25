<?php
session_start();
include('includes/conexion.php');

// ================================
//   PROTEGER PANEL: SOLO ADMIN
// ================================
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$mensaje = "";

// ================================
//   FUNCIÓN PARA PRECIO SEGÚN SERVICIO
// ================================
function obtenerPrecio($servicio)
{
    $s = strtolower($servicio);

    if (strpos($s, 'corte') !== false) return "12 €";
    if (strpos($s, 'barba') !== false) return "4 €";
    if (strpos($s, 'lavado') !== false) return "4 €";

    return "-";
}

// ================================
//   CONFIRMAR CITA
// ================================
if (isset($_GET['confirmar'])) {
    $id = intval($_GET['confirmar']);

    $stmt = $conn->prepare("UPDATE citas SET estado='confirmada' WHERE id_cita=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $mensaje = "Cita confirmada correctamente.";
}

// ================================
//   CANCELAR CITA + LIBERAR DISPONIBILIDAD
// ================================
if (isset($_GET['cancelar'])) {
    $id = intval($_GET['cancelar']);

    $stmt = $conn->prepare("SELECT id_disponibilidad FROM citas WHERE id_cita=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {

        $fila = $res->fetch_assoc();
        $id_disp = $fila['id_disponibilidad'];

        $del = $conn->prepare("DELETE FROM citas WHERE id_cita=?");
        $del->bind_param("i", $id);
        $del->execute();

        $upd = $conn->prepare("UPDATE disponibilidad SET disponible=1 WHERE id_disponibilidad=?");
        $upd->bind_param("i", $id_disp);
        $upd->execute();

        $mensaje = "Cita cancelada y disponibilidad liberada.";
    }
}

// ================================
//   AÑADIR DISPONIBILIDAD
// ================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nueva_fecha'], $_POST['nueva_hora'])) {

    $fecha = $_POST['nueva_fecha'];
    $hora = $_POST['nueva_hora'];

    // Horario permitido
    $mananaInicio = "10:00";
    $mananaFin    = "14:00";
    $tardeInicio  = "17:00";
    $tardeFin     = "20:30";

    if (
        !(
            ($hora >= $mananaInicio && $hora <= $mananaFin) ||
            ($hora >= $tardeInicio  && $hora <= $tardeFin)
        )
    ) {
        $mensaje = "Hora fuera del horario permitido.";
    } else {

        // EVITAR DUPLICADOS
        $check = $conn->prepare("SELECT id_disponibilidad FROM disponibilidad WHERE fecha=? AND hora=?");
        $check->bind_param("ss", $fecha, $hora);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $mensaje = "Ya existe disponibilidad en ese horario.";
        } else {
            $stmt = $conn->prepare("INSERT INTO disponibilidad (fecha, hora, disponible) VALUES (?, ?, 1)");
            $stmt->bind_param("ss", $fecha, $hora);
            $stmt->execute();
            $mensaje = "Nueva disponibilidad añadida.";
        }
    }
}

// ================================
//   ELIMINAR DISPONIBILIDAD
// ================================
if (isset($_GET['eliminar_disp'])) {
    $id = intval($_GET['eliminar_disp']);

    // SOLO SI NO TIENE CITAS
    $check = $conn->prepare("SELECT * FROM citas WHERE id_disponibilidad=?");
    $check->bind_param("i", $id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        $del = $conn->prepare("DELETE FROM disponibilidad WHERE id_disponibilidad=?");
        $del->bind_param("i", $id);
        $del->execute();

        $mensaje = "Disponibilidad eliminada.";
    } else {
        $mensaje = "No se puede eliminar: tiene citas asociadas.";
    }
}

// ================================
//   CONSULTAS
// ================================
$citas = $conn->query("
    SELECT 
        c.id_cita,
        u.nombre AS cliente,
        u.email,
        d.fecha,
        d.hora,
        c.servicio,
        c.estado
    FROM citas c
    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
    INNER JOIN disponibilidad d ON c.id_disponibilidad = d.id_disponibilidad
    ORDER BY d.fecha, d.hora
");

$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY id_usuario");
$disponibilidad = $conn->query("SELECT * FROM disponibilidad ORDER BY fecha, hora");

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Administración | La Garduña</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Panel de Administración</h1>
        <a href="logout.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info alert-auto shadow-sm text-center">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <div class="alert alert-secondary shadow-sm">
        Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong>
    </div>

    <hr>

    <!-- =====================
         GESTIÓN DE CITAS
    ====================== -->
    <h2 class="mb-3">Citas registradas</h2>

    <div class="table-responsive mb-4">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Servicio</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($citas->num_rows > 0): ?>
                    <?php while ($c = $citas->fetch_assoc()): ?>
                        <tr>
                            <td><?= $c['id_cita'] ?></td>
                            <td><?= htmlspecialchars($c['cliente']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= $c['fecha'] ?></td>
                            <td><?= substr($c['hora'], 0, 5) ?></td>
                            <td><?= htmlspecialchars($c['servicio']) ?></td>
                            <td><strong><?= obtenerPrecio($c['servicio']) ?></strong></td>

                            <td>
                                <span class="badge bg-<?= $c['estado'] === 'confirmada' ? 'success' : 'warning' ?>">
                                    <?= $c['estado'] ?>
                                </span>
                            </td>

                            <td>
                                <a href="admin.php?confirmar=<?= $c['id_cita'] ?>" 
                                   class="btn btn-success btn-sm">
                                   ✔
                                </a>

                                <a href="admin.php?cancelar=<?= $c['id_cita'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   data-confirm="¿Cancelar esta cita?">
                                   ✖
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr><td colspan="9" class="text-center">No hay citas registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr>

    <!-- =====================
         USUARIOS
    ====================== -->
    <h2 class="mb-3">Usuarios registrados</h2>

    <table class="table table-bordered table-striped mb-4">
        <thead class="table-dark">
            <tr>
                <th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
            <tr>
                <td><?= $u['id_usuario'] ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= $u['rol'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <hr>

    <!-- =====================
         DISPONIBILIDAD
    ====================== -->
    <h2 class="mb-3">Disponibilidad del negocio</h2>

    <p class="text-muted">Horario permitido: <strong>10:00–14:00</strong> y <strong>17:00–20:30</strong></p>

    <table class="table table-bordered table-striped mb-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th><th>Fecha</th><th>Hora</th><th>Disponible</th><th>Acciones</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($d = $disponibilidad->fetch_assoc()): ?>
            <tr>
                <td><?= $d['id_disponibilidad'] ?></td>
                <td><?= $d['fecha'] ?></td>
                <td><?= substr($d['hora'], 0, 5) ?></td>
                <td>
                    <span class="badge bg-<?= $d['disponible'] ? 'success' : 'secondary' ?>">
                        <?= $d['disponible'] ? 'Sí' : 'No' ?>
                    </span>
                </td>

                <td>
                    <a href="admin.php?eliminar_disp=<?= $d['id_disponibilidad'] ?>" 
                       class="btn btn-danger btn-sm"
                       data-confirm="¿Eliminar esta disponibilidad?">
                       Eliminar
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Añadir disponibilidad -->
    <h4>Añadir nueva disponibilidad</h4>

    <form action="" method="POST" class="row g-3 mb-5 mt-2">

        <div class="col-md-4">
            <label class="form-label">Fecha</label>
            <input type="date" name="nueva_fecha" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Hora</label>
            <input type="time" name="nueva_hora" class="form-control"
                   required min="10:00" max="20:30" step="1800">
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Agregar disponibilidad</button>
        </div>

    </form>

</div>

<script src="public/js/app.js"></script>
</body>
</html>
