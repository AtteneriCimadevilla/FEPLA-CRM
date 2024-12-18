<?php
require 'conexion.php';

// Obtener los datos del formulario
$dni_nie_alumno = $_POST['dni_nie_alumno'];
$id_empresa = $_POST['id_empresa'];
$curso = $_POST['curso'];

// Insertar los datos en la base de datos
$query = "INSERT INTO formaciones (dni_nie_alumno, id_empresa, curso) 
          VALUES ('$dni_nie_alumno', '$id_empresa', '$curso')";

if ($mysqli->query($query) === TRUE) {
    echo "Formación creada exitosamente.";
} else {
    echo "Error al crear formación: " . $mysqli->error;
}

$mysqli->close();
