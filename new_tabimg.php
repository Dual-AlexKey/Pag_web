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
    <div class="bloque-verde"><h2>Nueva Sección</h2></div>
    
    <div id="capaformulario">
        <form action="conect/sendtablero.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="idcontrol">
            
            <!-- Campos Título, Imagen y URL en la parte superior -->
            <div class="columna-formulario">
                <table class="tableborderfull">
                    <tr>
                        <td class="colgrishome">Título:</td>
                        <td class="colblancocen">
                            <input type="text" id="titulo" name="titulo" required oninput="actualizarURL()">
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Imagen:</td>
                        <td class="colblancocen">
                            <input type="file" id="imagen" name="imagen" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">URL:</td>
                        <td class="colblancocen">
                            <input type="text" id="link" name="link" required readonly>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="bloque-verde"><h2>Publicacion</h2></div>
        <div class="contenedor-button">
            <div class="acciones-botones">
                    <button type="button" class="accion-boton">+</button>
                    <button type="button" class="accion-boton">-</button>
                    <button type="button" class="accion-boton">::</button>
                </div>
            </div>
            <div class="contenedor-tabla">
                
                <!-- Botones de acción (antes de la tabla SQL) -->
               
               
                <!-- Columna de la tabla SQL -->
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
                

                <!-- Columna con los campos (Ubicación, Fecha Inicio, etc.), movida más a la izquierda -->
                <div class="columna-formulario-izquierda">
                    <table class="tableborderfull">
                        <tr>
                            <td class="colgrishome">Ubicación:</td>
                            <td class="colblancocen">
                                <select id="ubicacion" name="ubicacion" required>
                                    <option value="D">Cuerpo top 1</option>
                                    <option value="E">Cuerpo top 2</option>
                                    <option value="F">Cuerpo top 3</option>
                                    <option value="G">Columna Izquierda</option>
                                    <option value="H">Columna Central</option>
                                    <option value="I">Columna Derecha</option>
                                    <option value="J">Cuerpo Bottom 1</option>
                                    <option value="K">Cuerpo Bottom 2</option>
                                    <option value="L">Cuerpo Bottom 3</option>
                                    <option value="M">Pie de Pagina</option>
                                </select>
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
                                    <option value="">Columna Vacía</option>
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
                </div>
                  
                
            </div>

            <!-- Botones de Aceptar y Cancelar -->
            <div class="boton-container">
                <button name="aceptar" class="botonesAyC" type="submit">Aceptar</button>
                <button name="Cancelar" class="botonesAyC" type="button" onclick="window.location = 'secciones.php'">Cancelar</button>
            </div>

        </form>
    </div>
</div>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
