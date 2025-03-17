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
                    // Limpiar el nombre del menú
                    $menu_limpio = preg_replace('/^menu_/', '', $menu);
                    $ubicaciones = ['cabecerat', 'pie','cabeceral', 'cabeceram', 'columnai', 'columnad'];
                    foreach ($ubicaciones as $ubicacion) {
                        $menu_limpio = preg_replace('/_' . preg_quote($ubicacion, '/') . '$/', '', $menu_limpio);
                    }
                ?>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h4><?php echo htmlspecialchars($menu_limpio); ?></h4>
                    <form action="conect/eliminar_tabla.php" method="post" style="display:inline;">
                        <input type="hidden" name="menu" value="<?php echo htmlspecialchars($menu); ?>">
                        <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar esta tabla?');" style="background: none; border: none; cursor: pointer;">
                            <img src="https://i.ibb.co/LdTnB39W/wp-borrar.png" alt="Eliminar" style="width: 24px; height: 24px;">
                        </button>
                    </form>
                </div>
                
                <ul style="list-style: none; padding: 0;">
                    <?php
                    $sql_items = "SELECT id, nombre FROM `$menu` ORDER BY id ASC"; 
                    $result_items = $conn->query($sql_items);
                    
                    if ($result_items && $result_items->num_rows > 0):
                        $contador = 0;
                        while ($item = $result_items->fetch_assoc()):
                            $contador++;
                    ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 5px 0;">
    <span>ID: <span id="id-<?php echo $menu . '-' . $item['id']; ?>"><?php echo $item['id']; ?></span> - <?php echo htmlspecialchars($item['nombre']); ?></span>
    
    <div style="float: right;">
        <?php if ($contador > 1): ?>
            <button class="boton-mover" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $item['id']; ?>, -1)">↑</button>
        <?php endif; ?>
        <button class="boton-mover" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $item['id']; ?>, 1)">↓</button>
    </div>
</li>

                    <?php endwhile; endif; ?>
                </ul>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
// Incluir el footer.php
include('estilo/footer.php');
?>