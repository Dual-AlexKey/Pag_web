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


// Obtener todas las tablas que empiezan con "menu_"
$menus = [];
$result_tables = $conn->query("SHOW TABLES LIKE 'menu_%'");
while ($row = $result_tables->fetch_array()) {
    $menus[] = $row[0];
}


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
        <?php if (!empty($menus)): ?>
            <?php foreach ($menus as $menu): ?>
                <?php
                    // Quitar "menu_" del inicio
                    $menu_limpio = preg_replace('/^menu_/', '', $menu);
                    
                    // Ubicaciones a eliminar del nombre
                    $ubicaciones = ['cabecerat', 'pie','cabeceral', 'cabeceram', 'columnai', 'columnad'];

                    foreach ($ubicaciones as $ubicacion) {
                        $menu_limpio = preg_replace('/_' . preg_quote($ubicacion, '/') . '$/', '', $menu_limpio);
                    }
                ?>
                <div class="menu-header">
                    <h4><?php echo htmlspecialchars($menu_limpio); ?></h4>
                    <form action="conect/eliminar_tabla.php" method="post" class="form-eliminar">
                        <input type="hidden" name="menu" value="<?php echo htmlspecialchars($menu); ?>">
                        <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar esta tabla?');" class="btn-eliminar">
                            <img src="https://i.ibb.co/LdTnB39W/wp-borrar.png" alt="Eliminar">
                        </button>
                    </form>
                </div>

                <!-- Tabla de elementos SIN encabezado -->
                <table class="tabla">
                    <tbody id="tabla-<?php echo $menu; ?>">
                        <?php
                        $sql_items = "SELECT id, nombre FROM `$menu` ORDER BY id ASC"; 
                        $result_items = $conn->query($sql_items);
                        $total_registros = $result_items->num_rows;
                        $contador = 0;

                        if ($total_registros > 0):
                            while ($item = $result_items->fetch_assoc()):
                                $contador++;
                        ?>
                            <tr class="fila" id="fila-<?php echo $menu . '-' . $item['id']; ?>">
                                <td class="nombre">
                                    <?php echo $item['id'] . " - " . htmlspecialchars($item['nombre']); ?>
                                </td>
                                <td class="acciones">
                                    <?php if ($total_registros == 1): ?>
                                        <!-- Si solo hay un registro, no mostrar botones -->

                                    <?php elseif ($contador == 1): ?> 
                                        <!-- Si es el primer registro, solo mostrar flecha abajo -->
                                        <button class="botonM" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $item['id']; ?>, 1)">↓</button>

                                    <?php elseif ($contador == $total_registros): ?>
                                        <!-- Si es el último registro, solo mostrar flecha arriba -->
                                        <button class="botonM" onclick="cambiarID('<?php echo $menu; ?>',<?php echo $item['id']; ?>, -1)">↑</button>

                                    <?php else: ?>
                                        <!-- Si es cualquier otro, mostrar ambos botones -->
                                        <button class="botonM" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $item['id']; ?>, -1)">↑</button>
                                        <button class="botonM" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $item['id']; ?>, 1)">↓</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
// Incluir el footer.php
include('estilo/footer.php');
?>