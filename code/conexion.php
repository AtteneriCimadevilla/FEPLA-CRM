<?php
$host = 'localhost:3306';
$user = 'root';
$password = '1234';
$database = 'fepla_crm';

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die('Error de conexión (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');
?>