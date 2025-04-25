<?php
include 'conect/conexion.php';
include('estilo/data.php');
include('estilo/header.php');
include('estilo/menu.php');
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
                    // Omitir el menú 'menu_sinselect'
                    if ($menu === 'menu_sinselect') {
                        continue;
                    }
                    // Quitar "menu_" del inicio
                    $menu_limpio = preg_replace('/^menu_/', '', $menu);
                    
                    // Limpiar sufijos de ubicación
                    $ubicaciones = ['cabecerat', 'pie','cabeceral', 'cabeceram', 'columnai', 'columnad', 'sinselect'];
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

                <!-- Tabla de elementos -->
                <table class="tabla">
                    <tbody id="tabla-<?php echo $menu; ?>">
                        <?php
                        // Solo mostrar donde Num_nivel = 1
                        $sql_items = "SELECT nombre FROM `$menu` WHERE Num_nivel = '1' ORDER BY id ASC"; 
                        $result_items = $conn->query($sql_items);
                        $total_registros = $result_items->num_rows;
                        $contador = 0;

                        if ($total_registros > 0):
                            while ($item = $result_items->fetch_assoc()):
                                $contador++; // ID visual
                        ?>
                            <tr class="fila" id="fila-<?php echo $menu . '-' . $contador; ?>">
                                <td class="nombre">
                                    <?php echo $contador . " - " . htmlspecialchars($item['nombre']); ?>
                                </td>
                                <td class="acciones">
                                    <?php if ($total_registros == 1): ?>
                                        <!-- Solo un registro, sin botones -->

                                    <?php elseif ($contador == 1): ?>
                                        <!-- Primero -->
                                        <button class="botonM" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $contador; ?>, 1)">↓</button>

                                    <?php elseif ($contador == $total_registros): ?>
                                        <!-- Último -->
                                        <button class="botonM" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $contador; ?>, -1)">↑</button>

                                    <?php else: ?>
                                        <!-- Intermedios -->
                                        <button class="botonM" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $contador; ?>, -1)">↑</button>
                                        <button class="botonM" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $contador; ?>, 1)">↓</button>
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

<?php include('estilo/footer.php'); ?>
