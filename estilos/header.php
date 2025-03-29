<?php
include __DIR__ . '/../websystem/conect/conexion.php'; // ✅ Conexión a la base de datos

$menu_items = []; // Array para almacenar los datos del menú

// ✅ Obtener todas las tablas que terminan en "_cabecerat"
$sql = "SHOW TABLES LIKE '%_cabecerat'";
$result = $conn->query($sql);

while ($row = $result->fetch_array()) {
    $table_name = $row[0]; // Nombre de la tabla
    $base_name = str_replace('_cabecerat', '', $table_name); // Quitamos "_cabecera"
    
    // ✅ Consulta para obtener el contenido de cada tabla
    $sql_data = "SELECT * FROM `$table_name`";
    $result_data = $conn->query($sql_data);

    while ($data = $result_data->fetch_assoc()) {
        $nombre_item = $data['nombre'] ?? ''; // Ajustar si la columna tiene otro nombre
        if (!empty($nombre_item)) {
            $menu_items[$nombre_item] = urlencode(strtolower($nombre_item)) . ".php";
        }
    }
}

$sql = "SELECT imgcabe, cabfondo FROM Empresa LIMIT 1"; // Suponemos que hay un solo registro relevante
$result = $conn->query($sql);

$headerStyle = ''; // Inicializamos la variable para los estilos dinámicos

if ($result && $row = $result->fetch_assoc()) {
    if (!empty($row['imgcabe'])) {
        // Si 'imgcabe' tiene un valor, usarlo como fondo de imagen
        $headerStyle = "background-image: url('{$row['imgcabe']}'); background-size: cover; background-position: center; background-repeat: no-repeat;";
    } elseif (!empty($row['cabfondo'])) {
        // Si 'cabfondo' tiene un valor, usarlo como color de fondo
        $headerStyle = "background-color: {$row['cabfondo']};";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Web</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="estilos/css/styles.css?<?php echo time(); ?>">
</head>
<body>
    <!-- Cabecera -->
    <header class="py-3 text-white" style="<?= $headerStyle; ?>">
    <div class="container custom-container">
        <div class="row align-items-center text-center">
            <!-- Columna 1: Logo -->
            <div class="col-4">
                <a href="index.php">
                    <img src="https://i.ibb.co/1JYrfbjH/Logo.png" alt="Logo" class="img-fluid">
                </a>
            </div>
            <!-- Columna 2: Carro o Inicio -->
            <div class="col-4">
                <img src="https://via.placeholder.com/100" alt="Carro o Inicio" class="img-fluid">
            </div>
            <!-- Columna 3: Contactos -->
            <div class="col-4">
                <img src="https://via.placeholder.com/100" alt="Contacto" class="img-fluid">
            </div>
        </div>
    </div>
</header>


    <!-- Menú de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container custom-container">
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" href="index.php">Inicio</a>
                </li>
                <?php foreach ($menu_items as $nombre => $url): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === $url ? 'active' : '' ?>" href="<?= $url ?>">
                            <?= ucfirst($nombre) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>

    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
