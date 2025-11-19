<?php
session_start();
include('includes/conexion.php');

// PROTEGER PANEL: SOLO ADMIN
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit;
}

// --------------------------
// ACCIONES DEL ADMIN
// --------------------------

// Confirmar cita (cambiar estado a confirmada)
if (isset($_GET['confirmar'])) {
    $id = intval($_GET['confirmar']);
    $conn->query("UPDATE citas SET estado='confirmada' WHERE id_cita=$id");
}

// Cancelar/Eliminar cita y liberar disponibilidad
if (isset($_GET['cancelar'])) {
    $id = intval($_GET['cancelar']);

    // Recuperar id_disponibilidad
    $sql = $conn->query("SELECT id_disponibilidad FROM citas WHERE id_cita=$id");
    if ($sql && $sql->num_rows > 0) {
        $fila = $sql->fetch_assoc();
        $id_disp = $fila['id_disponibilidad'];

        // Borrar la cita
        $conn->query("DELETE FROM citas WHERE id_cita=$id");

        // Marcar disponibilidad como libre
        $conn->query("UPDATE disponibilidad SET disponible=1 WHERE id_disponibilidad=$id_disp");
    }
}

// Añadir nueva disponibilidad
if (isset($_POST['nueva_fecha']) && isset($_POST['nueva_hora'])) {
    $fecha = $_POST['nueva_fecha'];
    $hora  = $_POST['nueva_hora'];
    $conn->query("INSERT INTO disponibilidad (fecha, hora, disponible) VALUES ('$fecha', '$hora', 1)");
}

// Eliminar disponibilidad
if (isset($_GET['eliminar_disp'])) {
    $id = intval($_GET['eliminar_disp']);
    $conn->query("DELETE FROM disponibilidad WHERE id_disponibilidad=$id");
}

// --------------------------
// CONSULTAS PRINCIPALES
// --------------------------

// Citas con JOIN a usuarios y disponibilidad
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

// Usuarios
$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY id_usuario");

// Disponibilidad
$disponibilidad = $conn->query("SELECT * FROM disponibilidad ORDER BY fecha, hora");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - La Garduña</title>
</head>
<body>

<h1>Panel de Administración – La Garduña</h1>

<p>
    Bienvenido, administrador: 
    <strong>
        <?php 
        // En tu login usas $_SESSION["usuario"]
        echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Admin'; 
        ?>
    </strong>
</p>

<p><a href="logout.php">Cerrar sesión</a></p>

<hr>

<!-- =====================
     GESTIÓN DE CITAS
====================== -->
<h2>Citas registradas</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Email</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Servicio</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>

    <?php if ($citas && $citas->num_rows > 0): ?>
        <?php while ($c = $citas->fetch_assoc()): ?>
            <tr>
                <td><?php echo $c['id_cita']; ?></td>
                <td><?php echo $c['cliente']; ?></td>
                <td><?php echo $c['email']; ?></td>
                <td><?php echo $c['fecha']; ?></td>
                <td><?php echo $c['hora']; ?></td>
                <td><?php echo $c['servicio']; ?></td>
                <td><?php echo $c['estado']; ?></td>
                <td>
                    <a href="admin.php?confirmar=<?php echo $c['id_cita']; ?>">Confirmar</a> |
                    <a href="admin.php?cancelar=<?php echo $c['id_cita']; ?>">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8">No hay citas registradas.</td></tr>
    <?php endif; ?>
</table>

<hr>

<!-- =====================
     GESTIÓN DE USUARIOS
====================== -->
<h2>Usuarios registrados</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Rol</th>
    </tr>

    <?php if ($usuarios && $usuarios->num_rows > 0): ?>
        <?php while ($u = $usuarios->fetch_assoc()): ?>
            <tr>
                <td><?php echo $u['id_usuario']; ?></td>
                <td><?php echo $u['nombre']; ?></td>
                <td><?php echo $u['email']; ?></td>
                <td><?php echo $u['rol']; ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4">No hay usuarios registrados.</td></tr>
    <?php endif; ?>
</table>

<hr>

<!-- =====================
     GESTIÓN DE DISPONIBILIDAD
====================== -->
<h2>Disponibilidad (horas libres)</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Disponible</th>
        <th>Acciones</th>
    </tr>

    <?php if ($disponibilidad && $disponibilidad->num_rows > 0): ?>
        <?php while ($d = $disponibilidad->fetch_assoc()): ?>
            <tr>
                <td><?php echo $d['id_disponibilidad']; ?></td>
                <td><?php echo $d['fecha']; ?></td>
                <td><?php echo $d['hora']; ?></td>
                <td><?php echo $d['disponible']; ?></td>
                <td>
                    <a href="admin.php?eliminar_disp=<?php echo $d['id_disponibilidad']; ?>">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No hay horarios creados.</td></tr>
    <?php endif; ?>
</table>

<h3>Añadir nueva disponibilidad</h3>

<form action="admin.php" method="POST">
    <label>Fecha: </label>
    <input type="date" name="nueva_fecha" required>

    <label>Hora: </label>
    <input type="time" name="nueva_hora" required>

    <button type="submit">Agregar</button>
</form>

</body>
</html>


