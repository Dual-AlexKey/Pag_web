<?php
include __DIR__ . '/../websystem/conect/conexion.php';

$menu_items = [];
$estructura = [];

// Obtener tablas con datos
$sql = "SHOW TABLES LIKE '%_cabecerat'";
$result = $conn->query($sql);

while ($row = $result->fetch_array()) {
    $table_name = $row[0];
    $sql_data = "SELECT * FROM `$table_name`";
    $result_data = $conn->query($sql_data);

    while ($data = $result_data->fetch_assoc()) {
        $nombre = trim($data['nombre'] ?? '');
        $secciones = trim($data['secciones'] ?? '');

        $nombre_url = urlencode(strtolower($nombre)) . '.php';

        if (!empty($secciones)) {
            $parts = array_values(array_filter(explode('/', $secciones)));

            if (count($parts) === 1) {
                // Ej: /pie
                $padre = $parts[0];
                $estructura[$padre]['label'] = ucfirst($padre);
                $estructura[$padre]['url'] = '#';
                $estructura[$padre]['submenu'][] = [
                    'label' => ucfirst($nombre),
                    'url' => $nombre_url
                ];
            } elseif (count($parts) === 2) {
                [$padre, $categoria] = $parts;
                $estructura[$padre]['label'] = ucfirst($padre);
                $estructura[$padre]['url'] = '#';
                $estructura[$padre]['categorias'][$categoria][] = [
                    'label' => ucfirst($nombre),
                    'url' => $nombre_url
                ];
            } else {
                // Más niveles → opcional manejar
                $menu_items[$nombre] = [
                    'label' => ucfirst($nombre),
                    'url' => $nombre_url
                ];
            }
        } else {
            // Sin secciones, agregar directo al menú
            $menu_items[$nombre] = [
                'label' => ucfirst($nombre),
                'url' => $nombre_url
            ];
        }
    }
}

// Convertir estructura a $menu_items
foreach ($estructura as $padre => $data) {
    $submenu = [];

    // Items sin categoría dentro del padre
    if (!empty($data['submenu'])) {
        foreach ($data['submenu'] as $item) {
            $submenu[] = [
                'label' => $item['label'],
                'url' => $item['url'],
            ];
        }
    }

    // Items agrupados por categoría
    if (!empty($data['categorias'])) {
        foreach ($data['categorias'] as $cat => $items) {
            foreach ($items as $item) {
                $submenu[] = [
                    'label' => $item['label'],
                    'url' => $item['url']
                ];
            }
        }
    }

    $menu_items[$padre] = [
        'label' => ucfirst($padre),
        'url' => '#',
        'submenu' => $submenu
    ];
}

// Obtener estilos del encabezado
$sql = "SELECT imgcabe, cabfondo FROM Empresa LIMIT 1";
$result = $conn->query($sql);
$headerStyle = '';

if ($result && $row = $result->fetch_assoc()) {
    if (!empty($row['imgcabe'])) {
        $headerStyle = "background-image: url('{$row['imgcabe']}'); background-size: cover; background-position: center; background-repeat: no-repeat;";
    } elseif (!empty($row['cabfondo'])) {
        $headerStyle = "background-color: {$row['cabfondo']};";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="estilos/css/styles.css?<?php echo time(); ?>">
</head>
<body>

<!-- Menú de navegación con Mega Menú -->
<nav class="navbar navbar-expand-lg" style="<?= $headerStyle ?>">
    <div class="container-fluid justify-content-center">
        <a class="navbar-brand text-white" href="index.php">
            <img src="https://i.ibb.co/1JYrfbjH/Logo.png" alt="Logo" style="height: 40px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" href="index.php">Inicio</a>
                </li>

                <?php foreach ($menu_items as $item): ?>
                    <?php if (isset($item['submenu'])): ?>
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                                <?= $item['label'] ?>
                            </a>
                            <div class="dropdown-menu w-100 mt-0 custom-mega-menu">
                                <div class="container">
                                    <div class="row">
                                        <?php
                                        $columnas = array_chunk($item['submenu'], 3);
                                        foreach ($columnas as $col): ?>
                                            <div class="col-md-4">
                                                <?php foreach ($col as $sub): ?>
                                                    <a class="dropdown-item" href="<?= $sub['url'] ?>">|└─ <?= $sub['label'] ?></a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?= $item['url'] ?>">|└─ <?= $item['label'] ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
