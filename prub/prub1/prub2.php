<?php
// Detecta la profundidad de la ruta
$uri = $_SERVER['REQUEST_URI'];
$secciones = trim($uri, '/'); // Elimina "/" inicial y final
$niveles = substr_count($secciones, '/');

// Construye el prefijo para subir directorios
$dirPrefix = str_repeat('../', $niveles -1);

// Incluye archivos con rutas relativas dinámicas
include($dirPrefix . 'estilos/header.php');
include($dirPrefix . 'estilos/generar_design.php');
include($dirPrefix . 'contador_visitas.php');

$nombreArchivo = basename(__FILE__, '.php');
$contador = manejar_contador_por_pagina($nombreArchivo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diseño Dinámico - <?php echo $nombreArchivo; ?></title>
    <link rel="stylesheet" href="<?php echo $dirPrefix; ?>estilos/css/styles.css">
</head>
<body>
    <div class="layout">
        <?php generarDiseno($nombreArchivo); ?>
    </div>
</body>
</html>
<?php include($dirPrefix . 'estilos/footer.php'); ?>