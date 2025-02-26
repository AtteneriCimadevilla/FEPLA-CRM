<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CRM-FEPLA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="d-flex flex-column">
                    <div class="text-center mb-4">
                        <img src="logo.png" alt="logo" class="logo img-fluid mb-3">
                    </div>
                    <div class="nav flex-column">
                        <a href="home.php" class="nav-link active">
                            <i class="bi bi-house-door"></i> Inicio
                        </a>
                        <a href="#" class="nav-link">
                            <i class="bi bi-person"></i> Mi Perfil
                        </a>
                        <a href="logout.php" class="nav-link text-danger">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="mb-4">
                    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></h2>
                    <p class="text-muted">Panel de Control</p>
                </div>

                <div class="row g-4">
                    <?php if ($_SESSION["tipo_usuario"] === "admin") : ?>
                        <!-- Módulo Profesores (Solo para admin) -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card dashboard-card h-100" onclick="window.location.href='profesores.php'">
                                <div class="card-body text-center">
                                    <i class="bi bi-person-video3 card-icon text-primary"></i>
                                    <h5 class="card-title">Profesores</h5>
                                    <p class="card-text">Gestionar profesores</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Módulo Empresas -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card dashboard-card h-100" onclick="window.location.href='empresas.php'">
                            <div class="card-body text-center">
                                <i class="bi bi-building card-icon text-success"></i>
                                <h5 class="card-title">Empresas</h5>
                                <p class="card-text">Gestionar empresas</p>
                            </div>
                        </div>
                    </div>

                    <!-- Módulo Alumnos -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card dashboard-card h-100" onclick="window.location.href='alumnos.php'">
                            <div class="card-body text-center">
                                <i class="bi bi-mortarboard card-icon text-info"></i>
                                <h5 class="card-title">Alumnos</h5>
                                <p class="card-text">Gestionar alumnos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Módulo Formaciones -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card dashboard-card h-100" onclick="window.location.href='formaciones.php'">
                            <div class="card-body text-center">
                                <i class="bi bi-journal-text card-icon text-danger"></i>
                                <h5 class="card-title">Formaciones</h5>
                                <p class="card-text">Gestionar formaciones</p>
                            </div>
                        </div>
                    </div>

                    <!-- Módulo Actividad -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card dashboard-card h-100" onclick="window.location.href='actividades.php'">
                            <div class="card-body text-center">
                                <i class="bi bi-activity card-icon text-warning"></i>
                                <h5 class="card-title">Actividades</h5>
                                <p class="card-text">Ver actividades</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Listados -->
                <div class="col-12 mt-5 text-center">
                    <h3>Informes</h3>
                    <div class="d-flex justify-content-center mt-3 gap-3">
                        <button class="btn btn-primary listados-btn" onclick="window.location.href='formaciones.php'">
                            Listado de Formaciones
                        </button>
                        <button class="btn btn-secondary listados-btn" onclick="window.location.href='actividades.php'">
                            Listado de Actividades
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>