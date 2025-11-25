<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>La Garduña | Inicio</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="bg-light">

<!-- ============================
       NAVBAR MINIMALISTA
============================ -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container text-center">

        <a class="navbar-brand fw-bold mx-auto d-flex align-items-center gap-2" href="index.php">

            <img src="public/images/logo.png" 
                 alt="Logo La Garduña"
                 class="logo-navbar">

            <span>La Garduña</span>

        </a>

    </div>
</nav>




<!-- ============================
     CONTENIDO PRINCIPAL
============================ -->
<div class="container py-5">

    <div class="row align-items-center">
        
        <!-- IMAGEN PRINCIPAL -->
        <div class="col-md-6 mb-4 mb-md-0 text-center">
            <img src="public/images/puerta.jpg"
                alt="Peluquería La Garduña"
                class="img-fluid img-principal mb-4">

        </div>

        <!-- TEXTO DEL INDEX -->
        <div class="col-md-6">
            <h1 class="fw-bold mb-3">La Garduña</h1>

            <p class="lead">
                La Garduña es una peluquería de barrio gestionada por un único profesional.
                Esta aplicación web permite digitalizar la gestión de citas, evitar confusiones,
                ahorrar tiempo y ofrecer un mejor servicio a los clientes.
            </p>

            <!-- HORARIO -->
            <div class="alert alert-secondary mt-3 shadow-sm horario-fijo">
                <strong>Horario de atención:</strong><br>
                Lunes a Viernes<br>
                <span class="fw-semibold">10:00 – 14:00</span> &nbsp; | &nbsp;
                <span class="fw-semibold">17:00 – 20:30</span>
            </div>

            <!-- ============================
                 SERVICIOS CON IMAGEN
            ============================= -->
            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 fw-bold mb-3">Servicios y precios</h2>

                    <div class="row g-3">

                        <!-- Corte de pelo -->
                        <div class="col-12 d-flex align-items-center">
                            <img src="public/images/corte.png" 
                                alt="Corte de pelo"
                                class="me-3 rounded"
                                style="width:70px; height:70px; object-fit:cover;">
                            <div class="flex-grow-1">
                                <span class="fw-semibold">Corte de pelo</span><br>
                                <span class="text-muted">12 €</span>
                            </div>
                        </div>

                        <!-- Arreglo de barba -->
                        <div class="col-12 d-flex align-items-center">
                            <img src="public/images/barba.png" 
                                alt="Arreglo de barba"
                                class="me-3 rounded"
                                style="width:70px; height:70px; object-fit:cover;">
                            <div class="flex-grow-1">
                                <span class="fw-semibold">Arreglo de barba</span><br>
                                <span class="text-muted">4 €</span>
                            </div>
                        </div>

                        <!-- Lavado de cabeza -->
                        <div class="col-12 d-flex align-items-center">
                            <img src="public/images/lavado.png" 
                                alt="Lavado de cabeza"
                                class="me-3 rounded"
                                style="width:70px; height:70px; object-fit:cover;">
                            <div class="flex-grow-1">
                                <span class="fw-semibold">Lavado de cabeza</span><br>
                                <span class="text-muted">4 €</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ============================
                 BOTONES SEGÚN SESIÓN
            ============================= -->
            <?php if (isset($_SESSION['usuario_id'])): ?>

                <p class="mb-3 mt-3">
                    Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong>.
                </p>

                <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                    <a href="admin.php" class="btn btn-primary me-2 mb-2">Ir al panel de administración</a>
                <?php else: ?>
                    <a href="reservar.php" class="btn btn-primary me-2 mb-2">Reservar una cita</a>
                    <a href="mis_citas.php" class="btn btn-outline-dark me-2 mb-2">Ver mis citas</a>
                <?php endif; ?>

                <a href="logout.php" class="btn btn-outline-secondary mb-2">Cerrar sesión</a>

            <?php else: ?>

                <p class="mb-3 mt-3">
                    Para reservar una cita, crea una cuenta o inicia sesión.
                </p>

                <a href="register.php" class="btn btn-success me-2 mb-2">Crear cuenta</a>
                <a href="login.php" class="btn btn-outline-primary mb-2">Iniciar sesión</a>

            <?php endif; ?>

        </div>
    </div>

</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="public/js/app.js"></script>

</body>
</html>
