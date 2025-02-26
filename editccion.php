<?php
include 'conect/conexion.php';
include('estilo/data.php');
include('estilo/header.php');
include('estilo/menu.php');
include('estilo/tabla_menu.php');

// ✅ Obtener el código desde la URL
$cod_parametro = isset($_GET['cod']) ? trim($_GET['cod']) : '';
if (empty($cod_parametro)) {
    die("Error: No se proporcionó un código válido.");
}

// ✅ Buscar todas las tablas que comienzan con "menu_"
$sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
$result_tablas = $conn->query($sql_buscar_tablas);

if ($result_tablas->num_rows == 0) {
    die("Error: No se encontraron tablas en la base de datos.");
}

// ✅ Filtrar las tablas que contienen el `cod`
$nombres_tablas = [];
while ($fila = $result_tablas->fetch_array()) {
    $tabla = $fila[0];

    // Verificar si la tabla contiene el `cod`
    $sql_check = "SELECT COUNT(*) as count FROM `$tabla` WHERE cod = '$cod_parametro'";
    $result_check = $conn->query($sql_check);
    $row_check = $result_check->fetch_assoc();

    if ($row_check['count'] > 0) {
        $nombres_tablas[] = $tabla;
    }
}

// Si ninguna tabla tiene el código, mostrar error
if (empty($nombres_tablas)) {
    die("Error: No se encontraron registros con este código.");
}

// ✅ Unir correctamente los datos de cada tabla
$queries = [];
foreach ($nombres_tablas as $tabla) {
    $queries[] = "SELECT '$tabla' AS tabla, id, nombre, modulo, orden, nro_item, visitas, link, Num_nivel, cod, estilos FROM `$tabla` WHERE cod = '$cod_parametro'";
}

$sql_final = implode(" UNION ALL ", $queries);
$result = $conn->query($sql_final);

// ✅ Obtener los datos correctos
$fila = $result->fetch_assoc();

// ✅ Extraer valores de la base de datos
$moduloSeleccionado = isset($fila['modulo']) ? $fila['modulo'] : ''; 
$estiloSeleccionado = isset($fila['estilos']) ? $fila['estilos'] : ''; 
?>

<div class="contenido-derecha">
    <a href="secciones.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Editar Sección</h2></div>
    <div id="capaformulario">
        <form action="conect/modificar_tabla.php" method="post">
            <input type="hidden" name="idcontrol" value="<?php echo htmlspecialchars($fila['id']); ?>">
            <input type="hidden" name="tabla" value="<?php echo htmlspecialchars($fila['tabla']); ?>">

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
                    <td class="colgrishome">
                        <div style="display: flex; gap: 20px; align-items: right;" id="estilos" 
                            data-seleccionado="<?= htmlspecialchars($estiloSeleccionado) ?>">
                            <!-- Aquí se cargarán dinámicamente los estilos -->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Publicar en Menú:</td>
                    <td class="colgrishome">
                        <?php
                        // ✅ Obtener los menús disponibles
                        $sql_menus = "SHOW TABLES LIKE 'menu_%'";
                        $result_menus = $conn->query($sql_menus);
                        $menus = [];

                        while ($row_menu = $result_menus->fetch_array()) {
                            $menus[] = $row_menu[0];
                        }

                        if (!empty($menus)): ?>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php foreach ($menus as $index => $menu): ?>
                                    <?php
                                    // Quitar "menu_" del inicio
                                    $menu_limpio = preg_replace('/^menu_/', '', $menu);

                                    // Lista de ubicaciones dinámicas
                                    $ubicaciones = ['cabecera', 'pie', 'lateral', 'footer'];

                                    // Eliminar cualquier sufijo que coincida con una ubicación
                                    foreach ($ubicaciones as $ubicacion) {
                                        $menu_limpio = preg_replace('/_' . preg_quote($ubicacion, '/') . '$/', '', $menu_limpio);
                                    }

                                    // ✅ Verificar si la tabla actual es la que está en `$fila['tabla']`
                                    $checked = ($menu === $fila['tabla']) ? 'checked' : '';
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

                <tr>
                    <td align="center">
                        <button name="aceptar" class="boton" type="submit">Aceptar</button>
                    </td>
                    <td align="center">
                        <button name="Cancelar" class="boton" type="button" onclick="window.location = 'secciones.php'">Cancelar</button>
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>

<?php
include('estilo/footer.php');
?>
