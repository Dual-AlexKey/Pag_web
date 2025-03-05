<?php
// Asegúrate de que el nombre del menú se pasa correctamente
if (isset($_POST['menu'])) {
    $menu = $_POST['menu'];

    // Asegúrate de que el nombre de la tabla es seguro y no contiene caracteres maliciosos
    $menu = preg_replace('/[^a-zA-Z0-9_]/', '', $menu); // Esto elimina cualquier carácter no permitido

    // Conectar a la base de datos
    // Asumimos que $conn es la conexión a la base de datos
    include 'conexion.php'; // Aquí se debe incluir tu archivo de conexión a la base de datos

    // Consulta para eliminar la tabla
    $sql = "DROP TABLE `$menu`";

    if ($conn->query($sql) === TRUE) {
        echo "La tabla '$menu' ha sido eliminada correctamente.";
    } else {
        echo "Error al eliminar la tabla: " . $conn->error;
    }

    // Redirigir después de eliminar
    header('Location: ../menus.php'); // Redirige a la página donde están los menús
    exit();
} else {
    echo "No se ha recibido el nombre del menú.";
}
?>
