<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function testConnection($host, $user, $password, $database)
{
    try {
        $mysqli = new mysqli($host, $user, $password, $database);
        if ($mysqli->connect_errno) {
            throw new Exception($mysqli->connect_error);
        }
        $mysqli->close();
        return ["status" => "success", "message" => "Connected successfully"];
    } catch (Exception $e) {
        return ["status" => "error", "message" => $e->getMessage()];
    }
}

$configs = [
    ["localhost", "root", "", "fepla_crm"],
    ["localhost", "root", "1234", "fepla_crm"], // Replace 1234 with your actual password if different
    ["localhost:3306", "root", "", "fepla_crm"],
    ["localhost:3306", "root", "1234", "fepla_crm"], // Replace 1234 with your actual password if different
];

$results = [];
foreach ($configs as $index => $config) {
    $results["config_" . $index] = testConnection(...$config);
}

header('Content-Type: application/json');
echo json_encode($results);
