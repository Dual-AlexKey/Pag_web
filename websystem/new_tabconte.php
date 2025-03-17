<?php
include 'conect/conexion.php';
//inclusion de informacion
include('estilo/data.php');
// Incluir el header.php
include('estilo/header.php');
// Incluir el menu.php
include('estilo/menu.php');
include('estilo/tabla_menu.php');

// Consultar las tablas que comienzan con 'menu_'
$tablas_menu = [];
$query = "SHOW TABLES LIKE 'menu_%'";
$resultado_tablas = mysqli_query($conexion, $query);

while ($row = mysqli_fetch_row($resultado_tablas)) {
    $tablas_menu[] = $row[0];  // Almacenar el nombre de las tablas
}

// Obtener los datos de las tablas 'menu_'
$modulos = [];

foreach ($tablas_menu as $tabla) {
    $query = "SELECT DISTINCT nombre FROM $tabla WHERE modulo LIKE '%Contenidos%'";
    $resultado = mysqli_query($conexion, $query);

    while ($row = mysqli_fetch_assoc($resultado)) {
        $modulos[$row['nombre']] = $row['nombre']; // Almacenar el nombre como clave para evitar duplicados
    }
}
$codigos_guardados = [];
$registros_cod = []; // Aquí guardamos los registros únicos por cod

foreach ($tablas_menu as $tabla) {
    $query = "SELECT * FROM $tabla";
    $resultado = mysqli_query($conexion, $query);

    while ($row = mysqli_fetch_assoc($resultado)) {
        $cod = $row['cod'];

        // Guardar solo el primer registro de cada 'cod'
        if (!in_array($cod, $codigos_guardados)) {
            $registros_cod[] = $row;
            $codigos_guardados[] = $cod;
        }
    }
}

// **Paso 2: Filtrar registros por 'codtab' (solo si tienen valor)**
$codtab_guardados = [];
$registros_finales = []; // Aquí guardamos los registros finales

foreach ($registros_cod as $row) {
    $codtab = $row['codtab'] ?? null;

    // Si 'codtab' está vacío, agregarlo sin filtrar
    if (empty($codtab)) {
        $registros_finales[] = $row;
    } 
    // Si 'codtab' tiene valor, agregarlo solo si es único
    elseif (!in_array($codtab, $codtab_guardados)) {
        $registros_finales[] = $row;
        $codtab_guardados[] = $codtab;
    }
}

// **Cargar datos para edición si hay un ID en la URL**
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$datos = [
    'id' => '',
    'nombre' => '',
    'modulo' => '',
    'categoria' => '',
    'nro_items' => '',
    'items_visibles' => '',
    'ordennum' => '',
    'estilocheck' => '',
    'mostrar' => '',
    'tabla' => '',
    'ubicacion' => '',
    'orden' => '', 
    'columnas' => '',
    'columnas_moviles' => '',
    'estilo' => '',
    'margen' => [],
    'fecha_inicio' => date('Y-m-d'),
    'fecha_final' => '',
];

if ($id > 0) {
    $stmt = $conexion->prepare("SELECT * FROM tablero WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos_bd = $resultado->fetch_assoc(); // 🔹 Obtener datos de la BD

        // 🔹 Mezclar `$datos_bd` con `$datos` para asegurar que todas las claves existan
        $datos = array_merge($datos, $datos_bd);

        // 🔹 Convertir `margen` a array si tiene valores guardados (separados por ",")
        $datos['margen'] = !empty($datos['margen']) ? explode(',', $datos['margen']) : [];
    }

    $stmt->close();
}



$tabla_valor = isset($datos['tabla']) ? trim($datos['tabla']) : '';

?>

