<?php
include __DIR__ . '/../websystem/conect/conexion.php';
$menu = [];

// TODO: QUITAR '/hub' CUANDO SUBAS A PRODUCCIÓN
$baseURL = '/hub/'; // Ruta base local para XAMPP
//$baseURL = '/'; // Ajusta si tu sitio no está en la raíz del dominio

// Obtener tablas con _cabecerat
$sql = "SHOW TABLES LIKE '%_cabecerat'";
$result = $conn->query($sql);

while ($row = $result->fetch_array()) {
    $table_name = $row[0];
    $query = "SELECT nombre, Num_nivel, secciones FROM `$table_name`";
    $data = $conn->query($query);

    while ($rowData = $data->fetch_assoc()) {
        $nombre = strtolower(trim($rowData['nombre']));
        $nivel = intval($rowData['Num_nivel']);
        $secciones = trim($rowData['secciones'] ?? '');
        $path = array_values(array_filter(explode('/', $secciones)));
        $seccionesPath = trim($rowData['secciones'] ?? '', '/');

        // Ruta absoluta desde raíz del sitio (para navegadores)
        $relativePath = trim($seccionesPath . '/' . $nombre . '.php', '/');
        $url = $baseURL . $relativePath;

        // Si necesitas usar rutas absolutas del sistema de archivos (opcional)
        $fullFilePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $relativePath;

        if ($nivel === 1) {
            $nivel1 = !empty($path[0]) ? $path[0] : $nombre;
            $menu[$nivel1] = [
                'label' => ucfirst($nombre),
                'url' => $url,
                'children' => []
            ];
        } elseif ($nivel === 2 && isset($path[0])) {
            $parent = $path[0];
            $key = $nombre;
            $menu[$parent]['children'][$key] = [
                'label' => ucfirst($nombre),
                'url' => $url,
                'children' => []
            ];
        } elseif ($nivel === 3 && isset($path[0], $path[1])) {
            $parent = $path[0];
            $child = $path[1];
            $menu[$parent]['children'][$child]['children'][] = [
                'label' => ucfirst($nombre),
                'url' => $url
            ];
        }
    }
}


// Fondo del encabezado
$headerStyle = '';
$sql = "SELECT imgcabe, cabfondo FROM Empresa LIMIT 1";
$res = $conn->query($sql);
if ($res && $row = $res->fetch_assoc()) {
    if (!empty($row['imgcabe'])) {
        $headerStyle = "background-image: url('{$row['imgcabe']}'); background-size: cover; background-position: center;";
    } elseif (!empty($row['cabfondo'])) {
        $headerStyle = "background-color: {$row['cabfondo']};";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nav</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos/css/styles.css?<?= time(); ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg" style="<?= $headerStyle ?>">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="https://i.ibb.co/1JYrfbjH/Logo.png" alt="Logo">
        </a>

        <!-- Toggle button -->
        <button class="navbar-toggler px-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarExample2" aria-controls="navbarExample2" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Collapsible wrapper -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarExample2">
            <ul class="navbar-nav" style="padding-left: 0.15rem">
                <!-- Inicio -->
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" href="index.php">Inicio</a>
                </li>

                <!-- Menú dinámico -->
                <?php foreach ($menu as $nivel1): ?>
                    <li class="nav-item dropdown position-static">
                    <a class="nav-link dropdown-toggle" href="<?= $nivel1['url'] ?>" id="navbarDropdown<?= $nivel1['label'] ?>" >
                        <?= $nivel1['label'] ?>
                    </a>

                        <div class="dropdown-menu w-100 mt-0" aria-labelledby="navbarDropdown<?= $nivel1['label'] ?>">
                            <div class="container">
                                <div class="row">
                                    <?php 
                                    $count = 0; // Contador para las filas
                                    foreach ($nivel1['children'] as $nivel2): 
                                        if ($count % 3 === 0 && $count !== 0): ?>
                                            </div><div class="row"> <!-- Nueva fila cada 3 elementos -->
                                        <?php endif; ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="list-group list-group-flush">
                                                <!-- Nivel 2 ahora es seleccionable -->
                                                <a href="<?= $nivel2['url'] ?>" class="mb-0 list-group-item text-uppercase font-weight-bold">
                                                    <?= $nivel2['label'] ?>
                                                </a>
                                                <?php if (!empty($nivel2['children'])): ?>
                                                    <?php foreach ($nivel2['children'] as $nivel3): ?>
                                                        <a href="<?= $nivel3['url'] ?>" class="list-group-item list-group-item-action">
                                                            <?= $nivel3['label'] ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php $count++; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>