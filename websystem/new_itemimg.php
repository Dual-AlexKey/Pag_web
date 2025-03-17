<?php
include 'conect/conexion.php';
include('estilo/data.php');
include('estilo/header.php');
include('estilo/menu.php');

$directorio = "../img/"; // ✅ Directorio correcto basado en la estructura del proyecto
$archivos = is_dir($directorio) ? scandir($directorio) : [];
?>

<div class="contenido-derecha">
    <a href="secciones.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Nuevo Item</h2></div>
    <div id="capaformulario">
        <form id="miFormulario" action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="formulario_tipo" value="Seccion">
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Nombre:</td>
                    <td class="colblancocen">
                        <input type="text" id="nombre" name="nombre" style="width: 72%;">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Imagen:</td>
                    <td class="colblancocen">
                        <input type="text" id="imagen_link" name="imagen_link" placeholder="https://ejemplo.com/imagen.jpg" style="width: 50%;" readonly>
                        <button type="button" class="boton-explorador" onclick="mostrarExplorador()">📂</button>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Transicion:</td>
                    <td class="colblancocen">
                        <select id="modulo" name="modulo" style="width: 40%;">
                            <option value="" selected></option>
                            <option value="Sliding from right">Sliding from right</option>
                            <option value="Sliding from left">Sliding from left</option>
                            <option value="Sliding from bottom">Sliding from bottom</option>
                            <option value="Sliding from top">Sliding from top</option>
                            <option value="Smooth sliding from right">Smooth sliding from right</option>
                            <option value="Smooth sliding from left">Smooth sliding from left</option>
                            <option value="Smooth sliging from bottom">Smooth sliding from bottom</option>
                            <option value="Smooth sliding from top">Smooth sliding from top</option>
                            <option value="Sliding tiles to right (random)">Sliding tiles to right (random)</option>
                            <option value="Sliding tiles to left (random)">Sliding tiles to left (random)</option>
                            <option value="Sliding tiles to bottom (random)">Sliding tiles to bottom (random)</option>
                            <option value="Sliding tiles to top (random)">Sliding tiles to top (random)</option>
                            <option value="Sliding random tiles to random directions">Sliding random tiles to random directions</option>
                            <option value="Fading tiles forward">Fading tiles forward</option>
                            <option value="Fading tiles reverse">Fading tiles reverse</option>
                            <option value="Fading tiles col-forward">Fading tiles col-forward</option>
                            <option value="Fading tiles col-reverse">Fading tiles col-reverse</option>
                            <option value="Smooth fading from right">Smooth fading from right</option>
                            <option value="Smooth fading from left">Smooth fading from left</option>
                            <option value="Smooth fading from bottom">Smooth fading from bottom</option>
                            <option value="Smooth fading from top">Smooth fading from top</option>
                            <option value="Crossfading">Crossfading</option>
                            <option value="Scaling tile in">Scaling tile in</option>
                            <option value="Scaling tile from out">Scaling tile from out</option>
                            <option value="Scaling tiles random">Scaling tiles random</option>
                            <option value="Scaling tiles from out random">Scaling tiles from out random</option>
                            <option value="Scaling in and rotating tiles random">Scaling in and rotating tiles random</option>
                            <option value="Scaling and rotating tiles from out random">Scaling and rotating tiles from out random</option>
                            <option value="Mirror-sliding tiles diagonal">Mirror-sliding tiles diagonal</option>
                            <option value="Mirror-sliding rows horizontal">Mirror-sliding rows horizontal</option>
                            <option value="Mirror-sliding rows vertical">Mirror-sliding rows vertical</option>
                            <option value="Mirror-sliding cols horizontal">Mirror-sliding cols horizontal</option>
                            <option value="Mirror-sliding cols vertical">Mirror-sliding cols vertical</option>
                            <option value="Turning tile from left">Turning tile from left</option>
                            <option value="Turning tile from right">Turning tile from right</option>
                            <option value="Turning tile from top">Turning tile from top</option>
                            <option value="Turning tile from bottom">Turning tile from bottom</option>
                            <option value="Turning tiles from left">Turning tiles from left</option>
                            <option value="Turning tiles from right">Turning tiles from right</option>
                            <option value="Turning tiles from top">Turning tiles from top</option>
                            <option value="Turning tiles from bottom">Turning tiles from bottom</option>
                            <option value="Turning rows from top">Turning rows from top</option>
                            <option value="Turning rows from bottom">Turning rows from bottom</option>
                            <option value="Turning cols from left">Turning cols from left</option>
                            <option value="Turning cols from right">Turning cols from right</option>
                            <option value="Flying and rotating tile from left">Flying and rotating tile from left</option>
                            <option value="Flying and rotating tile from right">Flying and rotating tile from right</option>
                            <option value="Flying and rotating tiles from left">Flying and rotating tiles from left</option>
                            <option value="Flying and rotating tiles from right">Flying and rotating tiles from right</option>
                            <option value="Flying and rotating tiles from random">Flying and rotating tiles from random</option>
                            <option value="Carousel">Carousel</option>
                            <option value="Carousel rows">Carousel rows</option>
                            <option value="Carousel cols">Carousel cols</option>
                            <option value="Carousel tiles horizontal">Carousel tiles horizontal</option>
                            <option value="Carousel tiles vertical">Carousel tiles vertical</option>
                            <option value="Carousel-mirror tiles horizontal">Carousel-mirror tiles horizontal</option>
                            <option value="Carousel-mirror tiles vertical">Carousel-mirror tiles vertical</option>
                            <option value="Carousel mirror rows">Carousel mirror rows</option>
                            <option value="Carousel mirror cols">Carousel mirror cols</option>
                            <option value="Sliding rows to right (forward)">Sliding rows to right (forward)</option>
                            <option value="Sliding rows to right (reverse)">Sliding rows to right (reverse)</option>
                            <option value="Sliding rows to right (random)">Sliding rows to right (random)</option>
                            <option value="Sliding rows to left (forward)">Sliding rows to left (forward)</option>
                            <option value="Sliding rows to left (reverse)">Sliding rows to left (reverse)</option>
                            <option value="Sliding rows to left (random)">Sliding rows to left (random)</option>
                            <option value="Sliding rows from top to bottom (forward)">Sliding rows from top to bottom (forward)</option>
                            <option value="Sliding rows from top to bottom (random)">Sliding rows from top to bottom (random)</option>
                            <option value="Sliding rows from bottom to top (reverse)">Sliding rows from bottom to top (reverse)</option>
                            <option value="Sliding rows from bottom to top (random)">Sliding rows from bottom to top (random)</option>
                            <option value="Sliding columns to bottom (forward)">Sliding columns to bottom (forward)</option>
                            <option value="Sliding columns to bottom (reverse)">Sliding columns to bottom (reverse)</option>
                            <option value="Sliding columns to bottom (random)">Sliding columns to bottom (random)</option>
                            <option value="Sliding columns to top (forward)">Sliding columns to top (forward)</option>
                            <option value="Sliding columns to top (reverse)">Sliding columns to top (reverse)</option>
                            <option value="Sliding columns to top (random)">Sliding columns to top (random)</option>
                            <option value="Sliding columns from left to right (forward)">Sliding columns from left to right (forward)</option>
                            <option value="Sliding columns from left to right (random)">Sliding columns from left to right (random)</option>
                            <option value="Sliding columns from right to left (reverse)">Sliding columns from right to left (reverse)</option>
                            <option value="Sliding columns from right to left (random)">Sliding columns from right to left (random)</option>
                        </select>
                        <a> si no se desea colocar una animacion colocar en blanco.</a>
                    </td>
                </tr>

                <tr>
                    <td class="colgrishome">Altura:</td>
                    <td class="colblancocen">
                        <input type="text" id="altura" name="altura" style="width: 10%;">
                        <a> 1 Segundo = 1000 Milisegundos.</a>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Orden:</td>
                    <td class="colblancocen">
                        <input type="text" id="Orden" name="Orden" style="width: 10%;">
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
                        $ruta = $directorio . $archivo;
                        echo "<div class='item' onclick='seleccionar(\"$ruta\")'>";
                        echo "<span class='eliminar-x' onclick='eliminarImagen(\"$archivo\", event)'>&times;</span>"; // ✅ Agregar botón de eliminar
                        echo "<img src='$ruta' alt='$archivo' class='preview'>";
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
<?php
include('estilo/footer.php');
?>
