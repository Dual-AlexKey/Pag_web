<?php
$sql = "SHOW TABLES LIKE 'menu_%'";
$result = $conn->query($sql);

$menus = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        $table_name = reset($row);
        $menus[] = $table_name;
    }
}
?>