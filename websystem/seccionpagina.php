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
        <!-- Contenedor del editor -->
        <div id="editor-container">
            <!-- Barra de herramientas con opciones -->
            <div id="toolbar">
                <button type="button" onclick="document.execCommand('bold', false, '')"><b>B</b></button>
                <button type="button" onclick="document.execCommand('italic', false, '')"><i>I</i></button>
                <button type="button" onclick="document.execCommand('underline', false, '')"><u>U</u></button>
                <button type="button" onclick="document.execCommand('justifyLeft', false, '')">Izq</button>
                <button type="button" onclick="document.execCommand('justifyCenter', false, '')">Centro</button>
                <button type="button" onclick="document.execCommand('justifyRight', false, '')">Der</button>
                <select onchange="document.execCommand('fontSize', false, this.value)" style="font-size: 12px;">
                    <option value="3">Normal</option>
                    <option value="4">Grande</option>
                    <option value="5">Muy Grande</option>
                </select>
                <select onchange="document.execCommand('fontName', false, this.value)" style="font-size: 12px;">
                    <option value="Arial">Arial</option>
                    <option value="Courier New">Courier</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Times New Roman">Times</option>
                </select>
                <input type="color" id="color-text" title="Color de texto" onchange="document.execCommand('foreColor', false, this.value)">
                <input type="color" id="color-bg" title="Color de fondo" onchange="document.execCommand('backColor', false, this.value)">
                <button type="button" onclick="openCodeModal()">Bloque de Código</button>
                <button type="button" onclick="insertLink()">Enlace</button>
                <button type="button" onclick="insertVideo()">Video</button>
            </div>
            <!-- Área de edición -->
            <div id="editor" contenteditable="true" style="border: 1px solid #ccc; padding: 10px; min-height: 150px; margin-top: 5px;">
                <?= htmlspecialchars($datos['contenido']) ?>
            </div>
        </div>
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
                        <input type="text" id="nombreS" name="nombreS"  
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

<!-- Modal para bloque de código -->
<div id="codeModal" style="display: none;">
    <div style="padding: 20px; background: #f4f4f4; border-radius: 5px;">
        <h3>Insertar Bloque de Código</h3>
        <textarea id="code-input" rows="5" style="width: 100%;"></textarea>
        <br><br>
        <button type="button" onclick="insertCode()">Insertar</button>
        <button type="button" onclick="closeCodeModal()">Cancelar</button>
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
<script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
