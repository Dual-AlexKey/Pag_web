<?php
//inclusion de informacion
include('estilo/data.php');

// Incluir el header.php    
include('estilo/header.php');

// Incluir el menu.php

include('estilo/menu.php');

// Inicializar el array con valores vacíos por defecto
$empresa = [
    'url_pagina'      => '',
    'nombre'          => '',
    'idioma'          => '',
    'logo'            => '',
    'favicon'         => '',
    'seo_titulo'      => '',
    'seo_descripcion' => '',
    'seo_metatags'    => '',
    'pie_pagina'      => '',
    'imgcabe'      => '',
    'cabfondo'      => '',
    'piefondo'      => '',
    'empresa'         => '',
    'ruc'             => '',
    'descripcion'     => '',
    'pais'            => '',
    'dpto'            => '',
    'city'            => '',
    'direccion_principal' => '',
    'email_contactos' => '',
    'email_ventas'    => '',
    'telefono_fijo'   => '',
    'telefono_movil'  => '',
    'moneda'          => '',
    'precios'         => '',
    'carrito_compras' => '',
    'zona_usuarios'   => '',
    'terminos_condiciones' => ''
];

// Consultar si hay datos en la tabla empresa
$sql = "SELECT * FROM empresa LIMIT 1";
$resultado = $conexion->query($sql);

// Si hay datos, sobrescribimos el array con la información de la base de datos
if ($resultado && $resultado->num_rows > 0) {
    $empresa = $resultado->fetch_assoc();
}
$directorio = "../img/"; // ✅ Directorio correcto basado en la estructura del proyecto
$archivos = is_dir($directorio) ? scandir($directorio) : [];
?>

<div class="contenido-derecha">
    <a href="panel.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Editar configuración</h2></div>
    <div class="bloque-gris"><h3>Configuración Web</h3></div>

    <div id="capaformulario">
        <form id="miFormulario" action="conect/guardar_tablero.php" method="post">
            <input type="hidden" name="formulario_tipo" value="Webconfig">
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">URL Página</td>
                    <td class="colblancocen">
                        <?php 
                            // Si no hay una URL en la base de datos, usa el nombre del servidor
                            $url_pagina = !empty($empresa['url_pagina']) ? $empresa['url_pagina'] : $_SERVER['SERVER_NAME'];
                        ?>
                        <input name="url_pagina" type="hidden" value="<?= htmlspecialchars($url_pagina) ?>" readonly>
                        <span class="negrita"><?= htmlspecialchars($url_pagina) ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Nombre</td>
                    <td class="colblancocen">
                        <input name="nombre" type="text" value="<?= htmlspecialchars($empresa['nombre']) ?>" style="width: 50%;">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Idioma</td>
                    <td class="colblancocen">
                        <select name="idioma" style="width: 20%;">
                            <option value="0" <?= ($empresa['idioma'] == '0') ? 'selected' : '' ?>>English</option>
                            <option value="1" <?= ($empresa['idioma'] == '1') ? 'selected' : '' ?>>Español</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Logo</td>
                    <td class="colblancocen">
                        <input type="text" id="imagen_link" name="logo" value="<?= htmlspecialchars($empresa['logo']) ?>" style="width: 50%;">
                        <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_link')">📂</button>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Favicon</td>
                    <td class="colblancocen">
                        <input type="text" id="imagen_link2" name="favicon" value="<?= htmlspecialchars($empresa['favicon']) ?>" style="width: 50%;">
                        <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_link2')">📂</button>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">SEO Título</td>
                    <td class="colblancocen"><input name="seo_titulo" type="text" value="<?= htmlspecialchars($empresa['seo_titulo']) ?>" style="width: 50%;"></td>
                </tr>
                <tr>
                    <td class="colgrishome">SEO Descripción</td>
                    <td class="colblancocen"><textarea name="seo_descripcion" ><?= htmlspecialchars($empresa['seo_descripcion']) ?></textarea></td>
                </tr>
                <tr>
                    <td class="colgrishome">SEO Metatags</td>
                    <td class="colblancocen"><textarea name="seo_metatags"><?= htmlspecialchars($empresa['seo_metatags']) ?></textarea></td>
                </tr>
                <tr>
                    <td class="colgrishome">Cabecera</td>
                    <td class="colblancocen">
                        <!-- Input para el logo -->
                        <input type="text" id="imagen_link3" name="imgcabe" value="<?= htmlspecialchars($empresa['imgcabe']) ?>" style="width: 50%;">
                        <button type="button" class="boton-explorador" onclick="mostrarExplorador('imagen_link3')">📂</button>
                        <br><br>
                        <!-- Selector de color del fondo de la cabecera -->
                        <label for="cabfondo" style="margin-right: 10px;">Fondo de Cabecera:</label>
                        <input type="color" id="cabfondo" name="cabfondo" value="<?= htmlspecialchars($empresa['cabfondo']) ?>" >
                    </td>               
                </tr>
                <tr>
                    <td class="colgrishome">Pie de Página</td>
                    <td class="colblancocen">
                        <!-- Input para el pie de página -->
                        <textarea name="pie_pagina" style="width: 70%; height: 50px;"><?= htmlspecialchars($empresa['pie_pagina']) ?></textarea>
                        <br><br>

                        <!-- Selector de color del fondo del pie -->
                        <label for="piefondo" style="margin-right: 10px;">Fondo del Pie:</label>
                        <input type="color" id="piefondo" name="piefondo" value="<?= htmlspecialchars($empresa['piefondo']) ?>">
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="titlehome">Datos Empresariales</td>
                </tr>
                <tr>
                    <td class="colgrishome">Empresa</td>
                    <td class="colblancocen"><input type="text" name="empresa" value="<?= htmlspecialchars($empresa['empresa']) ?>" style="width: 50%;"></td>
                </tr>
                <tr>
                    <td class="colgrishome">RUC</td>
                    <td class="colblancocen"><input name="ruc" type="text" value="<?= htmlspecialchars($empresa['ruc']) ?>" style="width: 50%;"></td>
                </tr>
                <tr>
                    <td class="colgrishome">Descripción</td>
                    <td class="colblancocen"><textarea name="descripcion"><?= htmlspecialchars($empresa['descripcion']) ?></textarea></td>
                </tr>
                <tr>
    <td class="colgrishome">País</td>
    <td class="colblancocen">
        <select name="pais" id="pais" style="width: 30%;">
            <option value="">Seleccione un país</option>
            <option value="peru" <?= ($empresa['pais'] == 'peru') ? 'selected' : '' ?>>Perú</option>
        </select>
    </td>
