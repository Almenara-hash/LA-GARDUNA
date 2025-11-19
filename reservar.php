<?php
session_start();
include('includes/conexion.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

// Obtener id del usuario actual
// Lo ideal sería obtenerlo de la base de datos a partir del email o nombre
// (simplificado en este ejemplo)
$emailUsuario = $_SESSION["usuario"];
$consultaUsuario = $conn->prepare("SELECT id_usuario FROM usuarios WHERE nombre = ?");
$consultaUsuario->bind_param("s", $emailUsuario);
$consultaUsuario->execute();
$resultado = $consultaUsuario->get_result();
if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    $id_usuario = $usuario["id_usuario"];
}

// Si el usuario envía el formulario de reserva
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_disponibilidad = $_POST["id_disponibilidad"];
    $servicio = $_POST["servicio"];

    // Insertar la cita
    $stmt = $conn->prepare("INSERT INTO citas (id_usuario, id_disponibilidad, servicio, estado)
                            VALUES (?, ?, ?, 'pendiente')");
    $stmt->bind_param("iis", $id_usuario, $id_disponibilidad, $servicio);
    $stmt->execute();

    // Marcar la disponibilidad como no disponible
    $update = $conn->prepare("UPDATE disponibilidad SET disponible = 0 WHERE id_disponibilidad = ?");
    $update->bind_param("i", $id_disponibilidad);
    $update->execute();

    $mensaje = "Cita reservada con éxito.";
}

// Obtener las disponibilidades libres
$result = $conn->query("SELECT * FROM disponibilidad WHERE disponible = 1 ORDER BY fecha, hora");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reservar cita | La Garduña</title>
<link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<h1>Reservar una cita </h1>

<p><a href="index.php"> Volver al inicio</a></p>

<?php if (!empty($mensaje)): ?>
<p style="color:green;"><?= $mensaje ?></p>
<?php endif; ?>

<?php if ($result->num_rows > 0): ?>
<form method="POST" action="">
    <label for="servicio">Selecciona el servicio:</label><br>
    <input type="text" name="servicio" placeholder="Ej: Corte de pelo" required><br><br>

    <label for="id_disponibilidad">Selecciona fecha y hora:</label><br>
    <select name="id_disponibilidad" required>
        <?php while($row = $result->fetch_assoc()): ?>
            <option value="<?= $row['id_disponibilidad'] ?>">
                <?= $row['fecha'] ?> - <?= substr($row['hora'], 0, 5) ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <input type="submit" value="Reservar cita">
</form>
<?php else: ?>
<p style="color:red;">❌ No hay horarios disponibles actualmente.</p>
<?php endif; ?>

</body>
</html>
