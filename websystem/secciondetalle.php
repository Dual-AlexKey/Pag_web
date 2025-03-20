<?php
include 'conect/conexion.php';
//inclusion de informacion
include('estilo/data.php');
// Incluir el header.php
include('estilo/header.php');
// Incluir el menu.php
include('estilo/menu.php');

// 🔹 Obtener "cod" desde la URL
$cod = $_GET['cod'] ?? '';

$datos = [
    'cod' => $cod,
    'estructsecc' => '',
    'estructcont' => '',
    'mostrar' => '',
    'estilosubsec' => '',
    'fondsecc' => '',
    'galeria' => '',
    'barrasubmenu' => '',
    'barramenu' => '',
    'ordensecc' => '',
    'orden' => '',
    'ordencont' => ''
];

// 🔹 Si hay un "cod", buscar los datos en la base de datos
if (!empty($cod)) {
    $sql = "SELECT cod, estructsecc, estructcont, mostrar, estilosubsec, fondsecc, galeria, barrasubmenu, barramenu, ordensecc, orden, ordencont 
            FROM detalles WHERE cod = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $cod); // "s" porque cod es texto
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $datos = $row; // 🔹 Cargar los datos si existen
        }

        $stmt->close();
    }
}
?>

<div class="contenido-derecha">
    <a href="secciones.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Parametros Seccion</h2></div>

    <div id="capaformulario">
    <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="formulario_tipo" value="SeccionPar"> 
        <input type="hidden" name="cod" value="<?= htmlspecialchars($datos['cod']) ?>">

        <div class="columna-formulario">
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Estructura para Seccion:</td>
                    <td>
                        <ul style="display: flex; gap: 20px; align-items: flex-start;" id="estructsecc" name="estructsecc">
                            <?php
                            $estilos = [
                                "Estilo Izquierda" => "https://i.ibb.co/7J5ry10N/estiloweb01.gif",
                                "Estilo Derecha" => "https://i.ibb.co/5WnwPvGR/estiloweb02.gif",
                                "Estilo 3Columnas" => "https://i.ibb.co/N4rtv5J/estiloweb03.gif",
                                "Estilo  Full" => "https://i.ibb.co/G4BKp12v/estiloweb04.gif",
                            ];
                            foreach ($estilos as $key => $img) {
                                $checked = (isset($datos['estructsecc']) && $datos['estructsecc'] == $key) ? 'checked' : '';
                                echo "<div style='width: 90px;'>
                                        <img src='$img' alt='$key' style='width: 80px; height: auto;'><br>
                                        <input type='radio' name='estructsecc' value='$key' $checked> <span>$key</span>
                                    </div>";
                            }
                            ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Estructura para Contenido:</td>
                    <td>
                        <ul style="display: flex; gap: 20px; align-items: flex-start;" id="estructcont" name="estructcont">
                            <?php
                            $estilos = [
                                "Estilo Izquierda" => "https://i.ibb.co/7J5ry10N/estiloweb01.gif",
                                "Estilo Derecha" => "https://i.ibb.co/5WnwPvGR/estiloweb02.gif",
                                "Estilo 3Columnas" => "https://i.ibb.co/N4rtv5J/estiloweb03.gif",
                                "Estilo Full" => "https://i.ibb.co/G4BKp12v/estiloweb04.gif",
                            ];
                            foreach ($estilos as $key => $img) {
                                $checked = (isset($datos['estructcont']) && $datos['estructcont'] == $key) ? 'checked' : '';
                                echo "<div style='width: 90px;'>
                                        <img src='$img' alt='$key' style='width: 80px; height: auto; margin-top: 3px;'><br>
                                        <input type='radio' name='estructcont' value='$key' $checked> <span>$key</span>
                                    </div>";
                            }
                            ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Datos a Mostrar:</td>
                    <td class="colblancocen">
                        <table class="sin-borde">
                            <tr>
                                <?php
                                $mostrar_opciones = [
                                    "img" => "Imagen", "tit" => "Título", "sut" => "Sub Título", "res" => "Resumen", "fep" => "Fecha Publicación",
                                    "tid" => "Tiempo/Duración", "nrd" => "Nro Dormitorios", "arc" => "Área Construida", "art" => "Área Terreno", "ubi" => "Ubicación",
                                    "pre" => "Precio","lee" => "Leer más"
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
                <tr>
                    <td class="colgrishome">Estilo de Subsecciones:</td>
                    <td>
                        <ul style="display: flex; gap: 20px; align-items: center;" id="estilosubsec" name="estilosubsec">
                            <?php
                            $estilos = [
                                "No mostrar" => "https://i.ibb.co/RppK23Ch/estilo-blanco.gif",
                                "Resumen" => "https://i.ibb.co/qLdNSmzM/estiloresumen.gif",
                                "Galeria" => "https://i.ibb.co/k29qfG19/estilogaleria.gif",
                                "Tabs" => "https://i.ibb.co/pqGHmxr/estiloslider2.gif",
                                "Acordeon" => "https://i.ibb.co/tytJkbV/estiloacordion.gif",
                            ];
                            foreach ($estilos as $key => $img) {
                                $checked = (isset($datos['estilosubsec']) && $datos['estilosubsec'] == $key) ? 'checked' : '';
                                echo "<div style='width: 90px;'>
                                        <img src='$img' alt='$key' style='width: 80px; height: auto;'><br>
                                        <input type='radio' name='estilosubsec' value='$key' $checked> <span style='font-size: 14px;'>$key</span>
                                    </div>";
                            }
                            ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Fondo Seccion:</td>
                    <td class="colblancocen">
                        <select id="fondsecc" name="fondsecc" style="width: 30%;">
                            <?php
                            $ordenes = [
                                "fondo1" => "Fondo seccion 1",
                                "fondo2" => "Fondo seccion 2",
                                "fondo3" => "Fondo seccion 3",
                                "fondo4" => "Fondo seccion 4",
                            ];
                            foreach ($ordenes as $key => $label) {
                                $selected = (isset($datos['fondsecc']) && $datos['fondsecc'] == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Ancho Galeria:</td>
                    <td class="colblancocen">
                        <select id="galeria" name="galeria" style="width: 30%;">
                            <?php
                            $ordenes = [
                                "columna1" => "1 Columna",
                                "columna2" => "2 Columnas",
                                "columna3" => "3 Columnas",
                                "columna4" => "4 Columnas",
                                "columna5" => "6 Columnas",
                                "columna6" => "12 Columnas",
                            ];
                            foreach ($ordenes as $key => $label) {
                                $selected = (isset($datos['galeria']) && $datos['galeria'] == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Barra Menu: Estilo Submenus</td>
                    <td class="colblancocen">
                        <select id="barrasubmenu" name="barrasubmenu" style="width: 30%;">
                            <?php
                            $ordenes = [
                                "menu0" => "No mostrar",
                                "menu1" => "Menu desplegable",
                                "menu2" => "Mega menu",
                            ];
                            foreach ($ordenes as $key => $label) {
                                $selected = (isset($datos['barrasubmenu']) && $datos['barrasubmenu'] == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Barra lateral: Estilo Menus</td>
                    <td class="colblancocen">
                        <select id="barramenu" name="barramenu" style="width: 30%;">
                            <?php
                            $ordenes = [
                                "menu00" => "No mostrar",
                                "menu01" => "Menu lateral principal",
                                "menu02" => "Solo sus submenus",
                                "menu03" => "Todo sus submenus",
                            ];
                            foreach ($ordenes as $key => $label) {
                                $selected = (isset($datos['barramenu']) && $datos['barramenu'] == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Orden seccion:</td>
                    <td class="colblancocen">
                        <input type="text" id="ordensecc" name="ordensecc" style="width: 12%" value="<?= htmlspecialchars($datos['ordensecc'] ?? '') ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Orden:</td>
                    <td class="colblancocen">
                        <input type="text" id="orden" name="orden" style="width: 12%" value="<?= htmlspecialchars($datos['orden'] ?? '') ?>">
                        <a>Items</a>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Orden contenido: </td>
                    <td class="colblancocen">
                        <select id="ordencont" name="ordencont" style="width: 30%;">
                            <?php
                            $ordenes = [
                                "orden0" => "Lo mas reciente",
                                "orden1" => "Por fecha desencendente",
                                "orden2" => "Por titulo alfabetico",
                                "orden3" => "Por su codigo interno",
                                "orden3" => "Por stock",
                            ];
                            foreach ($ordenes as $key => $label) {
                                $selected = (isset($datos['ordencont']) && $datos['ordencont'] == $key) ? 'selected' : '';
                                echo "<option value='$key' $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>                
            </table>
        </div>
        <div class="boton-container">
            <button name="aceptar" class="botonesAyC" type="submit">Aceptar</button>
            <button name="Cancelar" class="botonesAyC" type="button" onclick="window.location = 'tablero.php'">Cancelar</button>
        </div>
    </form>
</div>