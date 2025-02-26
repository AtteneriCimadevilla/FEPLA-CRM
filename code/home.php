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
    <style>
        .dashboard-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #333;
            padding-top: 2rem;
        }

        .main-content {
            padding: 2rem;
        }

        .nav-link {
            color: white;
            padding: 0.5rem 1rem;
        }

        .nav-link:hover {
            background-color: #444;
        }

        .gap-3 {
            gap: 20px;
            /* Espaciado uniforme entre botones */
        }

        .listados-btn {
            width: 250px;
            /* Ancho fijo para todos los botones */
            height: 60px;
            /* Altura fija */
            font-size: 1.2rem;
            /* Tamaño del texto uniforme */
            display: inline-block;
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="d-flex flex-column">
                    <div class="text-center mb-4">
                        <img src="logo.png" alt="logo" class="logo img-fluid mb-3" style="max-width: 120px;">
                        <h5 class="text-white">CRM-FEPLA</h5>
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

                    <!-- Módulo Actividad -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card dashboard-card h-100" onclick="window.location.href='actividad.php'">
                            <div class="card-body text-center">
                                <i class="bi bi-activity card-icon text-warning"></i>
                                <h5 class="card-title">Actividad</h5>
                                <p class="card-text">Ver actividad reciente</p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Módulo de Formaciones -->
                <div class="col-md-6 col-lg-3">
                    <div class="card dashboard-card h-100" onclick="window.location.href='formaciones.php'">
                        <div class="card-body text-center">
                            <i class="bi bi-journal-text card-icon text-danger"></i>
                            <h5 class="card-title">Formaciones</h5>
                            <p class="card-text">Gestionar formaciones en empresas</p>
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