<?php
include 'conect/conexion.php';

//inclusion de informacion
include('estilo/data.php');

// Incluir el header.php
include('estilo/header.php');

// Incluir el menu.php

include('estilo/menu.php');
// Consultar las tablas que comienzan con 'menu_'
include('estilo/tabla_menu.php');

?>

<div class="contenido-derecha">
    <a href="panel.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Menus</h2></div>
    
    <form action="conect/crear_menu.php" method="post">
        <input type="hidden" id="idcontrol">
        <table class="tableborderfull">
            <tr>
                <td class="colgrishome">Nombre: <input type="text" name="nombre" required></td>
                <td class="colgrishome">Ubicación: 
                    <select name="ubicacion" required>
                        <option value="cabeceraT">Cabecera Top</option>
                        <option value="cabeceraL">Cabecera Logo</option>
                        <option value="cabeceraM">Cabecera Menu</option>
                        <option value="columnaI">Columna Izquierda</option>
                        <option value="columnaD">Columna Derecha</option>
                        <option value="pie">Pie de página</option>
                    </select>
                </td>
            </tr>
        </table>
        <div class="boton-container">
        <button type="submit" class="botonesAyC" onclick="window.location = 'menus.php'" style="width: 30%; margin-bottom: 10px;">Crear Menu</button>
        </div>
    </form>

    <!-- Mostrar los menús creados -->
    <div class="bloque-gris">
        <h3>Menús creados</h3>
        <?php if (!empty($menus)): ?>
            <?php foreach ($menus as $menu): ?>
                <?php
                    // Quitar "menu_" del inicio
                    $menu_limpio = preg_replace('/^menu_/', '', $menu);
                    
                    // Obtener todas las ubicaciones posibles desde el select
                    $ubicaciones = ['cabecerat', 'pie','cabeceral', 'cabeceram', 'columnai', 'columnad']; // Agrega más si es necesario

                    // Eliminar cualquier sufijo que coincida con una ubicación
                    foreach ($ubicaciones as $ubicacion) {
                        $menu_limpio = preg_replace('/_' . preg_quote($ubicacion, '/') . '$/', '', $menu_limpio);
                    }
                ?>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h4><?php echo htmlspecialchars($menu_limpio); ?></h4>
                    <!-- Formulario para eliminar la tabla, alineado al extremo derecho -->
                    <form action="conect/eliminar_tabla.php" method="post" style="display:inline;">
                        <input type="hidden" name="menu" value="<?php echo htmlspecialchars($menu); ?>">
                        <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar esta tabla?');">Eliminar Tabla</button>
                    </form>
                </div>
                
                <ul>
                    <?php
                    $sql_items = "SELECT nombre FROM `$menu`"; 
                    $result_items = $conn->query($sql_items);
                    
                    if ($result_items && $result_items->num_rows > 0):
                        while ($item = $result_items->fetch_assoc()):
                    ?>
                        <li><?php echo htmlspecialchars($item['nombre']); ?></li>
                    <?php
                        endwhile;
                    endif;
                    ?>
                </ul>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
// Incluir el footer.php
include('estilo/footer.php');
?>