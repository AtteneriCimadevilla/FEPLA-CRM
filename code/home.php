<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - CRM-FEPLA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <img src="logo.png" alt="logo" class="logo">
    </header>
    
    <div class="container mt-5">
        <h1 class="my-5">Hola, <b><?php echo htmlspecialchars($_SESSION["nombre"]); ?></b>. Bienvenido al CRM-FEPLA.</h1>
        <p>Tu tipo de usuario es: <?php echo htmlspecialchars($_SESSION["tipo_usuario"]); ?></p>
        <p>
            <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
        </p>
    </div>
</body>
</html>