<?php
require 'conexion.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] === 0) {
        $file = $_FILES["csvFile"]["tmp_name"];

        if (($handle = fopen($file, "r")) !== FALSE) {
            // Verificar y convertir el CSV a UTF-8
            $csvContent = file_get_contents($file);
            $csvContent = mb_convert_encoding($csvContent, 'UTF-8', mb_detect_encoding($csvContent, 'UTF-8, ISO-8859-1, WINDOWS-1252', true));
            file_put_contents($file, $csvContent);

            fgetcsv($handle, 1000, ";"); // Saltar la cabecera

            $importCount = 0;

            // Preparar la consulta con sentencias preparadas para evitar SQL Injection
            $query = "INSERT INTO empresas (cif, nombre_comercial, nombre_empresa, telefono_empresa, nombre_contacto, telefono_contacto, email_contacto, direccion, interesado, cantidad_alumnos, notas) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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

            $stmt = $mysqli->prepare($query);

            if (!$stmt) {
                $_SESSION['import_error'] = "Error en la preparación de la consulta: " . $mysqli->error;
                header("Location: empresas.php");
                exit();
            }

            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                // Limpiar y validar datos
                $cif = trim($data[0]);
                $nombre_comercial = trim($data[1]);
                $nombre_empresa = trim($data[2]);
                $telefono_empresa = trim($data[3]);
                $nombre_contacto = trim($data[4]);
                $telefono_contacto = trim($data[5]);
                $email_contacto = trim($data[6]);
                $direccion = trim($data[7]);
                $interesado = (strtolower(trim($data[8])) == 'sí') ? 1 : 0;
                $cantidad_alumnos = intval(trim($data[9]));
                $notas = trim($data[10]);

                // Bind de parámetros
                $stmt->bind_param(
                    "sssssssssis",
                    $cif,
                    $nombre_comercial,
                    $nombre_empresa,
                    $telefono_empresa,
                    $nombre_contacto,
                    $telefono_contacto,
                    $email_contacto,
                    $direccion,
                    $interesado,
                    $cantidad_alumnos,
                    $notas
                );

                // Ejecutar consulta
                if ($stmt->execute()) {
                    $importCount++;
                } else {
                    error_log("Error al insertar la empresa con CIF $cif: " . $stmt->error);
                }
            }

            fclose($handle);
            $stmt->close();

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
