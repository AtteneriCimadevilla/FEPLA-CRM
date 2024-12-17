<?php
// Include the connection file
include('conexion.php');  // This includes your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the POST request
    $dni_nie_alumno = $_POST['dni_nie_alumno'];  // The alumno's ID passed from the form
    $id_empresa = $_POST['empresa'];   // The selected empresa ID

    // Sanitize input data to avoid SQL injection
    $dni_nie_alumno = $mysqli->real_escape_string($dni_nie_alumno);
    $id_empresa = $mysqli->real_escape_string($id_empresa);

    // Insert into the formaciones table
    $query = "INSERT INTO formaciones (dni_nie_alumno, id_empresa) VALUES ('$dni_nie_alumno', '$id_empresa')";

    // Execute the query and check if successful
    if ($mysqli->query($query)) {
<<<<<<< HEAD
        echo "Formación asociada correctamente.";
    } else {
        echo "Error: " . $mysqli->error;
=======
        echo "Formación creada correctamente.";
    } else {
        echo "Error al asociar la formación: " . $mysqli->error;
>>>>>>> 0750e502d26e1ed964443256302d6361b089731a
    }
}
?>