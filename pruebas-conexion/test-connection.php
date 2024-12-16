<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function testDatabaseConnection() {
    $host = 'localhost';
    $user = 'root';
    $password = ''; // Replace with your actual password if set
    $database = 'fepla_crm';

    try {
        $mysqli = new mysqli($host, $user, $password, $database);

        if ($mysqli->connect_errno) {
            throw new Exception("Failed to connect to MySQL: " . $mysqli->connect_error);
        }

        $result = [
            'status' => 'success',
            'message' => 'Successfully connected to the database.'
        ];

        $mysqli->close();
    } catch (Exception $e) {
        $result = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];

        if (strpos($e->getMessage(), "Unknown database") !== false) {
            $result['hint'] = 'The database "fepla_crm" does not exist. You may need to create it.';
        } elseif (strpos($e->getMessage(), "Connection refused") !== false) {
            $result['hint'] = 'Connection refused. Make sure MySQL server is running.';
        }
    }

    header('Content-Type: application/json');
    echo json_encode($result);
}

testDatabaseConnection();