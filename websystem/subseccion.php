<?php
include 'conect/conexion.php';
// Inclusión de información
include('estilo/data.php');

// Incluir el header.php
include('estilo/header.php');

// Incluir el menu.php
include('estilo/menu.php');

include('estilo/tabla_menu.php');

// Inicializar el array con valores vacíos por defecto
$menu = [
    'seccion'  => '',
    'nombre'   => '',
    'link'     => '',
    'modulo'   => '',
    'estilos'  => '',
    'publicar' => [],
    'secciones' => ''
    
];

// Buscar tablas que comiencen con "menu_"
$tablas_menu = [];
$query = "SHOW TABLES LIKE 'menu_%'";
$resultado_tablas = mysqli_query($conexion, $query);

while ($fila = mysqli_fetch_row($resultado_tablas)) {
    $tablas_menu[] = $fila[0]; // Almacena el nombre de la tabla
}

// Recoger información de cada tabla "menu_"
$menus_info = [];
foreach ($tablas_menu as $tabla) {
    $sql = "SELECT * FROM $tabla LIMIT 1";
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $menus_info[$tabla] = $resultado->fetch_assoc(); // Guarda la info de la tabla
        if (!empty($menus_info[$tabla]['Num_nivel'])) {
            $menu['Num_nivel'] = (int)$menus_info[$tabla]['Num_nivel'];
        }
        break; 
    } else {
        $menus_info[$tabla] = []; // Si no hay datos, se mantiene vacío
    }
}
$seccion_nombre = isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '';
$nv = isset($_GET['nv']) ? htmlspecialchars($_GET['nv']) : ''; // Predeterminado a 1 si no está en la URL
$seccion_padre = isset($_GET['sc']) ? htmlspecialchars($_GET['sc']) : ''; // Sección padre

// Si `nv == 2`, incluir `seccion_padre` antes de `seccion_nombre`
if ($nv == 2 && !empty($seccion_padre)) {
    $seccion_nombre = $seccion_padre . "/" . $seccion_nombre;
}

?>

<div class="contenido-derecha">
    <a href="secciones.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Nueva Subseccion</h2></div>
    <div id="capaformulario">
        <form id="miFormulario" action="conect/guardar_tablero.php" method="post">
            <input type="hidden" name="formulario_tipo" value="Subseccion">
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Sección Principal:</td>
                    <td class="colblancocen">
                        <input id="secciones" name="secciones" type="hidden" value="<?= $seccion_nombre; ?>">
                        <span class="negrita" style="font-weight: 900;"><?= "/" . $seccion_nombre; ?></span> <!-- Negrita fuerte -->
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Nombre:</td>
                    <td class="colblancocen">
                        <input type="text" id="nombre" name="nombre" style="width: 50%;" required oninput="actualizarURL()" value="<?= htmlspecialchars($menu['nombre']) ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">URL:</td>
                    <td class="colblancocen">
                        <input type="text" id="link" name="link" style="width: 50%;" required readonly value="<?= htmlspecialchars($menu['link']) ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Módulo:</td>
                    <td class="colblancocen">
                        <select id="modulo" name="modulo" style="width: 20%;" required onchange="cambiarEstilos()">
                            <option value="Contenidos" <?= ($menu['modulo'] == 'Contenidos') ? 'selected' : '' ?>>Contenidos</option>
                            <option value="Catalogo" <?= ($menu['modulo'] == 'Catalogo') ? 'selected' : '' ?>>Catálogo</option>
                            <option value="Usuarios" <?= ($menu['modulo'] == 'Usuarios') ? 'selected' : '' ?>>Usuarios</option>
                            <option value="Formularios" <?= ($menu['modulo'] == 'Formularios') ? 'selected' : '' ?>>Formularios</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Estilos:</td>
                    <td class="colblancocen">
                        <div style="display: flex; gap: 20px;" id="estilos">
                            <!-- Estilos dinámicos se cargarán aquí -->
                        </div>
                    </td>
                </tr>
                <tr>
    <td class="colgrishome">Publicar en Menú:</td>
    <td class="colblancocen">
        <?php if (!empty($tablas_menu)): ?>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php foreach ($tablas_menu as $index => $tabla_menu): ?>
                    <?php
                    // Limpieza del nombre de la tabla
                    $menu_limpio = preg_replace('/^menu_/', '', $tabla_menu);
                    $ubicaciones = ['cabecerat', 'pie', 'cabeceral', 'cabeceram', 'columnai', 'columnad'];

                    foreach ($ubicaciones as $ubicacion) {
                        $menu_limpio = preg_replace('/_' . preg_quote($ubicacion, '/') . '$/', '', $menu_limpio);
                    }

                    // Verificar si el registro existe en la tabla
                    $sql_check = "SELECT COUNT(*) FROM $tabla_menu WHERE cod = ? OR codtab = ?";
                    $stmt_check = $conexion->prepare($sql_check);
                    $checked = '';
                    if ($stmt_check) {
                        $stmt_check->bind_param("ss", $cod, $codtab);
                        $stmt_check->execute();
                        $stmt_check->bind_result($existe);
                        $stmt_check->fetch();
                        $stmt_check->close();
                        if ($existe > 0) {
                            $checked = 'checked';
                        }
                    }
                    ?>
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" id="publicar_<?= $index; ?>" name="publicar[]" value="<?= $tabla_menu; ?>" <?= $checked; ?>>
                        <span style="margin-left: 5px;"><?= htmlspecialchars($menu_limpio); ?></span>
                    </label>
                    <?php if (($index + 1) % 3 == 0): ?>
                        <br>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No hay menús disponibles.</p>
        <?php endif; ?>
    </td>
</tr>

            </table>
            <div class="boton-container">
                <button name="aceptar" class="botonesAyC" type="submit" onclick="crearArchivo(event)">Aceptar</button>
                <button name="Cancelar" class="botonesAyC" type="button" onclick="window.location = 'secciones.php'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
