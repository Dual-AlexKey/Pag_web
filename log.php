<?php
include __DIR__ . '/websystem/conect/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];
    $hash = password_hash($password, PASSWORD_DEFAULT); // Encripta la contraseña

    $stmt = $conn->prepare("INSERT INTO log (usu, con) VALUES (?, ?)");
    $stmt->bind_param("ss", $usuario, $hash);
    
    if ($stmt->execute()) {
        echo "Usuario registrado correctamente.";
    } else {
        echo "Error al registrar usuario.";
    }

    $stmt->close();
}
?>

<form method="post">
    Usuario: <input type="text" name="usuario" required><br>
    Contraseña: <input type="password" name="password" required><br>
    <button type="submit">Registrar</button>
</form>



