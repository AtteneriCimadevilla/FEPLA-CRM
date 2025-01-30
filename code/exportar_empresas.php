<?php
require 'conexion.php';

// Establecer cabeceras para descarga de archivo con codificación correcta
header('Content-Type: text/csv; charset=ISO-8859-1');
header('Content-Disposition: attachment; filename=empresas.csv');

// Abrir un archivo temporal en memoria para generar el CSV
$output = fopen('php://output', 'w');

// Definir la cabecera del CSV
$headers = [
    'CIF',
    'Nombre Comercial',
    'Nombre Empresa',
    'Teléfono Empresa',
    'Nombre Contacto',
    'Teléfono Contacto',
    'Email Contacto',
    'Dirección',
    'Interesado',
    'Cantidad Alumnos',
    'Notas'
];

// Convertir caracteres especiales de UTF-8 a ISO-8859-1
$headers = array_map(fn($field) => iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $field), $headers);

// Escribir la cabecera convertida
fputcsv($output, $headers, ';');

// Realizar la consulta para obtener los datos de las empresas
$query = "SELECT cif, nombre_comercial, nombre_empresa, telefono_empresa, 
                 nombre_contacto, telefono_contacto, email_contacto, direccion, 
                 interesado, cantidad_alumnos, notas 
          FROM empresas";
$resultado = $mysqli->query($query);

// Escribir cada fila de la consulta en el archivo CSV
while ($fila = $resultado->fetch_assoc()) {
    fputcsv($output, $fila, ';');
}

// Cerrar la conexión a la base de datos
$mysqli->close();
exit;
