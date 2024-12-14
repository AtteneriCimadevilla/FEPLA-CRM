<?php
require_once "conexion.php";

$sql = "SELECT dni_nie, contrasenya FROM profesores";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dni_nie = $row["dni_nie"];
        $password = $row["contrasenya"];

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update the password in the database
        $update_sql = "UPDATE profesores SET contrasenya = ? WHERE dni_nie = ?";
        $stmt = $mysqli->prepare($update_sql);
        $stmt->bind_param("ss", $hashed_password, $dni_nie);
        $stmt->execute();
        $stmt->close();
    }
    echo "All passwords have been hashed.";
} else {
    echo "0 results";
}

$mysqli->close();
