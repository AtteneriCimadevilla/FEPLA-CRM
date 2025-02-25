<?php
// Include the connection file
include('conexion.php');  // This includes your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the POST request
    $dni_nie_alumno = $_POST['dni_nie_alumno'];  // The alumno's ID passed from the form
    $id_empresa = $_POST['empresa'];   // The selected empresa ID
    $curso = $_POST['curso']; // Add this line to get the curso value

    // Sanitize input data to avoid SQL injection
    $dni_nie_alumno = $mysqli->real_escape_string($dni_nie_alumno);
    $id_empresa = $mysqli->real_escape_string($id_empresa);
    $curso = $mysqli->real_escape_string($curso);

    // Insert into the formaciones table
    $query = "INSERT INTO formaciones (dni_nie_alumno, id_empresa, curso) VALUES (?, ?, ?)";
    
    // Prepare the statement
    $stmt = $mysqli->prepare($query);
    
    // Bind the parameters
    $stmt->bind_param("sis", $dni_nie_alumno, $id_empresa, $curso);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        echo "Formación creada correctamente.";
    } else {
        echo "Error al asociar la formación: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$mysqli->close();
?>