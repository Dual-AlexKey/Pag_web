<?php
include 'conect/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $menu = $conn->real_escape_string($_POST['menu']);
    $id = intval($_POST['id']);
    $cambio = intval($_POST['cambio']);

    // Calcular el nuevo ID
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

    // Reordenar los IDs para que sean secuenciales
    $conn->query("SET @rownum := 0");
    $conn->query("UPDATE `$menu` SET id = (@rownum := @rownum + 1) ORDER BY id ASC");

    // Obtener la tabla actualizada después del cambio
    $sql_items = "SELECT id, nombre FROM `$menu` ORDER BY id ASC"; 
    $result_items = $conn->query($sql_items);
    
    ob_start();
    $contador = 1; // ID visual siempre comienza en 1

    if ($result_items->num_rows > 0):
        while ($item = $result_items->fetch_assoc()):
?>
            <tr class="fila" id="fila-<?php echo $menu . '-' . $item['id']; ?>">
                <td class="nombre">
                    <?php echo $contador . " - " . htmlspecialchars($item['nombre']); ?>
                </td>
                <td class="acciones">
                    <?php if ($contador == 1): ?>
                        <!-- Si es el primer registro, solo mostrar flecha abajo -->
                        <button class="botonM" onclick="cambiarID('<?php echo $menu; ?>', <?php echo $item['id']; ?>, 1)">↓</button>
                    
                    <?php elseif ($contador == $result_items->num_rows): ?>
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
        $contador++; // Incrementar el ID visual
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