<?php
include __DIR__ . '/../websystem/conect/conexion.php'; // ✅ Conexión a la base de datos



$menu_items = []; // Array para almacenar los datos de la tabla

// ✅ Obtener todas las tablas que terminan en "_cabecera"
$sql = "SHOW TABLES LIKE '%_cabecerat'";
$result = $conn->query($sql);

while ($row = $result->fetch_array()) {
    $table_name = $row[0]; // Nombre de la tabla
    $base_name = str_replace('_cabecerat', '', $table_name); // Quitamos "_cabecera"
    
    // ✅ Consulta para obtener el contenido de cada tabla
    $sql_data = "SELECT * FROM `$table_name`";
    $result_data = $conn->query($sql_data);

    while ($data = $result_data->fetch_assoc()) {
        // Asegúrate de que haya una columna 'nombre' en la tabla
        $nombre_item = $data['nombre'] ?? ''; // 🔥 AJUSTA si la columna tiene otro nombre
        if (!empty($nombre_item)) {
            // ✅ Generar href usando el mismo nombre que se muestra en el botón
            $menu_items[$nombre_item] = urlencode(strtolower($nombre_item)) . ".php";
        }
    }
}
?>

<!-- header.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina web</title>
    <link rel="stylesheet" type="text/css" href="estilos/css/styles.css?<?php echo time(); ?>" />
</head>
<body>

<!-- Rectángulo de Cabecera (horizontal) -->
<div class="rectangulo-cabecera">
        <div class="tabla-Group">
            <div class="elemento-header">
                <img src="https://i.ibb.co/1JYrfbjH/Logo.png" alt="Logo">
                <img src="https://i.ibb.co/1JYrfbjH/Logo.png" alt="Logo">
                
            </div>
            <div class="elemento-header">
                <img src="https://via.placeholder.com/100" alt="Carro o Inicio">
            </div>
            <div class="elemento-header">
                <img src="https://via.placeholder.com/100" alt="Contacto">
            </div>
        </div>
</div>
<div class="menu-inferior">
            <a href="index.php">Inicio</a>
            <?php foreach ($menu_items as $nombre => $url): ?>
                <a href="<?= $url ?>"><?= ucfirst($nombre) ?></a>
            <?php endforeach; ?>
        </div>
    
