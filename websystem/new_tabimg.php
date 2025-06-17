<?php
include 'conect/conexion.php';
include('estilo/data.php');
include('estilo/header.php');
include('estilo/menu.php');
include('estilo/tabla_menu.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$datos = [
    'id' => '',
    'nombre' => '',
    'imagen_link' => '',
    'link' => '',
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
        $datos_bd = $resultado->fetch_assoc();
        $datos = array_merge($datos, $datos_bd);
        $datos['margen'] = !empty($datos['margen']) ? explode(',', $datos['margen']) : [];
    }

    $stmt->close();
}

$tabla_valor = isset($datos['tabla']) ? trim($datos['tabla']) : '';
$tabla_valores = array_map('trim', explode(',', $tabla_valor));

// 1. Obtener todos los datos de las tablas *_cabecerat y subnivel
$datos_jerarquicos = [];

$tablas_query = mysqli_query($conexion, "SHOW TABLES");
while ($row = mysqli_fetch_row($tablas_query)) {
    if (str_ends_with($row[0], '_cabecerat')) {
        $res = mysqli_query($conexion, "SELECT nombre, cod, Num_nivel FROM {$row[0]}");
        while ($fila = mysqli_fetch_assoc($res)) {
            $datos_jerarquicos[] = [
                'nombre' => $fila['nombre'],
                'cod' => $fila['cod'],
                'Num_nivel' => $fila['Num_nivel'],
                'seccion' => '',
            ];
        }
    }
}

$res = mysqli_query($conexion, "SELECT nombre, cod, Num_nivel, secciones FROM subnivel");
while ($fila = mysqli_fetch_assoc($res)) {
    $datos_jerarquicos[] = [
        'nombre' => $fila['nombre'],
        'cod' => $fila['cod'],
        'Num_nivel' => $fila['Num_nivel'],
        'seccion' => $fila['secciones'],
    ];
}

// 2. Construir jerarquía usando 'secciones'
$items = [];
$relaciones = [];

foreach ($datos_jerarquicos as $item) {
    $nombre = $item['nombre'];
    $seccion = $item['seccion'];
    $items[$nombre] = $item;

    if (!empty($seccion)) {
        $segmentos = array_filter(explode('/', $seccion));
        $ultimo = end($segmentos);
        $penultimo = prev($segmentos);

        if ($ultimo !== $nombre) continue;
        if ($penultimo) $relaciones[$nombre] = $penultimo;
    }
}

$arbol = [];
foreach ($items as $nombre => $item) {
    if (!isset($relaciones[$nombre])) {
        $arbol[$nombre] = &$items[$nombre];
        $items[$nombre]['hijos'] = [];
    } else {
        $padre = $relaciones[$nombre];
        if (!isset($items[$padre]['hijos'])) {
            $items[$padre]['hijos'] = [];
        }
        $items[$padre]['hijos'][] = &$items[$nombre];
    }
}


$directorio = "../img/";
$archivos = is_dir($directorio) ? scandir($directorio) : [];
?>


<!-- Contenedor principal con las dos columnas -->
<!-- 📌 Input oculto para ID -->

