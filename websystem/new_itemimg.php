<?php
include 'conect/conexion.php';
include('estilo/header.php');
include('estilo/menu.php');

$directorio = "../img/"; // ✅ Directorio correcto basado en la estructura del proyecto
$archivos = is_dir($directorio) ? scandir($directorio) : [];
// **Cargar datos para edición si hay un ID en la URL**
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Valores por defecto (formulario vacío)
$datos = [
    'id_img' => '',
    'nombre' => '',
    'imagen_1' => '',
    'transicion' => '',
    'altura' => '',
    'orden' => ''
];

// Si hay un ID válido, buscar el registro en la base de datos
if ($id > 0) {
    $sql = "SELECT * FROM Imagenes WHERE id_img = $id LIMIT 1";
    $resultado = mysqli_query($conn, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $datos = mysqli_fetch_assoc($resultado);
    }
}
?>
<div class="contenido-derecha">
    <a href="new_img.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2><?php echo $id > 0 ? "Editar Item" : "Nuevo Item"; ?></h2></div>
    <div id="capaformulario">
        <form id="miFormulario" action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="formulario_tipo" value="Imagenes_Tablero">
            
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Nombre:</td>
                    <td class="colblancocen">
                        <input type="text" id="nombre" name="nombre" style="width: 72%;" 
                               value="<?php echo htmlspecialchars($datos['nombre']); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Imagen:</td>
                    <td class="colblancocen">
                        <input type="text" id="imagen_1" name="imagen_1" placeholder="https://ejemplo.com/imagen.jpg" 
                               style="width: 50%;" 
                               value="<?php echo htmlspecialchars($datos['imagen_1']); ?>">
                        <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_1')">📂</button>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Transición:</td>
                    <td class="colblancocen">
                        <select id="transicion" name="transicion" style="width: 40%;">
                            <option value="" <?php echo $datos['transicion'] == '' ? 'selected' : ''; ?>></option>
                            <option value="Sliding from right" <?php echo $datos['transicion'] == 'Sliding from right' ? 'selected' : ''; ?>>Sliding from right</option>
                            <option value="Sliding from left" <?php echo $datos['transicion'] == 'Sliding from left' ? 'selected' : ''; ?>>Sliding from left</option>
                            <option value="Sliding from bottom" <?php echo $datos['transicion'] == 'Sliding from bottom' ? 'selected' : ''; ?>>Sliding from bottom</option>
                            <option value="Sliding from top" <?php echo $datos['transicion'] == 'Sliding from top' ? 'selected' : ''; ?>>Sliding from top</option>
                            <option value="Smooth sliding from right" <?php echo $datos['transicion'] == 'Smooth sliding from right' ? 'selected' : ''; ?>>Smooth sliding from right</option>
                            <option value="Smooth sliding from left" <?php echo $datos['transicion'] == 'Smooth sliding from left' ? 'selected' : ''; ?>>Smooth sliding from left</option>
                            <option value="Smooth sliding from bottom" <?php echo $datos['transicion'] == 'Smooth sliding from bottom' ? 'selected' : ''; ?>>Smooth sliding from bottom</option>
                            <option value="Smooth sliding from top" <?php echo $datos['transicion'] == 'Smooth sliding from top' ? 'selected' : ''; ?>>Smooth sliding from top</option>
                            <option value="Sliding tiles to right (random)" <?php echo $datos['transicion'] == 'Sliding tiles to right (random)' ? 'selected' : ''; ?>>Sliding tiles to right (random)</option>
                            <option value="Sliding tiles to left (random)" <?php echo $datos['transicion'] == 'Sliding tiles to left (random)' ? 'selected' : ''; ?>>Sliding tiles to left (random)</option>
                            <option value="Sliding tiles to right (random)" <?php echo $datos['transicion'] == 'Sliding tiles to right (random)' ? 'selected' : ''; ?>>Sliding tiles to right (random)</option>
                            <option value="Sliding tiles to bottom (random)" <?php echo $datos['transicion'] == 'Sliding tiles to bottom (random)' ? 'selected' : ''; ?>>Sliding tiles to bottom (random)</option>
                            <option value="Sliding tiles to top (random)" <?php echo $datos['transicion'] == 'Sliding tiles to top (random)' ? 'selected' : ''; ?>>Sliding tiles to top (random)</option>
                            <option value="Sliding random tiles to random directions" <?php echo $datos['transicion'] == 'Sliding random tiles to random directions' ? 'selected' : ''; ?>>Sliding random tiles to random directions</option>
                            <option value="Fading tiles forward" <?php echo $datos['transicion'] == 'Fading tiles forward' ? 'selected' : ''; ?>>Fading tiles forward</option>
                            <option value="Fading tiles reverse" <?php echo $datos['transicion'] == 'Fading tiles reverse' ? 'selected' : ''; ?>>Fading tiles reverse</option>
                            <option value="Fading tiles col-forward" <?php echo $datos['transicion'] == 'Fading tiles col-forward' ? 'selected' : ''; ?>>Fading tiles col-forward</option>
                            <option value="Fading tiles col-reverse" <?php echo $datos['transicion'] == 'Fading tiles col-reverse' ? 'selected' : ''; ?>>Fading tiles col-reverse</option>
                            <option value="Smooth fading from right" <?php echo $datos['transicion'] == 'Smooth fading from right' ? 'selected' : ''; ?>>Smooth fading from right</option>
                            <option value="Smooth fading from left" <?php echo $datos['transicion'] == 'Smooth fading from left' ? 'selected' : ''; ?>>Smooth fading from left</option>
                            <option value="Smooth fading from bottom" <?php echo $datos['transicion'] == 'Smooth fading from bottom' ? 'selected' : ''; ?>>Smooth fading from bottom</option>
                            <option value="Smooth fading from top" <?php echo $datos['transicion'] == 'Smooth fading from top' ? 'selected' : ''; ?>>Smooth fading from top</option>
                            <option value="Crossfading" <?php echo $datos['transicion'] == 'Crossfading' ? 'selected' : ''; ?>>Crossfading</option>
                            <option value="Scaling tile in" <?php echo $datos['transicion'] == 'Scaling tile in' ? 'selected' : ''; ?>>Scaling tile in</option>
                            <option value="Scaling tile from out" <?php echo $datos['transicion'] == 'Scaling tile from out' ? 'selected' : ''; ?>>Scaling tile from out</option>
                            <option value="Scaling tiles random" <?php echo $datos['transicion'] == 'Scaling tiles random' ? 'selected' : ''; ?>>Scaling tiles random</option>
                            <option value="Scaling tiles from out random" <?php echo $datos['transicion'] == 'Scaling tiles from out random' ? 'selected' : ''; ?>>Scaling tiles from out random</option>
                            <option value="Scaling in and rotating tiles random" <?php echo $datos['transicion'] == 'Scaling in and rotating tiles random' ? 'selected' : ''; ?>>Scaling in and rotating tiles random</option>
                            <option value="Scaling and rotating tiles from out random" <?php echo $datos['transicion'] == 'Scaling and rotating tiles from out random' ? 'selected' : ''; ?>>Scaling and rotating tiles from out random</option>
                            <option value="Mirror-sliding tiles diagonal" <?php echo $datos['transicion'] == 'Mirror-sliding tiles diagonal' ? 'selected' : ''; ?>>Mirror-sliding tiles diagonal</option>
                            <option value="Mirror-sliding tiles diagonal" <?php echo $datos['transicion'] == 'Mirror-sliding tiles diagonal' ? 'selected' : ''; ?>>Mirror-sliding tiles diagonal</option>
                            <option value="Mirror-sliding rows horizontal" <?php echo $datos['transicion'] == 'Mirror-sliding rows horizontal' ? 'selected' : ''; ?>>Mirror-sliding rows horizontal</option>
                            <option value="Mirror-sliding rows vertical" <?php echo $datos['transicion'] == 'Mirror-sliding rows vertical' ? 'selected' : ''; ?>>Mirror-sliding rows vertical</option>
                            <option value="Mirror-sliding cols horizontal" <?php echo $datos['transicion'] == 'Mirror-sliding cols horizontal' ? 'selected' : ''; ?>>Mirror-sliding cols horizontal</option>
                            <option value="Mirror-sliding cols vertical" <?php echo $datos['transicion'] == 'Mirror-sliding cols vertical' ? 'selected' : ''; ?>>Mirror-sliding cols vertical</option>
                            <option value="Turning tile from left" <?php echo $datos['transicion'] == 'Turning tile from left' ? 'selected' : ''; ?>>Turning tile from left</option>
                            <option value="Turning tile from right" <?php echo $datos['transicion'] == 'Turning tile from right' ? 'selected' : ''; ?>>Turning tile from right</option>
                            <option value="Turning tile from top" <?php echo $datos['transicion'] == 'Turning tile from top' ? 'selected' : ''; ?>>Turning tile from top</option>
                            <option value="Turning tile from bottom" <?php echo $datos['transicion'] == 'Turning tile from bottom' ? 'selected' : ''; ?>>Turning tile from bottom</option>
                            <option value="Turning tiles from left" <?php echo $datos['transicion'] == 'Turning tiles from left' ? 'selected' : ''; ?>>Turning tiles from left</option>
                            <option value="Turning tiles from right" <?php echo $datos['transicion'] == 'Turning tiles from right' ? 'selected' : ''; ?>>Turning tiles from right</option>
                            <option value="Turning tiles from top" <?php echo $datos['transicion'] == 'Turning tiles from top' ? 'selected' : ''; ?>>Turning tiles from top</option>
                            <option value="Turning tiles from bottom" <?php echo $datos['transicion'] == 'Turning tiles from bottom' ? 'selected' : ''; ?>>Turning tiles from bottom</option>
                            <option value="Turning rows from top" <?php echo $datos['transicion'] == 'Turning rows from top' ? 'selected' : ''; ?>>Turning rows from top</option>
                            <option value="Turning rows from bottom" <?php echo $datos['transicion'] == 'Turning rows from bottom' ? 'selected' : ''; ?>>Turning rows from bottom</option>
                            <option value="Turning cols from left" <?php echo $datos['transicion'] == 'Turning cols from left' ? 'selected' : ''; ?>>Turning cols from left</option>
                            <option value="Turning cols from right" <?php echo $datos['transicion'] == 'Turning cols from right' ? 'selected' : ''; ?>>Turning cols from right</option>
                            <option value="Flying and rotating tile from left" <?php echo $datos['transicion'] == 'Flying and rotating tile from left' ? 'selected' : ''; ?>>Flying and rotating tile from left</option>
                            <option value="Flying and rotating tile from right" <?php echo $datos['transicion'] == 'Flying and rotating tile from right' ? 'selected' : ''; ?>>Flying and rotating tile from right</option>
                            <option value="Flying and rotating tiles from left" <?php echo $datos['transicion'] == 'Flying and rotating tiles from left' ? 'selected' : ''; ?>>Flying and rotating tiles from left</option>
                            <option value="Flying and rotating tiles from right" <?php echo $datos['transicion'] == 'Flying and rotating tiles from right' ? 'selected' : ''; ?>>Flying and rotating tiles from right</option>
                            <option value="Flying and rotating tiles from random" <?php echo $datos['transicion'] == 'Flying and rotating tiles from random' ? 'selected' : ''; ?>>Flying and rotating tiles from random</option>
                            <option value="Carousel" <?php echo $datos['transicion'] == 'Carousel' ? 'selected' : ''; ?>>Carousel</option>
                            <option value="Carousel rows" <?php echo $datos['transicion'] == 'Carousel rows' ? 'selected' : ''; ?>>Carousel rows</option>
                            <option value="Carousel cols" <?php echo $datos['transicion'] == 'Carousel cols' ? 'selected' : ''; ?>>Carousel cols</option>
                            <option value="Carousel tiles horizontal" <?php echo $datos['transicion'] == 'Carousel tiles horizontal' ? 'selected' : ''; ?>>Carousel tiles horizontal</option>
                            <option value="Carousel tiles vertical" <?php echo $datos['transicion'] == 'Carousel tiles vertical' ? 'selected' : ''; ?>>Carousel tiles vertical</option>
                            <option value="Carousel-mirror tiles horizontal" <?php echo $datos['transicion'] == 'Carousel-mirror tiles horizontal' ? 'selected' : ''; ?>>Carousel-mirror tiles horizontal</option>
                            <option value="Carousel-mirror tiles vertical" <?php echo $datos['transicion'] == 'Carousel-mirror tiles vertical' ? 'selected' : ''; ?>>Carousel-mirror tiles vertical</option>
                            <option value="Carousel mirror rows" <?php echo $datos['transicion'] == 'Carousel mirror rows' ? 'selected' : ''; ?>>Carousel mirror rows</option>
                            <option value="Carousel mirror cols" <?php echo $datos['transicion'] == 'Carousel mirror cols' ? 'selected' : ''; ?>>Carousel mirror cols</option>
                            <option value="Sliding rows to right (forward)" <?php echo $datos['transicion'] == 'Sliding rows to right (forward)' ? 'selected' : ''; ?>>Sliding rows to right (forward)</option>
                            <option value="Sliding rows to right (reverse)" <?php echo $datos['transicion'] == 'Sliding rows to right (reverse)' ? 'selected' : ''; ?>>Sliding rows to right (reverse)</option>
                            <option value="Sliding rows to right (random)" <?php echo $datos['transicion'] == 'Sliding rows to right (random)' ? 'selected' : ''; ?>>Sliding rows to right (random)</option>
                            <option value="Sliding rows to left (forward)" <?php echo $datos['transicion'] == 'Sliding rows to left (forward)' ? 'selected' : ''; ?>>Sliding rows to left (forward)</option>
                            <option value="Sliding rows to left (reverse)" <?php echo $datos['transicion'] == 'Sliding rows to left (reverse)' ? 'selected' : ''; ?>>Sliding rows to left (reverse)</option>
                            <option value="Sliding rows to left (random)" <?php echo $datos['transicion'] == 'Sliding rows to left (random)' ? 'selected' : ''; ?>>Sliding rows to left (random)</option>
                            <option value="Sliding rows from top to bottom (forward)" <?php echo $datos['transicion'] == 'Sliding rows from top to bottom (forward)' ? 'selected' : ''; ?>>Sliding rows from top to bottom (forward)</option>
                            <option value="Sliding rows from top to bottom (random)" <?php echo $datos['transicion'] == 'Sliding rows from top to bottom (random)' ? 'selected' : ''; ?>>Sliding rows from top to bottom (random)</option>
                            <option value="Sliding rows from bottom to top (reverse)" <?php echo $datos['transicion'] == 'Sliding rows from bottom to top (reverse)' ? 'selected' : ''; ?>>Sliding rows from bottom to top (reverse)</option>
                            <option value="Sliding rows from bottom to top (random)" <?php echo $datos['transicion'] == 'Sliding rows from bottom to top (random)' ? 'selected' : ''; ?>>Sliding rows from bottom to top (random)</option>
                            <option value="Sliding columns to bottom (forward)" <?php echo $datos['transicion'] == 'Sliding columns to bottom (forward)' ? 'selected' : ''; ?>>Sliding columns to bottom (forward)</option>
                            <option value="Sliding columns to bottom (reverse)" <?php echo $datos['transicion'] == 'Sliding columns to bottom (reverse)' ? 'selected' : ''; ?>>Sliding columns to bottom (reverse)</option>
                            <option value="Sliding columns to bottom (random)" <?php echo $datos['transicion'] == 'Sliding columns to bottom (random)' ? 'selected' : ''; ?>>Sliding columns to bottom (random)</option>
                            <option value="Sliding columns to top (forward)" <?php echo $datos['transicion'] == 'Sliding columns to top (forward)' ? 'selected' : ''; ?>>Sliding columns to top (forward)</option>
                            <option value="Sliding columns to top (reverse)" <?php echo $datos['transicion'] == 'Sliding columns to top (reverse)' ? 'selected' : ''; ?>>Sliding columns to top (reverse)</option>
                            <option value="Sliding columns to top (random)" <?php echo $datos['transicion'] == 'Sliding columns to top (random)' ? 'selected' : ''; ?>>Sliding columns to top (random)</option>
                            <option value="Sliding columns from left to right (forward)" <?php echo $datos['transicion'] == 'Sliding columns from left to right (forward)' ? 'selected' : ''; ?>>Sliding columns from left to right (forward)</option>
                            <option value="Sliding columns from left to right (random)" <?php echo $datos['transicion'] == 'Sliding columns from left to right (random)' ? 'selected' : ''; ?>>Sliding columns from left to right (random)</option>
                            <option value="Sliding columns from right to left (reverse)" <?php echo $datos['transicion'] == 'Sliding columns from right to left (reverse)' ? 'selected' : ''; ?>>Sliding columns from right to left (reverse)</option>
                            <option value="Sliding columns from right to left (random)" <?php echo $datos['transicion'] == 'Sliding columns from right to left (random)' ? 'selected' : ''; ?>>Sliding columns from right to left (random)</option>
                        </select>
                        <a> Si no se desea colocar una animación, dejar en blanco.</a>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Altura:</td>
                    <td class="colblancocen">
                        <input type="text" id="altura" name="altura" style="width: 10%;"
                               value="<?php echo htmlspecialchars($datos['altura']); ?>">
                        <a> 1 Segundo = 1000 Milisegundos.</a>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Orden:</td>
                    <td class="colblancocen">
                        <input type="text" id="orden" name="orden" style="width: 10%;"
                               value="<?php echo htmlspecialchars($datos['orden']); ?>">
                    </td>
                </tr>
            </table>
            <div class="boton-container">
                <button name="aceptar" class="botonesAyC" type="submit">Aceptar</button>
                <button name="Cancelar" class="botonesAyC" type="button" onclick="window.location = 'new_img.php'">Cancelar</button>
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
