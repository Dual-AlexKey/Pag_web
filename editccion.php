<?php
include 'conect/conexion.php';
include('estilo/data.php');
include('estilo/header.php');
include('estilo/menu.php');
include('estilo/tabla_menu.php');

// ✅ Obtener el código y codtab desde la URL
$cod_parametro = isset($_GET['cod']) ? trim($_GET['cod']) : '';
$codtab_parametro = isset($_GET['codtab']) ? trim($_GET['codtab']) : '';

if (empty($cod_parametro)) {
    die("Error: No se proporcionó un código válido.");
}

// ✅ Buscar todas las tablas que comienzan con "menu_"
$sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
$result_tablas = $conn->query($sql_buscar_tablas);

if ($result_tablas->num_rows == 0) {
    die("Error: No se encontraron tablas en la base de datos.");
}

// ✅ Guardar las tablas en un array
$menu_tables = [];
while ($fila = $result_tablas->fetch_array()) {
    $menu_tables[] = $fila[0];
}

// ✅ Unir correctamente los datos de cada tabla para buscar coincidencias en `cod` o `codtab`
$queries = [];
foreach ($menu_tables as $tabla) {
    $queries[] = "SELECT '$tabla' AS tabla, nombre, modulo, orden, nro_item, visitas, link, Num_nivel, cod, codtab, estilos FROM `$tabla` 
                  WHERE cod = '$cod_parametro' OR codtab = '$codtab_parametro'";
}

$sql_final = implode(" UNION ALL ", $queries);
$result = $conn->query($sql_final);

// ✅ Obtener los datos correctos
$fila = $result->fetch_assoc();

// ✅ Extraer valores de la base de datos
$moduloSeleccionado = isset($fila['modulo']) ? $fila['modulo'] : ''; 
$estiloSeleccionado = isset($fila['estilos']) ? $fila['estilos'] : ''; 

// ✅ Buscar en qué menús ya está publicada la sección
$menus_publicados = [];
foreach ($menu_tables as $menu) {
    $sql_check = "SELECT COUNT(*) as count FROM `$menu` WHERE cod = '$cod_parametro' OR codtab = '$codtab_parametro'";
    $result_check = $conn->query($sql_check);
    $row_check = $result_check->fetch_assoc();

    if ($row_check['count'] > 0) {
        $menus_publicados[] = $menu; // Guardamos las tablas donde ya está publicado
    }
}

?>

<div class="contenido-derecha">
    <a href="secciones.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Editar Sección</h2></div>
    <div id="capaformulario">
        <form action="conect/guardar_tablero.php" method="post">
            <input type="hidden" name="cod" value="<?php echo htmlspecialchars($fila['cod']); ?>">
            <input type="hidden" name="tabla" value="<?php echo htmlspecialchars($fila['tabla']); ?>">
            <input type="hidden" name="codtab" value="<?php echo htmlspecialchars($fila['codtab']); ?>">
            <input type="hidden" name="formulario_tipo" value="Editseccion">  
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Nombre:</td>
                    <td class="colblancocen">
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required oninput="actualizarURL()">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">URL:</td>
                    <td class="colblancocen">
                        <input type="text" id="link" name="link" value="<?php echo htmlspecialchars($fila['link']); ?>" required readonly>
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
                        <div style="display: flex; gap: 20px; align-items: right;" id="estilos" 
                            data-seleccionado="<?= htmlspecialchars($estiloSeleccionado) ?>">
                            <!-- Aquí se cargarán dinámicamente los estilos -->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Publicar en Menú:</td>
                    <td class="colblancocen">
                        <?php if (!empty($menu_tables)): ?>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php foreach ($menu_tables as $index => $menu): ?>
                                    <?php
                                    // Quitar "menu_" del inicio
                                    $menu_limpio = preg_replace('/^menu_/', '', $menu);

                                    // Lista de ubicaciones dinámicas
                                    $ubicaciones = ['cabecera', 'pie', 'lateral', 'footer'];

                                    // Eliminar cualquier sufijo que coincida con una ubicación
                                    foreach ($ubicaciones as $ubicacion) {
                                        $menu_limpio = preg_replace('/_' . preg_quote($ubicacion, '/') . '$/', '', $menu_limpio);
                                    }

                                    // ✅ Verificar si el `cod` o `codtab` están en la tabla actual
                                    $checked = in_array($menu, $menus_publicados) ? 'checked' : '';
                                    ?>
                                    <label style="display: flex; align-items: center;">
                                        <input type="checkbox" id="publicar_<?php echo $index; ?>" name="publicar[]" value="<?php echo $menu; ?>" <?php echo $checked; ?>>
                                        <span style="margin-left: 5px;"><?php echo htmlspecialchars($menu_limpio); ?></span>
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
                <button name="aceptar" class="botonesAyC" type="submit">Aceptar</button>
                <button name="Cancelar" class="botonesAyC" type="button" onclick="window.location = 'secciones.php'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
include('estilo/footer.php');
?>
