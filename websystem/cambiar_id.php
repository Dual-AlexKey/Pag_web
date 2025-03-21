<?php
include 'conect/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $menu = $conn->real_escape_string($_POST['menu']);
    $id = intval($_POST['id']);
    $cambio = intval($_POST['cambio']);

    // Obtener el número total de registros en la tabla
    $total_registros = $conn->query("SELECT COUNT(*) as total FROM `$menu`")->fetch_assoc()['total'];

    $nuevo_id = $id + $cambio;

    // Verificar si el nuevo ID ya existe
    $check_sql = "SELECT id FROM `$menu` WHERE id = $nuevo_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Intercambiar IDs correctamente
        $conn->query("UPDATE `$menu` SET id = 0 WHERE id = $id");
        $conn->query("UPDATE `$menu` SET id = $id WHERE id = $nuevo_id");
        $conn->query("UPDATE `$menu` SET id = $nuevo_id WHERE id = 0");
    } else {
        // Si el ID no existe, actualizar directamente
        $conn->query("UPDATE `$menu` SET id = $nuevo_id WHERE id = $id");
    }

    // Obtener la tabla actualizada después del cambio
    $sql_items = "SELECT id, nombre FROM `$menu` ORDER BY id ASC"; 
    $result_items = $conn->query($sql_items);
    
    ob_start();
    $primero = true;
    $contador = 0;

    if ($result_items->num_rows > 0):
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
                    
                    <?php elseif ($primero): ?> 
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
<?php
        $primero = false;
        endwhile;
    endif;
    $tabla_actualizada = ob_get_clean();

    echo json_encode([
        "success" => true,
        "menu" => $menu,
        "tabla" => $tabla_actualizada
    ]);
}

$conn->close();
?>
