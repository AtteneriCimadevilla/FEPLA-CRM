<?php
require 'conexion.php'; // Conexión a la base de datos

// Inicializar variables
$errores = [];
$exito = "";

// Procesar formulario al enviar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cif = trim($_POST['cif']);
    $nombre_comercial = trim($_POST['nombre_comercial']);
    $nombre_empresa = trim($_POST['nombre_empresa']);
    $telefono_empresa = trim($_POST['telefono_empresa']);
    $nombre_contacto = trim($_POST['nombre_contacto']);
    $telefono_contacto = trim($_POST['telefono_contacto']);
    $email_contacto = trim($_POST['email_contacto']);
    $direccion = trim($_POST['direccion']);
    $interesado = isset($_POST['interesado']) ? 1 : 0;
    $cantidad_alumnos = trim($_POST['cantidad_alumnos']);
    $notas = trim($_POST['notas']);

    // Validaciones básicas 
    if (empty($nombre_comercial)) {
        $errores[] = "El nombre comercial es obligatorio.";
    }
    if (!empty($email_contacto) && !filter_var($email_contacto, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email de contacto no es válido.";
    }

    // Si no hay errores, insertar en la base de datos
    if (empty($errores)) {
        $stmt = $mysqli->prepare("INSERT INTO empresas (cif, nombre_comercial, nombre_empresa, telefono_empresa, nombre_contacto, telefono_contacto, email_contacto, direccion, interesado, cantidad_alumnos, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssiss", $cif, $nombre_comercial, $nombre_empresa, $telefono_empresa, $nombre_contacto, $telefono_contacto, $email_contacto, $direccion, $interesado, $cantidad_alumnos, $notas);

        if ($stmt->execute()) {
            $exito = "Empresa creada con éxito.";
        } else {
            $errores[] = "Error al insertar en la base de datos: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Empresa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Crear Empresa</h1>

        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($exito)): ?>
            <div class="alert alert-success">
                <p><?php echo htmlspecialchars($exito); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="crear_empresa.php">
            <div class="mb-3">
                <label for="cif" class="form-label">CIF</label>
                <input type="text" class="form-control" id="cif" name="cif" maxlength="9" required>
            </div>
            <div class="mb-3">
                <label for="nombre_comercial" class="form-label">Nombre Comercial</label>
                <input type="text" class="form-control" id="nombre_comercial" name="nombre_comercial" required>
            </div>
            <div class="mb-3">
                <label for="nombre_empresa" class="form-label">Nombre Empresa</label>
                <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa">
            </div>
            <div class="mb-3">
                <label for="telefono_empresa" class="form-label">Teléfono Empresa</label>
                <input type="text" class="form-control" id="telefono_empresa" name="telefono_empresa">
            </div>
            <div class="mb-3">
                <label for="nombre_contacto" class="form-label">Nombre Contacto</label>
                <input type="text" class="form-control" id="nombre_contacto" name="nombre_contacto">
            </div>
            <div class="mb-3">
                <label for="telefono_contacto" class="form-label">Teléfono Contacto</label>
                <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto">
            </div>
            <div class="mb-3">
                <label for="email_contacto" class="form-label">Email Contacto</label>
                <input type="email" class="form-control" id="email_contacto" name="email_contacto">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion">
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="interesado" name="interesado">
                <label for="interesado" class="form-check-label">¿Está interesado?</label>
            </div>
            <div class="mb-3">
                <label for="cantidad_alumnos" class="form-label">Cantidad de Alumnos</label>
                <input type="number" class="form-control" id="cantidad_alumnos" name="cantidad_alumnos" min="0">
            </div>
            <div class="mb-3">
                <label for="notas" class="form-label">Notas</label>
                <textarea class="form-control" id="notas" name="notas"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Crear Empresa</button>
        </form>
    </div>
</body>

</html>
