<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $ubicacion = trim($_POST["ubicacion"]);
    
    if (!empty($nombre) && !empty($ubicacion)) {
        $nombre_tabla = "menu_" . preg_replace("/\s+/", "_", strtolower($nombre)) . "_" . strtolower($ubicacion); // Convertir espacios a guiones bajos

        // Crear la tabla con 'id' autoincrementable y 'cod' como VARCHAR (para incluir la letra)
        $sql = "CREATE TABLE IF NOT EXISTS `$nombre_tabla` (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            nombre VARCHAR(255) NOT NULL,
            modulo VARCHAR(255) NOT NULL,
            orden VARCHAR(255),
            nro_item INT,
            visitas INT,
            link VARCHAR(255) NOT NULL,
            Num_nivel VARCHAR(3),
            estilos VARCHAR(255),
            cod VARCHAR(10) UNIQUE
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Menú creado correctamente.";

            // Eliminar trigger previo si existe
            $conn->query("DROP TRIGGER IF EXISTS `before_insert_cod_$nombre_tabla`");

            // Obtener la primera letra del centro (nombre del menú)
            preg_match('/menu_([a-zA-Z0-9]+)_/', $nombre_tabla, $matches);
            $inicial = isset($matches[1]) ? substr($matches[1], 0, 1) : 'X'; // Si no encuentra, usa 'X'

            // Crear trigger sin DELIMITER
            $trigger_sql = "CREATE TRIGGER `before_insert_cod_$nombre_tabla`
            BEFORE INSERT ON `$nombre_tabla`
            FOR EACH ROW 
            BEGIN
                DECLARE max_cod INT;
                SELECT IFNULL(MAX(SUBSTRING(cod, 2)), 0) + 1 INTO max_cod FROM `$nombre_tabla`;

                -- Solo generar un nuevo cod si está vacío o NULL
                IF NEW.cod = '' OR NEW.cod IS NULL THEN
                    SET NEW.cod = CONCAT('$inicial', max_cod);
                END IF;
            END;";

            if ($conn->query($trigger_sql) === TRUE) {
                echo "Trigger creado correctamente.";
            } else {
                echo "Error al crear el trigger: " . $conn->error;
            }

        } else {
            echo "Error al crear el menú: " . $conn->error;
        }
    } else {
        echo "El nombre del menú no puede estar vacío.";
    }
}
header("Location: ../menus.php");
exit;
?>
