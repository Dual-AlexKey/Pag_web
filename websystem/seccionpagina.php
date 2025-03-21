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
    'titulo' => '',
    'contenido' => '',
    'tituloS' => '',
    'descripcion' => '',
    'metatags' => '',
    'imagen_referencia' => '',
    'imagen_social' => ''
];

// 🔹 Si hay un "cod", buscar los datos en la base de datos
if (!empty($cod)) {
    $sql = "SELECT cod, titulo, contenido, tituloS, descripcion, metatags, imagen_referencia, imagen_social 
            FROM paginas WHERE cod = ?";
    
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
    <div class="bloque-verde"><h2>Contenido</h2></div>
    
    <div id="capaformulario">
    <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="formulario_tipo" value="SeccionPag"> 
        <input type="hidden" name="cod" value="<?= htmlspecialchars($datos['cod']) ?>">

        <div class="columna-formulario">
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Título:</td>
                    <td class="colblancocen">
                        <input type="text" id="nombreT" name="nombreT" required 
                            style="width: 50%;" 
                            value="<?= htmlspecialchars($datos['titulo']) ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Contenido:</td>
                    <td class="colblancocen">
                        <textarea id="editor" name="contenido"><?= htmlspecialchars($datos['contenido']) ?></textarea>
                        <br>
                        <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_linkED')">📂 Insertar Imagen</button>
                        <input type="hidden" id="imagen_linkED"> 
                    </td>
                </tr>
            </table>
        </div>

        <div class="bloque-verde"><h2>SEO (Posicionamiento Web)</h2></div>
        <div class="columna-formulario">
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Título Secundario:</td>
                    <td class="colblancocen">
                        <input type="text" id="nombreS" name="nombreS" required 
                            style="width: 50%;" 
                            value="<?= htmlspecialchars($datos['tituloS']) ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Descripción:</td>
                    <td class="colblancocen">
                        <textarea id="descrip" name="descrip" rows="10" cols="65"><?= htmlspecialchars($datos['descripcion']) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Metatags:</td>
                    <td class="colblancocen">
                        <textarea id="meta" name="meta" rows="10" cols="65"><?= htmlspecialchars($datos['metatags']) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Imagen Referencia:</td>
                    <td class="colblancocen">
                        <input type="text" id="imagen_link2" name="imagen_link2" 
                            style="width: 30%;"
                            value="<?= htmlspecialchars($datos['imagen_referencia']) ?>">
                        <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_link2')">📂</button>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Imagen Social:</td>
                    <td class="colblancocen">
                        <input type="text" id="imagen_link3" name="imagen_link3" 
                            style="width: 30%;"
                            value="<?= htmlspecialchars($datos['imagen_social']) ?>">
                        <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_link3')">📂</button>
                    </td>
                </tr>
            </table>
        </div>

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
<script src="https://cdn.tiny.cloud/1/cdjub9u5verxs814ltydoojynkv4x5802dnix0botlvmns9g/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
