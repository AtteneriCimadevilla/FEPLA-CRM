<?php
session_start();
require_once "conexion.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Actividad - CRM-FEPLA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estiloActividad.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Módulo de Actividad</h1>
        <div class="form-container">
            <div id="message"></div>
            <form id="activityForm">

                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
                <div class="form-group">
                    <label for="empresa">Empresa:</label>
                    <select class="form-control" id="empresa" name="empresa" required>
                        <option value="">Seleccione una empresa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="alumno">Alumno:</label>
                    <select class="form-control" id="alumno" name="alumno" required>
                        <option value="">Seleccione un alumno</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nota">Nota:</label>
                    <textarea class="form-control" id="nota" name="nota" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Actividad</button>
            </form>
            <hr>
            <h3 class="mt-4">Filtrar</h3>
            <div class="form-group">
                <label for="filtro">Seleccione empresa o alumno:</label>
                <select class="form-control" id="filtro">
                    <option value="">Seleccione una opción</option>
                    <optgroup label="Empresas" id="empresas-filtro">
                    </optgroup>
                    <optgroup label="Alumnos" id="alumnos-filtro">
                    </optgroup>
                </select>
            </div>
            <button id="filtrarBtn" class="btn btn-secondary">Filtrar</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scriptActividad.js"></script>
</body>
</html>