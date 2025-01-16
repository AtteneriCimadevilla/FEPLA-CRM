<?php
require 'conexion.php';

// Establecer cabeceras para descarga de archivo
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=empresas.csv');

// Abrir un archivo temporal en memoria para generar el CSV
$output = fopen('php://output', 'w');

// Escribir la cabecera del archivo CSV
fputcsv($output, [
    'ID',
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
]);

// Realizar la consulta para obtener los datos de las empresas
$query = "SELECT id, cif, nombre_comercial, nombre_empresa, telefono_empresa, 
                 nombre_contacto, telefono_contacto, email_contacto, direccion, 
                 interesado, cantidad_alumnos, notas 
          FROM empresas";
$resultado = $mysqli->query($query);

// Escribir cada fila de la consulta en el archivo CSV
while ($fila = $resultado->fetch_assoc()) {
    fputcsv($output, $fila);
}

// Cerrar la conexión a la base de datos
$mysqli->close();
exit;
