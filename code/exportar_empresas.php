<?php
require 'conexion.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

// Establecer cabeceras para descarga de archivo con codificación correcta
header('Content-Type: text/csv; charset=ISO-8859-1');
header('Content-Disposition: attachment; filename=empresas.csv');

// Agregar BOM para UTF-8
echo "\xEF\xBB\xBF";

// Abrir un archivo temporal en memoria para generar el CSV
$output = fopen('php://output', 'w');

// Definir la cabecera del archivo CSV
$headers = [
    'NIF',
    'Nombre Comercial',
    'Nombre Empresa',
    'Teléfono Empresa',
    'Nombre Contacto',
    'Teléfono Contacto',
    'Email Contacto',
    'Dirección',
    'CP',
    'Web',
    'Email Empresa',
    'Interesado',
    'Cantidad Alumnos',
    'Descripción',
    'Actividad Principal',
    'Otras Actividades',
    'DNI Profesor'
];


// Convertir caracteres especiales de UTF-8 a ISO-8859-1
$headers = array_map(fn($field) => iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $field), $headers);

// Escribir la cabecera convertida
fputcsv($output, $headers, ';');

// Realizar la consulta para obtener los datos de las empresas
$query = "SELECT nif, nombre_comercial, nombre_empresa, telefono_empresa, 
                 nombre_contacto, telefono_contacto, email_contacto, direccion, 
                 cp, web, email_empresa, interesado, cantidad_alumnos, 
                 descripcion, actividad_principal, otras_actividades, dni_profesor 
          FROM empresas";

$resultado = $mysqli->query($query);

// Escribir cada fila de la consulta en el archivo CSV
while ($fila = $resultado->fetch_assoc()) {

    // Convertir 'Interesado' a 'Sí' o 'No'
    $fila['interesado'] = $fila['interesado'] ? 'Sí' : 'No';
    
    // Asegurarse de que todos los valores sean UTF-8
    array_walk($fila, function(&$valor) {
        $valor = mb_convert_encoding($valor, 'UTF-8', 'UTF-8');
    });
    

    fputcsv($output, $fila, ';');
}

// Cerrar la conexión a la base de datos
$mysqli->close();
exit;
?>