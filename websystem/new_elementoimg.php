<?php
include 'conect/conexion.php';
include('estilo/data.php');
include('estilo/header.php');
include('estilo/menu.php');

$directorio = "../img/"; // ✅ Directorio correcto basado en la estructura del proyecto
$archivos = is_dir($directorio) ? scandir($directorio) : [];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$nombre_url = isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; // Leer "nombre" desde la URL

// Valores por defecto (formulario vacío)
$datos = [
    'id_img' => '',
    'titulo' => '',
    'tipo' => '',
    'imagen_2' => '',
    'link' => '',
    'PosX' => '',
    'PosY' => '',
    'estilo' => '',
    'orden_2' => '',
    'INIHORI' => '',
    'INIHORO' => '',
    'INIVERI' => '',
    'INIVERO' => '',
    'VELI' => '',
    'VELO' => '',
    'ROTI' => '',
    'ROTO' => '',
    'ROTHORI' => '',
    'ROTHORO' => '',
    'ROTVERI' => '',
    'ROTVERO' => '',
    'AMPHORI' => '',
    'AMPHORO' => '',
    'AMPVERI' => '',
    'AMPVERO' => '',
    'SKEWHORI' => '',
    'SKEWHORO' => '',
    'SKEWVERI' => '',
    'SKEWVERO' => '',
    'TRASNI' => '',
    'TRASNO' => '',
    'RETAR' => '',
    'DURANI' => ''

];

// Si hay un ID válido, buscar el registro en la base de datos
if ($id > 0) {
    $sql = "SELECT * FROM Imagenes2 WHERE id_img = $id LIMIT 1";
    $resultado = mysqli_query($conexion, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $datos = mysqli_fetch_assoc($resultado);
    }
}
?>

