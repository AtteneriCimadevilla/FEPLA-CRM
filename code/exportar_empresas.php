<?php
require 'conexion.php';

// Establecer cabeceras para descarga de archivo
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=empresas.csv');

// Agregar BOM para UTF-8
echo "\xEF\xBB\xBF";

// Abrir un archivo temporal en memoria para generar el CSV
$output = fopen('php://output', 'w');

// Escribir la cabecera del archivo CSV
fputcsv($output, [
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
], ';');

// Realizar la consulta para obtener los datos de las empresas
$query = "SELECT * FROM empresas";
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