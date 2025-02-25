<?php
require 'conexion.php';

<<<<<<< HEAD
// Establecer cabeceras para descarga de archivo
header('Content-Type: text/csv; charset=UTF-8');
=======
// Establecer cabeceras para descarga de archivo con codificación correcta
header('Content-Type: text/csv; charset=ISO-8859-1');
>>>>>>> 88c2eaab726dc65c5641d2a6656f523cb05a7bbb
header('Content-Disposition: attachment; filename=empresas.csv');

// Agregar BOM para UTF-8
echo "\xEF\xBB\xBF";

// Abrir un archivo temporal en memoria para generar el CSV
$output = fopen('php://output', 'w');

<<<<<<< HEAD
// Escribir la cabecera del archivo CSV
fputcsv($output, [
    'NIF',
=======
// Definir la cabecera del CSV
$headers = [
    'CIF',
>>>>>>> 88c2eaab726dc65c5641d2a6656f523cb05a7bbb
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
<<<<<<< HEAD
    'Descripción',
    'Actividad Principal',
    'Otras Actividades',
    'DNI Profesor'
], ';');

// Realizar la consulta para obtener los datos de las empresas
$query = "SELECT * FROM empresas";
=======
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
>>>>>>> 88c2eaab726dc65c5641d2a6656f523cb05a7bbb
$resultado = $mysqli->query($query);

// Escribir cada fila de la consulta en el archivo CSV
while ($fila = $resultado->fetch_assoc()) {
<<<<<<< HEAD
    // Convertir 'Interesado' a 'Sí' o 'No'
    $fila['interesado'] = $fila['interesado'] ? 'Sí' : 'No';
    
    // Asegurarse de que todos los valores sean UTF-8
    array_walk($fila, function(&$valor) {
        $valor = mb_convert_encoding($valor, 'UTF-8', 'UTF-8');
    });
    
=======
>>>>>>> 88c2eaab726dc65c5641d2a6656f523cb05a7bbb
    fputcsv($output, $fila, ';');
}

// Cerrar la conexión a la base de datos
$mysqli->close();
exit;
?>