<?php
session_start();
require_once "conexion.php";

header('Content-Type: application/json');

// Verificar método HTTP
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit;
}

// Verificar autenticación
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(["status" => "error", "message" => "Usuario no autenticado"]);
    exit;
}

// Obtener y sanitizar datos
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
$tipo_actividad = isset($_POST['tipo_actividad']) ? $_POST['tipo_actividad'] : null;
$actividad_para = isset($_POST['actividad_para']) ? $_POST['actividad_para'] : null;
$empresa_id = isset($_POST['empresa']) && !empty($_POST['empresa']) ? $_POST['empresa'] : null;
$alumno_dni = isset($_POST['alumno']) && !empty($_POST['alumno']) ? $_POST['alumno'] : null;
$texto_registro = isset($_POST['texto_registro']) ? $_POST['texto_registro'] : null;
$profesor_dni = isset($_SESSION['dni_nie']) ? $_SESSION['dni_nie'] : null;

// Validaciones básicas
if (empty($fecha) || empty($tipo_actividad) || empty($texto_registro) || empty($actividad_para)) {
    echo json_encode([
        "status" => "error", 
        "message" => "Los campos fecha, tipo de actividad y texto son requeridos",
        "data" => [
            "fecha" => $fecha,
            "tipo_actividad" => $tipo_actividad,
            "texto" => $texto_registro,
            "actividad_para" => $actividad_para
        ]
    ]);
    exit;
}

if (empty($profesor_dni)) {
    echo json_encode(["status" => "error", "message" => "Error: DNI del profesor no encontrado en la sesión"]);
    exit;
}

// Validaciones específicas según el tipo de actividad
switch ($actividad_para) {
    case 'empresa':
        if (empty($empresa_id)) {
            echo json_encode(["status" => "error", "message" => "Debe seleccionar una empresa"]);
            exit;
        }
        $alumno_dni = null; // Aseguramos que no se guarde ningún alumno
        break;
        
    case 'alumno':
        if (empty($alumno_dni)) {
            echo json_encode(["status" => "error", "message" => "Debe seleccionar un alumno"]);
            exit;
        }
        $empresa_id = null; // Aseguramos que no se guarde ninguna empresa
        break;
        
    case 'ambos':
        if (empty($empresa_id) || empty($alumno_dni)) {
            echo json_encode(["status" => "error", "message" => "Debe seleccionar tanto empresa como alumno"]);
            exit;
        }
        break;
        
    default:
        echo json_encode(["status" => "error", "message" => "Tipo de actividad no válido"]);
        exit;
}

// Preparar la consulta
$sql_insert = "INSERT INTO registro (dni_nie_profesor, fecha, tipo_actividad, id_empresa, dni_nie_alumno, texto_registro) 
               VALUES (?, ?, ?, ?, ?, ?)";

try {
    $stmt = $mysqli->prepare($sql_insert);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $mysqli->error);
    }
    
    // Bind de parámetros
    $stmt->bind_param("ssssss", 
        $profesor_dni,
        $fecha,
        $tipo_actividad,
        $empresa_id,
        $alumno_dni,
        $texto_registro
    );
    
    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    echo json_encode([
        "status" => "success",
        "message" => "Actividad registrada con éxito",
        "id" => $mysqli->insert_id
    ]);
    
} catch (Exception $e) {
    error_log("Error en procesar_actividad.php: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => "Error al procesar la actividad: " . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $mysqli->close();
}
?>

