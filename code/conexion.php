<?php
//Step1
$mysqli = new mysqli('localhost','root','1234','fepla_crm');

if($mysqli->connect_error){
    die('Error connecting to MySQL server.');
}

printf("Conectado")

//  $db = mysqli_connect('localhost','root','1234','fepla_crm')
//  or die('Error connecting to MySQL server.');
?>