<div class="contenido-derecha">
    <a href="new_img.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Nueva Seccion</h2></div>
    <div id="capaformulario">
        <form id="miFormulario" action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="formulario_tipo" value="ElementoImg">
            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombre_url); ?>">
            <div class="columna-formulario">
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Titulo:</td>
                    <td class="colblancocen">
                        <input type="text" id="titulo" name="titulo" style="width: 50%;" 
                        value="<?php echo htmlspecialchars($datos['titulo']); ?>" required>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Tipo:</td>
                    <td class="colblancocen">
                        <select id="tipo" name="tipo" style="width: 30%;" required>
                            <option value="Text" <?php echo $datos['tipo'] == 'Text' ? 'selected' : ''; ?>>Texto</option>
                            <option value="Img" <?php echo $datos['tipo'] == 'Img' ? 'selected' : ''; ?>>Imagenes</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Texto/Imagen:</td>
                    <td class="colblancocen">
                        <input type="text" id="imagen_link2" name="imagen_link2" placeholder="https://ejemplo.com/imagen.jpg" style="width: 50%;">
                        <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_link2')">📂</button>
                        </td>
                </tr>
                <tr>
                    <td class="colgrishome">URL:</td>
                    <td class="colblancocen">
                        <input type="text" id="link" name="link" style="width: 50%;"
                        value="<?php echo htmlspecialchars($datos['link']); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Posicion X:</td>
                    <td class="colblancocen">
                        <input type="text" id="PosX" name="PosX" style="width: 10%;"
                        value="<?php echo htmlspecialchars($datos['PosX']); ?>">
                        <a>Horizontal.</a>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Posicion Y:</td>
                    <td class="colblancocen">
                        <input type="text" id="PosY" name="PosY" style="width: 10%;"
                        value="<?php echo htmlspecialchars($datos['PosY']); ?>">
                        <a>Vertical.</a>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Estilo de texto:</td>
                    <td class="colblancocen">
                        <select id="estilo" name="estilo" style="width: 30%;" required>
                            <option value="Text1" <?php echo $datos['estilo'] == 'Text1' ? 'selected' : ''; ?>>Texto 1</option>
                            <option value="Text2" <?php echo $datos['estilo'] == 'Text2' ? 'selected' : ''; ?>>Texto 2</option>
                            <option value="Text3" <?php echo $datos['estilo'] == 'Text3' ? 'selected' : ''; ?>>Texto 3</option>
                            <option value="Text4" <?php echo $datos['estilo'] == 'Text4' ? 'selected' : ''; ?>>Texto 4</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Orden:</td>
                    <td class="colblancocen">
                        <input type="text" id="orden_2" name="orden_2" style="width: 10%;"
                        value="<?php echo htmlspecialchars($datos['orden_2']); ?>">
                    </td>
                </tr>
            </table>
            </div>
            <div class="columna-formulario">
            <?php
                // Mostrar la tabla `tableborderfull2` solo si en el URL está presente "accion=mostrarTabla"
                if (isset($_GET['accion']) && $_GET['accion'] === 'mostrarTabla') {
                ?>
            <table class="tableborderfull2">
                <tr>
                    <th colspan="2">AL INGRESAR</th>
                    <th colspan="2">AL SALIR</th>
                </tr>
                <tr>
                    <td>Inicio Horizontal</td>
                    <td class="colblancocen">
                        <input type="text" id="INIHORI" name="INIHORI" style="width: 60%;">
                        <a>[offsetX in]</a>
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="INIHORO" name="INIHORO" style="width: 60%;">
                        <a>[offsetX out]</a>
                    </td>
                    <td>left, right or Nro</td>
                </tr>
                <tr>
                    <td>Inicio Vertical</td>
                    <td class="colblancocen">
                        <input type="text" id="INIVERI" name="INIVERI" style="width: 60%;">
                        <a>[offsetY in]</a>
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="INIVERO" name="INIVERO" style="width: 60%;">
                        <a>[offsetY out]</a>
                    </td>
                    <td>top, bottom or Nro.</td>
                </tr>
                <tr>
                    <td>Velocidad</td>
                    <td class="colblancocen">
                        <input type="text" id="VELI" name="VELI" style="width: 60%;">
                        <a>[duration in]</a>
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="VELO" name="VELO" style="width: 60%;">
                        <a>[duration out]</a>
                    </td>
                    <td>1 Segundo = 1000 Milisegundos</td>
                </tr>
                <tr>
                    <td>Rotacion</td>
                    <td class="colblancocen">
                        <input type="text" id="ROTI" name="ROTI" style="width: 60%;">
                        <a>[rotate in]</a>
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="ROTO" name="ROTO" style="width: 60%;">
                        <a>[rotate out]</a>
                    </td>
                    <td>Angulos(180, 360, 90)</td>
                </tr>
                <tr>
                    <td>Rotar Horizontal</td>
                    <td class="colblancocen">
                        <input type="text" id="ROTHORI" name="ROTHORI" style="width: 60%;">
                        <a>[rotateX in]</a>
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="ROTHORO" name="ROTHORO" style="width: 60%;">
                        <a>[rotateX out]</a>
                    </td>
                    <td>Angulos(180, 360, 90)</td>
                </tr>
                <tr>
                    <td>Rotar Vertical</td>
                    <td class="colblancocen">
                        <input type="text" id="ROTVERI" name="ROTVERI" style="width: 60%;">
                        <a>[rotateY in]</a>
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="ROTVERO" name="ROTVERO" style="width: 60%;">
                        <a>[rotateY out]</a>
                    </td>
                    <td>Angulos(180, 360, 90)</td>
                </tr>
                <tr>
                    <td>Ampliar Horizontal [scalexin]</td>
                    <td class="colblancocen">
                        <input type="text" id="AMPHORI" name="AMPHORI" style="width: 60%;">
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="AMPHORO" name="AMPHORO" style="width: 60%;">
                    </td>
                    <td>Angulos(180, 360, 90)</td>
                </tr>
                <tr>
                    <td>Ampliar Vertical [scalexin]</td>
                    <td class="colblancocen">
                        <input type="text" id="AMPVERI" name="AMPVERI" style="width: 60%;">
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="AMPVERO" name="AMPVERO" style="width: 60%;">
                    </td>
                    <td>Angulos(180, 360, 90)</td>
                </tr>
                <tr>
                    <td>Skew Horizontal [skewxin]</td>
                    <td class="colblancocen">
                        <input type="text" id="SKEWHORI" name="SKEWHORI" style="width: 60%;">
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="SKEWHORO" name="SKEWHORO" style="width: 60%;">
                    </td>
                    <td>Angulos(180, 360, 90)</td>
                </tr>
                <tr>
                    <td>Skew Vertical [skewyin]</td>
                    <td class="colblancocen">
                        <input type="text" id="SKEWVERI" name="SKEWVERI" style="width: 60%;">
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="SKEWVERO" name="SKEWVERO" style="width: 60%;">
                    </td>
                    <td>Angulos(180, 360, 90)</td>
                </tr>
                <tr>
                    <td>Transformacion [transformoriginin]</td>
                    <td class="colblancocen">
                        <input type="text" id="TRASNI" name="TRASNI" style="width: 60%;">
                    </td>
                    <td class="colblancocen">
                        <input type="text" id="TRASNO" name="TRASNO" style="width: 60%;">
                    </td>
                    <td>Angulos(180, 360, 90)</td>
                </tr>
                <tr>
                    <td>Retardo [delayin]</td>
                    <td class="colblancocen" colspan="3">
                        <input type="text" id="RETAR" name="RETAR" style="width: 10%;">
                        <a>1 Segundo = 1000 Milisegundos.</a>
                    </td>
                </tr>
                <tr>
                    <td>Duracion Animacion [showuntil]</td>
                    <td class="colblancocen"colspan="3">
                        <input type="text" id="DURANI" name="DURANI" style="width: 10%;">
                        <a>1 Segundo = 1000 Milisegundos.</a>
                    </td>
                </tr>
            </table>
            <?php
                }
                ?>
            </div>

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
// Incluir el footer.php
include('estilo/footer.php');
?>