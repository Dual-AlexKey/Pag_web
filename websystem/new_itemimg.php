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
                    <td class="colgrishome">Módulo:</td>
                    <td class="colblancocen">
                        <select id="modulo" name="modulo" style="width: 40%;">
                            <option value="Contenidos">Contenidos</option>
                            <option value="Catalogo">Catálogo</option>
                            <option value="Usuarios">Usuarios</option>
                            <option value="Formularios">Formularios</option>
                        </select>
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
