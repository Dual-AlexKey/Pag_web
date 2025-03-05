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
?>

<!-- Contenedor principal con las dos columnas -->
<div class="contenido-derecha">
    <a href="tablero.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Imagen</h2></div>
    
    <div id="capaformulario">
        <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="formulario_tipo" value="Imagen">            
            <!-- Campos Título, Imagen y URL en la parte superior -->
            <div class="columna-formulario">
                <table class="tableborderfull">
                    <tr>
                        <td class="colgrishome">Título:</td>
                        <td class="colblancocen">
                            <input type="text" id="nombre" name="nombre" required oninput="actualizarURL()" style="width: 50%;">
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Imagen:</td>
                        <td class="colblancocen">
                        <input type="text" name="imagen_link" placeholder="https://ejemplo.com/imagen.jpg" style="width: 30%;">
                        <input type="file" id="imagen" name="imagen" accept="image/*" style="width: 40%;">
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">URL:</td>
                        <td class="colblancocen">
                            <input type="text" id="link" name="link" required readonly style="width: 50%;">
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
                                foreach ($registros_finales as $dato) {
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
                                    <input type="date" id="fecha_final" name="fecha_final" placeholder="dd/mm/aaaa">
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