<div class="contenido-derecha">
    <a href="tablero.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Imagen</h2></div>
    
    <div id="capaformulario">
        <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="formulario_tipo" value="Imagen">            
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
                        <td class="colgrishome">Imagen:</td>
                        <td class="colblancocen">
                            <input type="text" id="imagen_link" name="imagen_link" 
                                placeholder="https://ejemplo.com/imagen.jpg" style="width: 30%;"
                                value="<?= isset($datos['imagen']) ? htmlspecialchars($datos['imagen']) : '' ?>">
                                <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_link')">📂</button>
                                </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">URL:</td>
                        <td class="colblancocen">
                            <input type="text" id="link" name="link" required readonly style="width: 50%;"
                                value="<?= isset($datos['link']) ? htmlspecialchars($datos['link']) : '' ?>">
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
                                        // Agrupar por cod
                                        $estructura_por_cod = [];
                                        foreach ($datos_jerarquicos as $item) {
                                            $estructura_por_cod[$item['cod']][] = $item;
                                        }

                                        foreach ($estructura_por_cod as $cod => $nodos) {
                                            foreach ($nodos as $nodo) {
                                                $nivel = intval($nodo['Num_nivel']);
                                                $nombre = htmlspecialchars($nodo['nombre']);
                                                $cod_actual = htmlspecialchars($nodo['cod']);
                                                $checked = in_array($nombre, $tabla_valores) ? 'checked' : '';
                                                $tiene_hijos = count($nodos) > 1 && $nivel === 1;
                                                $grupo_id = "grupo_" . md5($cod_actual);
                                                $padding = max(0, ($nivel - 1) * 20);

                                                // Estilo para alinear todo sin márgenes extra
                                                $style_td = "padding: 0; margin: 0;";
                                                $style_div = "display: flex; align-items: center; padding-left: {$padding}px; gap: 5px;";

                                                $tr_class = $nivel === 1 ? "nivel-1" : "nivel-hijo $grupo_id";
                                                $tr_style = $nivel > 1 ? "style='display:none;'" : "";

                                                echo "<tr class='$tr_class' $tr_style>";
                                                echo "<td style='$style_td'>";
                                                echo "<div style='$style_div'>";

                                                if ($nivel === 1 && $tiene_hijos) {
                                                    echo "<span class='toggle-hijos' data-target='$grupo_id' style='cursor:pointer;'>+</span>";
                                                } else {
                                                    echo "<span style='display:inline-block; width:9px;'></span>";
                                                }

                                                echo "<input type='checkbox' name='seleccionados[]' value='$cod_actual' $checked>";
                                                echo "<span>$nombre</span>";

                                                echo "</div></td></tr>";
                                            }
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
                                <input type="text" id="orden" name="orden" value="<?= htmlspecialchars($datos['orden'] ?? '') ?>">
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

<!-- 🔹 MODAL DEL EXPLORADOR DE IMÁGENES -->
<div id="modal-explorador" class="modal">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarExplorador()">&times;</span>
        <h3>Explorador de Imágenes</h3>

        <!-- 🔹 FORMULARIO DE SUBIDA DE IMÁGENES -->
        <form id="form-subida" enctype="multipart/form-data">
            <input type="file" id="imagen" name="imagen" accept="image/*">
            <button type="button" class="boton-subir" onclick="subirImagen()">Subir Imagen</button>
            <button type="button" class="boton-eliminar" onclick="activarEliminar()">Eliminar</button>
        </form>
        <!-- 🔹 LISTADO DE IMÁGENES QUE SE ACTUALIZARÁ AUTOMÁTICAMENTE -->
        <div class="explorador" id="lista-imagenes">
            <?php
            $directorio = "../img/";
            $archivos = is_dir($directorio) ? scandir($directorio) : [];

            if (!empty($archivos)) {
                foreach ($archivos as $archivo) {
                    if ($archivo != "." && $archivo != "..") {
                        $rutaOriginal = $directorio . $archivo; // ✅ Ruta original con ../img/
                        echo "<div class='item' onclick='seleccionar(\"$rutaOriginal\")'>"; // ✅ Enviar la ruta original
                        echo "<img src='$rutaOriginal' alt='$archivo' class='preview'>";
                        echo "</div>";
                    }
                }
            } else {
                echo "<p>No se encontraron imágenes.</p>";
            }
            ?>
        </div>
    </div>
</div>  


<script>
document.addEventListener("DOMContentLoaded", function () {
    const botonesToggle = document.querySelectorAll(".toggle-hijos");

    botonesToggle.forEach(boton => {
        boton.addEventListener("click", function () {
            const target = this.dataset.target;
            const hijos = document.querySelectorAll(`.${target}`);
            const visible = hijos[0].style.display !== "none";

            hijos.forEach(fila => {
                fila.style.display = visible ? "none" : "";
            });

            this.textContent = visible ? "+" : "-";
        });
    });

    document.querySelector(".accion-boton:nth-child(1)").addEventListener("click", () => {
        document.querySelectorAll(".nivel-hijo").forEach(tr => tr.style.display = "");
        document.querySelectorAll(".toggle-hijos").forEach(span => span.textContent = "-");
    });

    document.querySelector(".accion-boton:nth-child(2)").addEventListener("click", () => {
        document.querySelectorAll(".nivel-hijo").forEach(tr => tr.style.display = "none");
        document.querySelectorAll(".toggle-hijos").forEach(span => span.textContent = "+");
    });
});
</script>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
