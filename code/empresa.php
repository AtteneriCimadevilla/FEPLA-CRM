<?php
include 'conexion.php'; // Archivo de conexión a la base de datos

// Verificar si se recibió el ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Asegurarse de que sea un número entero

    // Consulta preparada para obtener la empresa
    $sql = "SELECT * FROM empresas WHERE id = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $empresa = $resultado->fetch_assoc();
        } else {
            echo "No se encontró la empresa.";
            exit();
        }
        $stmt->close();
    } else {
        echo "Error en la consulta.";
        exit();
    }
} else {
    echo "ID de empresa no proporcionado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Empresa</title>
    <link rel="stylesheet" href="styleEmpresa.css">
</head>
<body>
    <div class="contenedor">
        <header>
            <h1>Detalles de Empresa</h1>
        </header>

        <!-- Información de la Empresa -->
        <section class="perfil">
            <h2>Información General</h2>
            <p><strong>CIF:</strong> <?php echo htmlspecialchars($empresa['cif']); ?></p>
            <p><strong>Nombre Comercial:</strong> <?php echo htmlspecialchars($empresa['nombre_comercial']); ?></p>
            <p><strong>Nombre de la Empresa:</strong> <?php echo htmlspecialchars($empresa['nombre_empresa']); ?></p>
            <p><strong>Teléfono de Empresa:</strong> <?php echo htmlspecialchars($empresa['telefono_empresa']); ?></p>
            <p><strong>Nombre de Contacto:</strong> <?php echo htmlspecialchars($empresa['nombre_contacto']); ?></p>
            <p><strong>Teléfono de Contacto:</strong> <?php echo htmlspecialchars($empresa['telefono_contacto']); ?></p>
            <p><strong>Email de Contacto:</strong> <?php echo htmlspecialchars($empresa['email_contacto']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($empresa['direccion']); ?></p>
            <p><strong>Interesado:</strong> <?php echo $empresa['interesado'] ? 'Sí' : 'No'; ?></p>
            <p><strong>Cantidad de Alumnos:</strong> <?php echo htmlspecialchars($empresa['cantidad_alumnos']); ?></p>
            <!-- Campo resaltado -->
            <div class="notas">
                <strong>Notas:</strong>
                <p><?php echo nl2br(htmlspecialchars($empresa['notas'])); ?></p>
            </div>
        </section>

        <!-- Sección de Registro (Actividades) -->
        <section class="registro">
            <h2>Registro de Actividades</h2>
            <!-- Botón para abrir un formulario modal -->
            <a href="actividad.php?empresa_id=<?php echo $empresa['id']; ?>">
                <button>Añadir Actividad</button>
            </a>

            <!-- Aquí puedes agregar dinámicamente las actividades -->
            <p>No hay actividades registradas actualmente.</p>
        </section>


        <!-- Botón Crear Formación -->
        <div class="acciones">
            <a href="crear_formacion.php?empresa_id=<?php echo $empresa['id']; ?>">
                <button>Crear Formación</button>
            </a>
        </div>
    </div>

</body>
</html>
