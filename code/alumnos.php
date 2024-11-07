<?php
    require 'conexion.php';

    // <!-- // Consulta SQL para obtener los datos de la tabla "alumnos" -->
    $sql = "SELECT dni_nie, nombre, apellido1 FROM alumnos";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEPLA CRM Alumnos</title>
    <link rel="stylesheet" href="style-tablas.css">
</head>

<body>
    <header>
        <img src="logo.png" alt="logo">
        <div class="busqueda">filtros de búsqueda</div>
    </header>
     <!-- Generar HTML para mostrar los datos en una tabla -->
    <?php
     if ($result->num_rows > 0) {
    echo "<table border='1'>
        <tr>
            <th>DNI</th>
            <th>Nombre</th>
            <th>1er apellido</th>
        </tr>";
    
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        echo "<tr>
            <td>" . $row["dni_nie"] . "</td>
            <td>" . $row["nombre"] . "</td>
            <td>" . $row["apellido1"] . "</td>
        </tr>";
        }
        echo "
    </table>";
    } else {
    echo "No se encontraron resultados.";
    }
    
    // Cerrar la conexión -->
    $conn->close();
?>

    <!-- <table>
        <tr>
            <th>DNI</th>
            <th>Nombre</th>
            <th>1er Apellido</th>
            <th>2o apellido</th>
            <th>Edad</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Dirección</th>
            <th>Vehículo</th>
            <th>Empresa de prácticas</th>
        </tr>
        <tr>
            <td>prueba</td>
            <td>prueba</td>
            <td>prueba</td>
            <td>prueba</td>
            <td>prueba</td>
            <td>prueba</td>
            <td>prueba</td>
            <td>prueba</td>
            <td>prueba</td>
            <td>prueba</td>
        </tr>
    </table> -->
</body>

</html>