<!-- Contenedor principal con las dos columnas -->
<div class="contenido-derecha">
    <a href="tablero.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Contenido</h2></div>
    
    <div id="capaformulario">
        <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="formulario_tipo" value="Contenidos"> 
        <input type="hidden" name="id" value="<?= isset($datos['id']) ? htmlspecialchars($datos['id']) : '' ?>">
            <!-- Campos Título, Imagen y URL en la parte superior -->
            <div class="columna-formulario">
                <table class="tableborderfull">
                    <tr>
                        <td class="colgrishome">Título:</td>
                        <td class="colblancocen">
                            <input type="text" id="nombre" name="nombre" required 
                                oninput="actualizarURL()" style="width: 50%;"
                                value="<?= isset($datos['nombre']) ? htmlspecialchars($datos['nombre']) : '' ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Modulo:</td>
                        <td class="colblancocen">
                            <select id="modulo" name="modulo" style="width: 30%;">
                            <?php
                                if (!empty($modulos)) {
                                    foreach ($modulos as $nombre) {
                                        $selected = (isset($datos['modulo']) && $datos['modulo'] == $nombre) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($nombre) . '" ' . $selected . '>' . htmlspecialchars($nombre) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No hay módulos disponibles</option>';
                                }
                            ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="colgrishome">Categoria:</td>
                        <td class="colblancocen">
                            <select id="categoria" name="categoria" style="width: 30%;">
                                <?php
                                $categorias = ["todas" => "Todas", "normal" => "Normal", "destacado" => "Destacado", "destacado_premium" => "Destacado Premium", "super_destacado" => "Super Destacado"];
                                foreach ($categorias as $key => $label) {
                                    $selected = (isset($datos['categoria']) && $datos['categoria'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$label</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="colgrishome">Nro. Items:</td>
                        <td class="colblancocen">
                            <input type="text" id="nro_items" name="nro_items" value="<?= isset($datos['nro_items']) ? htmlspecialchars($datos['nro_items']) : '' ?>" style="width: 80px;">
                            <a> Items Visibles</a>
                            <input type="text" id="items_visibles" name="items_visibles" value="<?= isset($datos['items_visibles']) ? htmlspecialchars($datos['items_visibles']) : '' ?>" style="width: 80px;">
                        </td>
                    </tr>

                    <tr>
                        <td class="colgrishome">Orden:</td>
                        <td class="colblancocen">
                            <select id="ordennum" name="ordennum" style="width: 30%;">
                                <?php
                                $ordenes = [
                                    "reciente" => "Los más recientes",
                                    "fecha_reciente" => "Por Fecha Recientes",
                                    "titulo_alfabetico" => "Por Título Alfabético",
                                    "codigo" => "Por Código Interno",
                                    "stock" => "Por Stock"
                                ];
                                foreach ($ordenes as $key => $label) {
                                    $selected = (isset($datos['ordennum']) && $datos['ordennum'] == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$label</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="colgrishome">Estilos:</td>
                        <td>
                            <ul style="display: flex; gap: 20px; align-items: center;" id="estilocheck" name="estilocheck">
                                <?php
                                $estilos = [
                                    "Resumen" => "https://i.ibb.co/qLdNSmzM/estiloresumen.gif",
                                    "Galeria" => "https://i.ibb.co/k29qfG19/estilogaleria.gif",
                                    "Carrusel" => "https://i.ibb.co/rR28NxqC/estiloslider.gif",
                                    "Carrusel Avanzado" => "https://i.ibb.co/Xr8xz0Tp/estiloportafolio.gif",
                                    "Clasic 1" => "https://i.ibb.co/0jsRQx1V/image.png",
                                    "Clasic 2" => "https://i.ibb.co/nWPbjrq/estilomodelo2.gif",
                                    "Acordion" => "https://i.ibb.co/tytJkbV/estiloacordion.gif",
                                    "Video" => "https://i.ibb.co/pqGHmxr/estiloslider2.gif"
                                ];
                                foreach ($estilos as $key => $img) {
                                    $checked = (isset($datos['estilocheck']) && $datos['estilocheck'] == $key) ? 'checked' : '';
                                    echo "<div style='width: 90px;'>
                                            <img src='$img' alt='$key' style='width: 80px; height: auto;'><br>
                                            <input type='radio' name='estilocheck' value='$key' $checked> <span style='font-size: 14px;'>$key</span>
                                        </div>";
                                }
                                ?>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td class="colgrishome">Mostrar:</td>
                        <td class="colblancocen">
                            <table class="sin-borde">
                                <tr>
                                    <?php
                                    $mostrar_opciones = [
                                        "img" => "Imagen", "tit" => "Título", "sut" => "Sub Título", "res" => "Resumen", "fep" => "Fecha Publicación",
                                        "tid" => "Tiempo/Duración", "nrd" => "Nro Dormitorios", "arc" => "Área Construida", "art" => "Área Terreno", "pre" => "Precio",
                                        "lee" => "Leer más"
                                    ];
                                    $mostrar_valores = isset($datos['mostrar']) ? explode(',', $datos['mostrar']) : [];
                                    $columnas = array_chunk($mostrar_opciones, 5, true);

                                    foreach ($columnas as $col) {
                                        echo "<td>";
                                        foreach ($col as $key => $label) {
                                            $checked = in_array($key, $mostrar_valores) ? 'checked' : '';
                                            echo "<label><input type='checkbox' name='mostrar[]' value='$key' $checked> $label</label><br>";
                                        }
                                        echo "</td>";
                                    }
                                    ?>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="bloque-verde"><h2>Publicacion</h2></div>
            <div class="columna-formulario">
            <table class="tableborderfull">
                <tr>
                    <td>
                        <div class="contenedor-button">
                            <div class="acciones-botones">
                                <button type="button" class="accion-boton">+</button>
                                <button type="button" class="accion-boton">-</button>
                                <button type="button" class="accion-boton">::</button>
                            </div>
                            <div class="columna-tabla">
                                <table class="tableborderfull">
                                    <?php
                                    // 🔹 Obtener el valor de `tabla` desde `tablero`
                                    $tabla_valor = isset($datos['tabla']) ? trim($datos['tabla']) : '';

                                    // 🔹 Convertir `tabla` en un array si tiene múltiples valores
                                    $tabla_valores = array_map('trim', explode(',', $tabla_valor)); // 🔥 Divide y elimina espacios extra

                                    // 🔹 Recorrer los registros de `menu_*` y marcar los checkboxes si `nombre` está en la lista de `tabla`
                                    foreach ($registros_finales as $datoM) {
                                        $cod_actual = trim($datoM['cod']);  // ✅ Guardar por `cod`
                                        $nombre_actual = trim($datoM['nombre']); // ✅ Marcar por `nombre`

                                        // 🔹 Comparar si `nombre_actual` está en la lista de `tabla_valores`
                                        $checked = in_array($nombre_actual, $tabla_valores) ? 'checked' : '';

                                        echo "<tr>";
                                        echo "<td><input type='checkbox' name='seleccionados[]' value='" . htmlspecialchars($cod_actual) . "' $checked></td>";
                                        echo "<td>" . htmlspecialchars($nombre_actual) . "</td>";  // Mostrar solo el campo 'nombre'
                                        echo "</tr>";
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </td>

                    <td>
                    <table class="tableborderfull">
                        <!-- 📌 Campo `ubicacion` -->
                        <tr>
                            <td class="colgrishome">Ubicación:</td>
                            <td class="colblancocen">
                                <select id="ubicacion" name="ubicacion" required>
                                    <?php
                                    $ubicaciones = [
                                        "Cuerpo top 1", "Cuerpo top 2", "Cuerpo top 3", 
                                        "Columna Izquierda", "Columna Central", "Columna Derecha", 
                                        "Cuerpo Bottom 1", "Cuerpo Bottom 2", "Cuerpo Bottom 3", 
                                        "Pie de Pagina"
                                    ];
                                    foreach ($ubicaciones as $ubic) {
                                        $selected = ($datos['ubicacion'] == $ubic) ? 'selected' : '';
                                        echo "<option value='$ubic' $selected>$ubic</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <!-- 📌 Campo `orden` -->
                        <tr>
                            <td class="colgrishome">Orden:</td>
                            <td class="colblancocen">
                                <input type="text" id="orden" name="orden" value="<?= htmlspecialchars($datos['orden'] ?? '') ?>"

                            </td>
                        </tr>

                        <!-- 📌 Campo `columnas` -->
                        <tr>
                            <td class="colgrishome">Columnas:</td>
                            <td class="colblancocen">
                                <select id="columnas" name="columnas" required>
                                    <?php for ($i = 1; $i <= 12; $i++) {
                                        $selected = ($datos['columnas'] == $i) ? 'selected' : '';
                                        echo "<option value='$i' $selected>Columna $i</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>

                        <!-- 📌 Campo `columnas_moviles` -->
                        <tr>
                            <td class="colgrishome">Columnas Móviles:</td>
                            <td class="colblancocen">
                                <select id="columnas_moviles" name="columnas_moviles" required>
                                    <option value=""> </option>
                                    <?php for ($i = 1; $i <= 12; $i++) {
                                        $selected = ($datos['columnas_moviles'] == $i) ? 'selected' : '';
                                        echo "<option value='$i' $selected>Columna $i</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>

                        <!-- 📌 Campo `estilo` -->
                        <tr>
                            <td class="colgrishome">Estilo:</td>
                            <td class="colblancocen">
                                <select id="estilo" name="estilo" required>
                                    <?php for ($i = 1; $i <= 12; $i++) {
                                        $selected = ($datos['estilo'] == $i) ? 'selected' : '';
                                        echo "<option value='$i' $selected>Estilo $i</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>

                        <!-- 📌 Campo `margen` (checkboxes) -->
                        <tr>
                            <td class="colgrishome">Márgenes:</td>
                            <td class="colblancocen">
                                <?php
                                $margenes = ["IZQ", "DER", "SUP", "INF"];
                                foreach ($margenes as $margen) {
                                    $checked = in_array($margen, $datos['margen']) ? 'checked' : '';
                                    echo "<label><input type='checkbox' name='margen[]' value='$margen' $checked> $margen</label> ";
                                }
                                ?>
                            </td>
                        </tr>

                        <!-- 📌 Campo `fecha_inicio` -->
                        <tr>
                            <td class="colgrishome">Fecha Inicio:</td>
                            <td class="colblancocen">
                                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($datos['fecha_inicio']) ?>" required>
                            </td>
                        </tr>

                        <!-- 📌 Campo `fecha_final` -->
                        <tr>
                            <td class="colgrishome">Fecha Final:</td>
                            <td class="colblancocen">
                                <input type="date" id="fecha_final" name="fecha_final" value="<?= htmlspecialchars($datos['fecha_final']) ?>">
                            </td>
                        </tr>
                    </table>
                    </td>
                </tr>
            </table>
            </div>
            <!-- Botones de Aceptar y Cancelar -->
            <div class="boton-container">
                <button name="aceptar" class="botonesAyC" type="submit">Aceptar</button>
                <button name="Cancelar" class="botonesAyC" type="button" onclick="window.location = 'tablero.php'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
