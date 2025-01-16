<?php
require 'conexion.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0) {
        $file = $_FILES["csvFile"]["tmp_name"];

        if (($handle = fopen($file, "r")) !== FALSE) {
            // Skip the header row
            fgetcsv($handle, 1000, ",");

            $importCount = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $cif = $mysqli->real_escape_string($data[0]);
                $nombre_comercial = $mysqli->real_escape_string($data[1]);
                $nombre_empresa = $mysqli->real_escape_string($data[2]);
                $telefono_empresa = $mysqli->real_escape_string($data[3]);
                $nombre_contacto = $mysqli->real_escape_string($data[4]);
                $telefono_contacto = $mysqli->real_escape_string($data[5]);
                $email_contacto = $mysqli->real_escape_string($data[6]);
                $direccion = $mysqli->real_escape_string($data[7]);
                $interesado = $data[8] == 'Sí' ? 1 : 0;
                $cantidad_alumnos = intval($data[9]);
                $notas = $mysqli->real_escape_string($data[10]);

                $query = "INSERT INTO empresas (cif, nombre_comercial, nombre_empresa, telefono_empresa, nombre_contacto, telefono_contacto, email_contacto, direccion, interesado, cantidad_alumnos, notas) 
                          VALUES ('$cif', '$nombre_comercial', '$nombre_empresa', '$telefono_empresa', '$nombre_contacto', '$telefono_contacto', '$email_contacto', '$direccion', $interesado, $cantidad_alumnos, '$notas')
                          ON DUPLICATE KEY UPDATE 
                          nombre_comercial = VALUES(nombre_comercial),
                          nombre_empresa = VALUES(nombre_empresa),
                          telefono_empresa = VALUES(telefono_empresa),
                          nombre_contacto = VALUES(nombre_contacto),
                          telefono_contacto = VALUES(telefono_contacto),
                          email_contacto = VALUES(email_contacto),
                          direccion = VALUES(direccion),
                          interesado = VALUES(interesado),
                          cantidad_alumnos = VALUES(cantidad_alumnos),
                          notas = VALUES(notas)";

                if ($mysqli->query($query)) {
                    $importCount++;
                }
            }
            fclose($handle);

            $_SESSION['import_success'] = true;
            $_SESSION['import_count'] = $importCount;
        } else {
            $_SESSION['import_error'] = "Error al abrir el archivo CSV.";
        }
    } else {
        $_SESSION['import_error'] = "Error al subir el archivo. Por favor, inténtelo de nuevo.";
    }
} else {
    $_SESSION['import_error'] = "Método de solicitud no válido.";
}

$mysqli->close();
header("Location: empresas.php");
exit();
