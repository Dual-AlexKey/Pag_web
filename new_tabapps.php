<?php
include 'conect/conexion.php';
//inclusion de informacion
include('estilo/data.php');
// Incluir el header.php
include('estilo/header.php');
// Incluir el menu.php
include('estilo/menu.php');
include('estilo/tabla_menu.php');
include('conect/sendccion.php');

// Consultar las tablas que comienzan con 'menu_'
$tablas_menu = [];
$query = "SHOW TABLES LIKE 'menu_%'";
$resultado_tablas = mysqli_query($conexion, $query);

while ($row = mysqli_fetch_row($resultado_tablas)) {
    $tablas_menu[] = $row[0];  // Almacenar el nombre de las tablas
}

// Obtener los datos de las tablas 'menu_'
$codigos = [];
foreach ($tablas_menu as $tabla) {
    $query = "SELECT * FROM $tabla";  // Seleccionar todo de la tabla
    $resultado = mysqli_query($conexion, $query);

    while ($row = mysqli_fetch_assoc($resultado)) {
        $cod = $row['cod'];  // Suponiendo que 'cod' es el campo de identificación

        if (!in_array($cod, $codigos)) {
            // Si el código no está en el array de códigos, añadirlo
            $codigos[] = $cod;
        }
    }
}

// Obtener los datos únicos de las tablas 'menu_'
$datos_unicos = [];
foreach ($codigos as $cod) {
    foreach ($tablas_menu as $tabla) {
        $query = "SELECT * FROM $tabla WHERE cod = '$cod'";  // Filtrar por código
        $resultado = mysqli_query($conexion, $query);

        while ($row = mysqli_fetch_assoc($resultado)) {
            if ($row['cod'] == $cod && !isset($datos_unicos[$cod])) {
                $datos_unicos[$cod] = $row;  // Guardar solo el primer registro encontrado para cada código
                break;
            }
        }
    }
}
?>

<!-- Contenedor principal con las dos columnas -->
<div class="contenido-derecha">
    <a href="tablero.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Apps</h2></div>
    
    <div id="capaformulario">
        <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="formulario_tipo" value="Apps">            
            <!-- Campos Título, Imagen y URL en la parte superior -->
            <div class="columna-formulario">
                <table class="tableborderfull">
                    <tr>
                        <td class="colgrishome">Titulo:</td>
                        <td class="colblancocen">
                            <input type="text" id="nombre" name="nombre" style="width: 50%;">
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Apps:</td>
                        <td class="colblancocen">
                            <select id="apps" name="apps" style="width: 30%;">
                                <option value="RS">Redes Sociales</option>
                                <option value="S">Subscribase</option>
                                <option value="MP">Menu Pie</option>
                                <option value="FB">Fromulario Buscar</option>
                                <option value="FC">Formulario Contactactos</option>
                            </select>
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
                                // Mostrar solo el campo 'nombre' y el checkbox
                                foreach ($datos_unicos as $dato) {
                                    echo "<tr>";
                                    echo "<td><input type='checkbox' name='seleccionados[]' value='" . htmlspecialchars($dato['cod']) . "'></td>";
                                    echo "<td>" . htmlspecialchars($dato['nombre']) . "</td>";  // Mostrar solo el campo 'nombre'
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </td>
                    <td>
                    <table class="tableborderfull">
                            <tr>
                                <td class="colgrishome">Ubicación:</td>
                                <td class="colblancocen">
                                    <select id="ubicacion" name="ubicacion" required>
                                        <option value="Cuerpo top 1">Cuerpo top 1</option>
                                        <option value="Cuerpo top 2">Cuerpo top 2</option>
                                        <option value="Cuerpo top 3">Cuerpo top 3</option>
                                        <option value="Columna Izquierda">Columna Izquierda</option>
                                        <option value="Columna Central">Columna Central</option>
                                        <option value="Columna Derecha">Columna Derecha</option>
                                        <option value="Cuerpo Bottom 1">Cuerpo Bottom 1</option>
                                        <option value="Cuerpo Bottom 2">Cuerpo Bottom 2</option>
                                        <option value="Cuerpo Bottom 3">Cuerpo Bottom 3</option>
                                        <option value="Pie de Pagina">Pie de Pagina</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="colgrishome">Orden:</td>
                                <td class="colblancocen">
                                    <input type="text" id="Orden" name="Orden">
                                </td>
                            </tr>                            
                            <!-- Otras secciones con columnas, márgenes, fechas, etc. -->
                            <tr>
                                <td class="colgrishome">Columnas:</td>
                                <td class="colblancocen">
                                    <select id="columnas" name="columnas" required>
                                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                                            <option value="<?= $i ?>">Columna <?= $i ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="colgrishome">Columnas Móviles:</td>
                                <td class="colblancocen">
                                    <select id="columnas_moviles" name="columnas_moviles" required>
                                        <option value=""></option>
                                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                                            <option value="<?= $i ?>">Columna <?= $i ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="colgrishome">Estilo:</td>
                                <td class="colblancocen">
                                    <select id="estilo" name="estilo" required>
                                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                                            <option value="<?= $i ?>">Estilo <?= $i ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="colgrishome">Margenes:</td>
                                <td class="colblancocen">
                                    <label><input type="checkbox" name="margen[]" value="IZQ"> IZQ</label>
                                    <label><input type="checkbox" name="margen[]" value="DER"> DER</label>
                                    <label><input type="checkbox" name="margen[]" value="SUP"> SUP</label>
                                    <label><input type="checkbox" name="margen[]" value="INF"> INF</label>
                                </td>
                            </tr>
                            <tr>
                                <td class="colgrishome">Fecha Inicio:</td>
                                <td class="colblancocen">
                                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= date('Y-m-d') ?>" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="colgrishome">Fecha Final:</td>
                                <td class="colblancocen">
                                    <input type="date" id="fecha_final" name="fecha_final" value="" placeholder="dd/mm/aaaa" required>
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
