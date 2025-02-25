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

?>

<div class="contenido-derecha">
        <a href="panel.php"><button class="boton-cerrar">X</button></a>
        <div class="bloque-verde"><h2>Nueva Seccion</h2></div>
        <div id="capaformulario">
            <form action="conect/sendccion.php" method="post">
             <input type="hidden" id="idcontrol">
                <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Nombre:</td>
                    <td class="colblancocen">
                        <input type="text" id="nombre" name="nombre" required oninput="actualizarURL()">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">URL:</td>
                    <td class="colblancocen">
                        <input type="text" id="link" name="link" required readonly>
                    </td>
                </tr>
                    <tr>
                        <td class="colgrishome">Módulo:</td>
                        <td class="colblancocen">
                            <select id="modulo" name="modulo" required onchange="cambiarEstilos()">
                                <option value="Contenidos">Contenidos</option>
                                <option value="Catalogo">Catalogo</option>
                                <option value="Usuarios">Usuarios</option>
                                <option value="Formularios">Formularios</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Estilos:</td>
                        <td class="colgrishome">
                            <div style="display: flex; gap: 20px; align-items: right;" id="estilos">
                                <!-- Las opciones de estilo se agregarán aquí dinámicamente -->
                            </div>
                        </td>
                    </tr>
                    <tr>
                    <td class="colgrishome">Publicar en Menú:</td>
                    <td class="colgrishome">
                        <?php if (!empty($menus)): ?>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php foreach ($menus as $index => $menu): ?>
                                    <?php
                                        // Quitar "menu_" del inicio
                                        $menu_limpio = preg_replace('/^menu_/', '', $menu);
                                        
                                        // Lista de ubicaciones dinámicas
                                        $ubicaciones = ['cabecera', 'pie', 'lateral', 'footer']; // Agrega más si es necesario

                                        // Eliminar cualquier sufijo que coincida con una ubicación
                                        foreach ($ubicaciones as $ubicacion) {
                                            $menu_limpio = preg_replace('/_' . preg_quote($ubicacion, '/') . '$/', '', $menu_limpio);
                                        }
                                    ?>
                                    <label style="display: flex; align-items: center;">
                                        <input type="checkbox" id="publicar_<?php echo $index; ?>" name="publicar[]" value="<?php echo $menu; ?>">
                                        <span style="margin-left: 5px;"><?php echo htmlspecialchars($menu_limpio); ?></span>
                                    </label>
                                    <?php if (($index + 1) % 3 == 0): ?>
                                        <br>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No hay menús disponibles.</p>
                        <?php endif; ?>
                    </td>
                </tr>
                    <tr>
                        <td align="center">
                            <button name="aceptar"  class="boton" onclick="window.location = 'secciones.php'" type="submit">Aceptar</button>
                        </td>
                        <td align="center">
                            <button name="Cancelar"  class="boton" onclick="window.location = 'secciones.php'">Cancelar</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>