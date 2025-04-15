<?php
include('estilos/header.php');
include __DIR__ . '/estilos/generar_design.php';
include ('contador_visitas.php');
// Obtener el nombre del archivo actual
$nombreArchivo = basename(__FILE__, '.php');
$contador = manejar_contador_por_pagina($nombreArchivo);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diseño Dinámico - <?php echo $nombreArchivo; ?></title>
    <link rel="stylesheet" href="estilos/css/styles.css">
</head>
<body>
    <div class="layout">
        <?php generarDiseno($nombreArchivo); ?>
    </div>
</body>
</html>
<?php
include('estilos/footer.php'); // Footer
?>