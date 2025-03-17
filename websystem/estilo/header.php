<?php
session_start();

// Cerrar sesión si se pasa "?logout=1" en la URL
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: ../websystem.php"); // Redirige al login después de cerrar sesión
    exit();
}

// Definir tiempo máximo de inactividad (15 minutos = 900 segundos)
$tiempo_inactividad = 900;

// 📌 Aplicar timeout solo si el usuario está dentro de "websystem/"
if (strpos($_SERVER["REQUEST_URI"], '/websystem/') !== false) {
    if (isset($_SESSION["ultimo_acceso"])) {
        $tiempo_transcurrido = time() - $_SESSION["ultimo_acceso"];

        if ($tiempo_transcurrido > $tiempo_inactividad) {
            session_destroy();
            header("Location: ../websystem.php?timeout=1");
            exit();
        }
    }

    // Actualizar el tiempo de último acceso solo dentro de websystem
    $_SESSION["ultimo_acceso"] = time();
}

// Si el usuario no está autenticado, redirigir al login
if (!isset($_SESSION["usuario"])) {
    header("Location: ../websystem.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rectángulos con Botones</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css?<?php echo time(); ?>" />
</head>
<body>

<!-- Rectángulo de Cabecera (horizontal) -->
<div class="rectangulo-cabecera">
    <!-- Botones en la esquina superior derecha -->
    <div class="botones-cabecera">
        <a href="panel.php">
            <button class="boton-cabecera">Inicio</button>
        </a>
        <button class="boton-cabecera">Perfil</button>
        <button class="boton-cabecera" onclick="window.location.href='../websystem.php?logout=1';">Salir</button>
    </div>
</div>

<!-- Rectángulos Verdes debajo del Rectángulo de Cabecera -->
<div class="rectangulo-verde"></div>
