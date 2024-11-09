<?php

$mysqli = new mysqli('localhost','root','1234','fepla_crm');

if($mysqli->connect_error){
    die('Error connecting to MySQL server.');
}
?>