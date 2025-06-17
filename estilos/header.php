<?php
include __DIR__ . '/../websystem/conect/conexion.php';
$menu = [];

$baseURL = '/hub'; // Ruta base para XAMPP

$sql = "SHOW TABLES LIKE '%_cabecerat'";
$result = $conn->query($sql);

while ($row = $result->fetch_array()) {
    $table_name = $row[0];
    $query = "SELECT cod, nombre FROM `$table_name`";
    $data = $conn->query($query);

    while ($rowData = $data->fetch_assoc()) {
        $cod = trim($rowData['cod']);
        $nombre = ucfirst(strtolower(trim($rowData['nombre'])));

        // Buscar tipo de barra en detalles
        $stmt = $conn->prepare("SELECT barrasubmenu FROM detalles WHERE cod = ?");
        $stmt->bind_param("s", $cod);
        $stmt->execute();
        $stmt->bind_result($barrasubmenu);
        $hasResult = $stmt->fetch();
        $stmt->close();

        if (!$hasResult) continue;

        if ($barrasubmenu === 'menu0') {
            $menu[$cod] = [
                'label' => $nombre,
                'url' => $baseURL . '/' . strtolower($nombre) . '.php',
                'children' => [],
                'type' => 'menu0',
            ];
        } elseif (in_array($barrasubmenu, ['menu1', 'menu2'])) {
            $menu[$cod] = [
                'label' => $nombre,
                'url' => "#",
                'children' => [],
                'type' => $barrasubmenu,
            ];

            $stmt = $conn->prepare("SELECT nombre, Num_nivel, secciones FROM subnivel WHERE cod = ?");
            $stmt->bind_param("s", $cod);
            $stmt->execute();
            $res = $stmt->get_result();

            $nivel2 = [];

            while ($sub = $res->fetch_assoc()) {
                $nombreSub = ucfirst(strtolower(trim($sub['nombre'])));
                $nivel = intval($sub['Num_nivel']);
                $secciones = trim($sub['secciones'] ?? '');
                $path = array_values(array_filter(explode('/', $secciones)));
                $url = $baseURL . $secciones . '/' . strtolower($sub['nombre']) . '.php';

                if ($nivel === 2) {
                    $keyNivel2 = strtolower(str_replace(' ', '', $nombreSub));
                    $menu[$cod]['children'][$keyNivel2] = [
                        'label' => $nombreSub,
                        'url' => $url,
                        'children' => []
                    ];
                    $nivel2[$path[0]] = $keyNivel2;
                } elseif ($nivel === 3 && isset($path[0])) {
                    $nivel2Key = $nivel2[$path[0]] ?? strtolower(str_replace(' ', '', $path[0]));
                    if (isset($menu[$cod]['children'][$nivel2Key])) {
                        $menu[$cod]['children'][$nivel2Key]['children'][] = [
                            'label' => $nombreSub,
                            'url' => $url
                        ];
                    }
                }
            }
            $stmt->close();
        }
    }
}

// Fondo de encabezado
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
        <a class="navbar-brand" href="index.php">
            <img src="https://i.ibb.co/1JYrfbjH/Logo.png" alt="Logo">
        </a>

        <button class="navbar-toggler px-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarExample2" aria-controls="navbarExample2" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarExample2">
            <ul class="navbar-nav" style="padding-left: 0.15rem">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" href="index.php">Inicio</a>
                </li>

                <?php foreach ($menu as $item): ?>
                    <?php if ($item['type'] === 'menu0'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $item['url'] ?>"><?= $item['label'] ?></a>
                        </li>

                    <?php elseif ($item['type'] === 'menu1'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdown<?= $item['label'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $item['label'] ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdown<?= $item['label'] ?>">
                                <?php foreach ($item['children'] as $child): ?>
                                    <li><a class="dropdown-item" href="<?= $child['url'] ?>"><?= $child['label'] ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                    <?php elseif ($item['type'] === 'menu2'): ?>
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle" href="#" id="megaDropdown<?= $item['label'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $item['label'] ?>
                            </a>
                            <div class="dropdown-menu w-100 mt-0" aria-labelledby="megaDropdown<?= $item['label'] ?>">
                                <div class="container">
                                    <div class="row">
                                        <?php 
                                        $count = 0;
                                        foreach ($item['children'] as $subkey => $child):
                                            if ($count % 3 === 0 && $count !== 0): ?>
                                                </div><div class="row">
                                            <?php endif; ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="list-group list-group-flush">
                                                    <a href="<?= $child['url'] ?>" class="mb-0 list-group-item text-uppercase fw-bold">
                                                        <?= $child['label'] ?>
                                                    </a>
                                                    <?php if (!empty($child['children'])): ?>
                                                        <?php foreach ($child['children'] as $nivel3): ?>
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
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
