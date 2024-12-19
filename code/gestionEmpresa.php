<?php
require 'conexion.php';
// Variables para manejar errores y mensajes de éxito
$errores = [];
$exito = "";

// Check if we're editing an existing company
$editing = false;
$empresa = null;
if (isset($_GET['id'])) {
    $editing = true;
    $id = $_GET['id'];
    $query = "SELECT * FROM empresas WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $empresa = $result->fetch_assoc();
    $stmt->close();
}

// Verificar si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
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
    if (empty($cif) || strlen($cif) != 9) {
        $errores[] = "El CIF debe tener 9 caracteres.";
    }
    if (empty($nombre_comercial)) {
        $errores[] = "El nombre comercial es obligatorio.";
    }
    if (!empty($email_contacto) && !filter_var($email_contacto, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email de contacto no es válido.";
    }

    // Si no hay errores, insertar o actualizar en la base de datos
    if (empty($errores)) {
        if ($editing) {
            $stmt = $mysqli->prepare("UPDATE empresas SET cif=?, nombre_comercial=?, nombre_empresa=?, telefono_empresa=?, nombre_contacto=?, telefono_contacto=?, email_contacto=?, direccion=?, interesado=?, cantidad_alumnos=?, notas=? WHERE id=?");
            $stmt->bind_param("ssssssssisii", $cif, $nombre_comercial, $nombre_empresa, $telefono_empresa, $nombre_contacto, $telefono_contacto, $email_contacto, $direccion, $interesado, $cantidad_alumnos, $notas, $id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO empresas (cif, nombre_comercial, nombre_empresa, telefono_empresa, nombre_contacto, telefono_contacto, email_contacto, direccion, interesado, cantidad_alumnos, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssiis", $cif, $nombre_comercial, $nombre_empresa, $telefono_empresa, $nombre_contacto, $telefono_contacto, $email_contacto, $direccion, $interesado, $cantidad_alumnos, $notas);
        }

        if ($stmt->execute()) {
            $exito = $editing ? "Empresa actualizada con éxito." : "Empresa creada con éxito.";
            // Set a session variable to indicate success
            session_start();
            $_SESSION['empresa_actualizada'] = true;
            // Redirect to the list of companies
            header("Location: empresas.php");
            exit();
        } else {
            $errores[] = "Error al " . ($editing ? "actualizar" : "insertar") . " en la base de datos: " . $stmt->error;
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
    <title><?php echo $editing ? 'Editar' : 'Crear'; ?> Empresa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <header>
        <a href="empresas.php" class="btn btn-outline-secondary btn-sm" style="position: absolute; top: 10px; left: 10px;">
        ← Volver </a>   
        </header>
    <div class="container mt-5">
        <h1 class="text-center"><?php echo $editing ? 'Editar' : 'Crear'; ?> Empresa</h1>

        <!-- Mostrar errores -->
        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($empresa['id']); ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label for="cif" class="form-label">CIF</label>
                <input type="text" class="form-control" id="cif" name="cif" maxlength="9" value="<?php echo $editing ? htmlspecialchars($empresa['cif']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="nombre_comercial" class="form-label">Nombre Comercial</label>
                <input type="text" class="form-control" id="nombre_comercial" name="nombre_comercial" required value="<?php echo $editing ? htmlspecialchars($empresa['nombre_comercial']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="nombre_empresa" class="form-label">Nombre Empresa</label>
                <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" value="<?php echo $editing ? htmlspecialchars($empresa['nombre_empresa']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="telefono_empresa" class="form-label">Teléfono Empresa</label>
                <input type="text" class="form-control" id="telefono_empresa" name="telefono_empresa" value="<?php echo $editing ? htmlspecialchars($empresa['telefono_empresa']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="nombre_contacto" class="form-label">Nombre Contacto</label>
                <input type="text" class="form-control" id="nombre_contacto" name="nombre_contacto" value="<?php echo $editing ? htmlspecialchars($empresa['nombre_contacto']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="telefono_contacto" class="form-label">Teléfono Contacto</label>
                <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto" value="<?php echo $editing ? htmlspecialchars($empresa['telefono_contacto']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="email_contacto" class="form-label">Email Contacto</label>
                <input type="email" class="form-control" id="email_contacto" name="email_contacto" value="<?php echo $editing ? htmlspecialchars($empresa['email_contacto']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $editing ? htmlspecialchars($empresa['direccion']) : ''; ?>">
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="interesado" name="interesado" <?php echo ($editing && $empresa['interesado']) ? 'checked' : ''; ?>>
                <label for="interesado" class="form-check-label">¿Está interesado?</label>
            </div>
            <div class="mb-3">
                <label for="cantidad_alumnos" class="form-label">Cantidad de Alumnos</label>
                <input type="number" class="form-control" id="cantidad_alumnos" name="cantidad_alumnos" min="0" value="<?php echo $editing ? htmlspecialchars($empresa['cantidad_alumnos']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="notas" class="form-label">Notas</label>
                <textarea class="form-control" id="notas" name="notas"><?php echo $editing ? htmlspecialchars($empresa['notas']) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $editing ? 'Actualizar' : 'Crear'; ?> Empresa</button>
            <a href="empresas.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</body>

</html>