</tr>
<tr>
    <td class="colgrishome">Región / Departamento</td>
    <td class="colblancocen">
        <select name="dpto" id="dpto" style="width: 30%;">
            <option value="">Seleccione un departamento</option>
            <?php if (!empty($empresa['dpto'])): ?>
                <option value="<?= htmlspecialchars($empresa['dpto']) ?>" selected><?= htmlspecialchars($empresa['dpto']) ?></option>
            <?php endif; ?>
        </select>
    </td>
</tr>
<tr>
    <td class="colgrishome">Ciudad / Provincia</td>
    <td class="colblancocen">
        <select name="city" id="city" style="width: 30%;">
            <option value="">Seleccione una provincia</option>
            <?php if (!empty($empresa['city'])): ?>
                <option value="<?= htmlspecialchars($empresa['city']) ?>" selected><?= htmlspecialchars($empresa['city']) ?></option>
            <?php endif; ?>
        </select>
    </td>
</tr>

                <tr>
                    <td class="colgrishome">Dirección Principal:</td>
                    <td class="colblancocen" >
                        <input name="direccion_principal" type="text" value="<?= htmlspecialchars($empresa['direccion_principal']) ?>" style="width: 50%;">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Email Contactos:</td>
                    <td class="colblancocen">
                        <input name="email_contactos" type="text" value="<?= htmlspecialchars($empresa['email_contactos']) ?>" style="width: 50%;">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Email Ventas:</td>
                    <td class="colblancocen">
                        <input name="email_ventas" type="text" value="<?= htmlspecialchars($empresa['email_ventas']) ?>" style="width: 50%;">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Teléfono Fijo:</td>
                    <td class="colblancocen">
                        <input name="telefono_fijo" type="text" value="<?= htmlspecialchars($empresa['telefono_fijo']) ?>" style="width: 50%;">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Teléfono Móvil:</td>
                    <td class="colblancocen" >
                        <input name="telefono_movil" type="text" value="<?= htmlspecialchars($empresa['telefono_movil']) ?>" style="width: 50%;">
                    </td>
                </tr>
                    <!-- Servicios -->
                <tr>
                    <td colspan="2" class="titlehome">Servicios</td>
                </tr>
                <tr>
                    <td class="colgrishome">Moneda</td>
                    <td class="colblancocen">
                        <select name="moneda" style="width: 50%;">
                            <option value="0" <?= ($empresa['moneda'] == '0') ? 'selected' : '' ?>>Dólares</option>
                            <option value="1" <?= ($empresa['moneda'] == '1') ? 'selected' : '' ?>>Soles</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome" valign="top">Precios</td>
                    <td class="colblancocen">
                        <select name="precios" style="width: 50%;">
                            <option value="0" <?= ($empresa['precios'] == '0') ? 'selected' : '' ?>>No Activo</option>
                            <option value="1" <?= ($empresa['precios'] == '1') ? 'selected' : '' ?>>Activo</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome" valign="top">Carrito de Compras</td>
                    <td class="colblancocen">
                        <select name="carrito_compras" style="width: 50%;">
                            <option value="0" <?= ($empresa['carrito_compras'] == '0') ? 'selected' : '' ?>>No Activo</option>
                            <option value="1" <?= ($empresa['carrito_compras'] == '1') ? 'selected' : '' ?>>Activo</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome" valign="top">Zona de usuarios</td>
                    <td class="colblancocen">
                        <select name="zona_usuarios" style="width: 50%;">
                            <option value="0" <?= ($empresa['zona_usuarios'] == '0') ? 'selected' : '' ?>>No Activo</option>
                            <option value="1" <?= ($empresa['zona_usuarios'] == '1') ? 'selected' : '' ?>>Activo</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome" valign="top">Términos y condiciones</td>
                    <td class="colblancocen">
                        <textarea name="terminos_condiciones" rows="10" cols="65" ><?= htmlspecialchars($empresa['terminos_condiciones']) ?></textarea>
                    </td>
                </tr>
            </table>
            <div class="boton-container">
                <button name="aceptar" class="botonesAyC" type="submit">Aceptar</button>
                <button name="Cancelar" class="botonesAyC" type="button" onclick="window.location = 'panel.php'">Cancelar</button>
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
