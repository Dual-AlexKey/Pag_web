<?php
include 'conect/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $menu = $conn->real_escape_string($_POST['menu']);
    $id = intval($_POST['id']);
    $cambio = intval($_POST['cambio']);

    // Verificar si la tabla existe
    $check_table_sql = "SHOW TABLES LIKE '$menu'";
    $check_table_result = $conn->query($check_table_sql);
    if ($check_table_result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "La tabla no existe"]);
        exit;
    }

    // Obtener el ID actual
    $sql = "SELECT id FROM `$menu` WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nuevo_id = $row['id'] + $cambio;

        if ($nuevo_id < 1) {
            echo json_encode(["success" => false, "message" => "El ID no puede ser menor que 1"]);
            exit;
        }

        // Verificar si el nuevo ID ya existe
        $check_id_sql = "SELECT id FROM `$menu` WHERE id = $nuevo_id";
        $check_id_result = $conn->query($check_id_sql);

        if ($check_id_result->num_rows > 0) {
            // Intercambiar IDs si ya existe
            $conn->query("UPDATE `$menu` SET id = 0 WHERE id = $id"); // Temporal
            $conn->query("UPDATE `$menu` SET id = $id WHERE id = $nuevo_id");
            $conn->query("UPDATE `$menu` SET id = $nuevo_id WHERE id = 0"); // Restaurar
        } else {
            // Si no existe, actualizar normalmente
            $conn->query("UPDATE `$menu` SET id = $nuevo_id WHERE id = $id");
        }

        echo json_encode(["success" => true, "nuevo_id" => $nuevo_id, "menu" => $menu]);
    } else {
        echo json_encode(["success" => false, "message" => "Elemento no encontrado"]);
    }
}

?>
