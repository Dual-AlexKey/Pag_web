<?php
include 'conect/conexion.php';
//inclusion de informacion
include('estilo/data.php');
// Incluir el header.php
include('estilo/header.php');
// Incluir el menu.php
include('estilo/menu.php');
?>
<div class="contenido-derecha">
    <a href="tablero.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Contenido</h2></div>
    
    <div id="capaformulario">
        <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="formulario_tipo" value="SeccionPag"> 
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
        
                    </tr>

                </table>
            </div>
            <div class="bloque-verde"><h2>SEO (Posicionamiento Web)</h2></div>
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
                    <td class="colgrishome">Descripcion:</td>
                    <td class="colblancocen">
                        <textarea id="codigo" name="codigo" rows="10" cols="65"><?= isset($datos['codigo']) ? htmlspecialchars(trim($datos['codigo'])) : '' ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Metatags:</td>
                    <td class="colblancocen">
                        <textarea id="codigo" name="codigo" rows="10" cols="65"><?= isset($datos['codigo']) ? htmlspecialchars(trim($datos['codigo'])) : '' ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Imagen Referencia:</td>
                    <td class="colblancocen">
                        <input type="text" id="imagen_link" name="imagen_link" 
                            placeholder="https://ejemplo.com/imagen.jpg" style="width: 30%;"
                            value="<?= isset($datos['imagen']) ? htmlspecialchars($datos['imagen']) : '' ?>">
                            <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_link')">📂</button>
                            </td>
                </tr>
                <tr>
                    <td class="colgrishome">Imagen Social:</td>
                    <td class="colblancocen">
                        <input type="text" id="imagen_link" name="imagen_link" 
                            placeholder="https://ejemplo.com/imagen.jpg" style="width: 30%;"
                            value="<?= isset($datos['imagen']) ? htmlspecialchars($datos['imagen']) : '' ?>">
                            <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_link')">📂</button>
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
// Incluir el footer.php
include('estilo/footer.php');
?>
