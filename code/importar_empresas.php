<?php
require 'conexion.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] === 0) {
        $file = $_FILES["csvFile"]["tmp_name"];

        if (($handle = fopen($file, "r")) !== FALSE) {
            // Leer el BOM si está presente
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                // Si no hay BOM, volver al inicio del archivo
                rewind($handle);
            }

            fgetcsv($handle, 1000, ";"); // Saltar la cabecera

            $importCount = 0;

            // Preparar la consulta con sentencias preparadas para evitar SQL Injection
            $query = "INSERT INTO empresas (nif, nombre_comercial, nombre_empresa, telefono_empresa, nombre_contacto, telefono_contacto, email_contacto, direccion, cp, web, email_empresa, interesado, cantidad_alumnos, descripcion, actividad_principal, otras_actividades, dni_profesor)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE
                      nombre_comercial = VALUES(nombre_comercial),
                      nombre_empresa = VALUES(nombre_empresa),
                      telefono_empresa = VALUES(telefono_empresa),
                      nombre_contacto = VALUES(nombre_contacto),
                      telefono_contacto = VALUES(telefono_contacto),
                      email_contacto = VALUES(email_contacto),
                      direccion = VALUES(direccion),
                      cp = VALUES(cp),
                      web = VALUES(web),
                      email_empresa = VALUES(email_empresa),
                      interesado = VALUES(interesado),
                      cantidad_alumnos = VALUES(cantidad_alumnos),
                      descripcion = VALUES(descripcion),
                      actividad_principal = VALUES(actividad_principal),
                      otras_actividades = VALUES(otras_actividades),
                      dni_profesor = VALUES(dni_profesor)";

            $stmt = $mysqli->prepare($query);

            if (!$stmt) {
                $_SESSION['import_error'] = "Error en la preparación de la consulta: " . $mysqli->error;
                header("Location: empresas.php");
                exit();
            }

            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                // Limpiar y validar datos
                $nif = isset($data[0]) ? trim($data[0]) : '';
                $nombre_comercial = isset($data[1]) ? trim($data[1]) : '';
                $nombre_empresa = isset($data[2]) ? trim($data[2]) : '';
                $telefono_empresa = isset($data[3]) ? trim($data[3]) : '';
                $nombre_contacto = isset($data[4]) ? trim($data[4]) : '';
                $telefono_contacto = isset($data[5]) ? trim($data[5]) : '';
                $email_contacto = isset($data[6]) ? trim($data[6]) : '';
                $direccion = isset($data[7]) ? trim($data[7]) : '';
                $cp = isset($data[8]) ? trim($data[8]) : '';
                $web = isset($data[9]) ? trim($data[9]) : '';
                $email_empresa = isset($data[10]) ? trim($data[10]) : '';
                $interesado = isset($data[11]) ? (strtolower(trim($data[11])) == 'sí' ? 1 : 0) : 0;
                $cantidad_alumnos = isset($data[12]) ? intval(trim($data[12])) : 0;
                $descripcion = isset($data[13]) ? trim($data[13]) : '';
                $actividad_principal = isset($data[14]) ? trim($data[14]) : '';
                $otras_actividades = isset($data[15]) ? trim($data[15]) : '';
                $dni_profesor = isset($data[16]) ? trim($data[16]) : null;

                // Verificar si el dni_profesor existe en la tabla profesores
                if ($dni_profesor !== null) {
                    $check_profesor = $mysqli->prepare("SELECT dni_nie FROM profesores WHERE dni_nie = ?");
                    $check_profesor->bind_param("s", $dni_profesor);
                    $check_profesor->execute();
                    $check_profesor->store_result();
                    if ($check_profesor->num_rows == 0) {
                        // Si no existe, establecer como null
                        $dni_profesor = null;
                    }
                    $check_profesor->close();
                }

                // Bind de parámetros
                $stmt->bind_param(
                    "ssssssssssssissss",
                    $nif,
                    $nombre_comercial,
                    $nombre_empresa,
                    $telefono_empresa,
                    $nombre_contacto,
                    $telefono_contacto,
                    $email_contacto,
                    $direccion,
                    $cp,
                    $web,
                    $email_empresa,
                    $interesado,
                    $cantidad_alumnos,
                    $descripcion,
                    $actividad_principal,
                    $otras_actividades,
                    $dni_profesor
                );

                // Ejecutar consulta
                if ($stmt->execute()) {
                    $importCount++;
                } else {
                    error_log("Error al insertar la empresa con NIF $nif: " . $stmt->error);
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