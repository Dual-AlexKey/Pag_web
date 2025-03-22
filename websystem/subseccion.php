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

// Recoger valores de la URL
$nombre = isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '';
$accion = isset($_GET['accion']) ? htmlspecialchars($_GET['accion']) : '';
$seccion_principal = ''; // Valor predeterminado

// Buscar en las tablas si hay un registro que coincida con `nombre`
foreach ($tablas_menu as $tabla) {
    $sql = "SELECT * FROM $tabla WHERE nombre = '$nombre' LIMIT 1";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();

        // Recuperar todos los datos del registro en $menu
        $menu['nombre'] = $fila['nombre'];
        $menu['link'] = $fila['link'] ?? '';
        $menu['modulo'] = $fila['modulo'] ?? '';
        $menu['estilos'] = $fila['estilos'] ?? '';
        $menu['secciones'] = $fila['secciones'] ?? '';

        // Mostrar solo el valor de `secciones` si el identificador está presente
        if ($accion === 'subseccion') {
            $seccion_principal = $menu['secciones'];
        } else {
            // Si `secciones` está vacío o NULL, construir como "/nombre"
            if (empty($fila['secciones'])) {
                $seccion_principal = "/" . $nombre;
            } else {
                // Si `secciones` tiene un valor, añadir el valor de `nombre`
                $seccion_principal = $fila['secciones'] . "/" . $nombre;
            }
        }
        break;
    }
}

// Si no hay identificador, vaciar todos los campos excepto "Sección Principal" y "Estilos"
if ($accion !== 'subseccion') {
    $menu['nombre'] = '';
    $menu['link'] = '';
    $menu['modulo'] = '';
    $menu['publicar'] = [];
    // "Sección Principal" y "Estilos" permanecen como están
    $seccion_principal = empty($menu['secciones']) ? "/" . $nombre : $menu['secciones'] . "/" . $nombre;
}
$estiloSeleccionado = isset($fila['estilos']) ? $fila['estilos'] : ''; 
$moduloSeleccionado = isset($fila['modulo']) ? $fila['modulo'] : ''; 


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
                        <input id="secciones" name="secciones" type="hidden" value="<?= htmlspecialchars($seccion_principal); ?>">
                        <span class="negrita" style="font-weight: 900;"><?= htmlspecialchars($seccion_principal); ?></span>
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
                        <select id="modulo" name="modulo" required onchange="cambiarEstilos()">
                            <option value="Contenidos" <?= $moduloSeleccionado == 'Contenidos' ? 'selected' : '' ?>>Contenidos</option>
                            <option value="Catalogo" <?= $moduloSeleccionado == 'Catalogo' ? 'selected' : '' ?>>Catálogo</option>
                            <option value="Usuarios" <?= $moduloSeleccionado == 'Usuarios' ? 'selected' : '' ?>>Usuarios</option>
                            <option value="Formularios" <?= $moduloSeleccionado == 'Formularios' ? 'selected' : '' ?>>Formularios</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Estilos:</td>
                    <td class="colblancocen">
                        <div style="display: flex; gap: 20px; align-items: flex-start;" id="estilos" 
                            data-seleccionado="<?= htmlspecialchars($estiloSeleccionado) ?>">
                            <!-- Aquí se cargarán dinámicamente los estilos -->
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
                                    $checked = '';
                                    if ($accion === 'subseccion') {
                                        $sql_check = "SELECT COUNT(*) FROM $tabla_menu WHERE nombre = ?";
                                        $stmt_check = $conexion->prepare($sql_check);
                                        if ($stmt_check) {
                                            $stmt_check->bind_param("s", $nombre);
                                            $stmt_check->execute();
                                            $stmt_check->bind_result($existe);
                                            $stmt_check->fetch();
                                            $stmt_check->close();
                                            if ($existe > 0) {
                                                $checked = 'checked';
                                            }
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
