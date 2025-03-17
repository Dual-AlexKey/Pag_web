<?php
session_start();
include __DIR__ . '/websystem/conect/conexion.php';

// Cerrar sesión si se pasa "?logout=1"
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: websystem.php");
    exit();
}

// Si el usuario ya está autenticado, redirige al panel
if (isset($_SESSION["usuario"])) {
    header("Location: websystem/panel.php");
    exit();
}

// Procesar login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT con FROM log WHERE usu = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($hash);
    $stmt->fetch();
    $stmt->close();

    if ($hash && password_verify($password, $hash)) {
        $_SESSION["usuario"] = $usuario;
        $_SESSION["ultimo_acceso"] = time(); // Registrar la hora de inicio de sesión
        header("Location: websystem/panel.php");
        exit();
    } 
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="estilos/css/styles.css?<?php echo time(); ?>" />
</head>
<body>
<div class="wrapper">
    <div class="container">
        <h2 class="title">Iniciar sesión</h2>
        <form class="form-box" method="post">
            <label class="label">Usuario:</label>
            <input class="input-field" type="text" name="usuario" required><br>

            <label class="label">Contraseña:</label>
            <input class="input-field" type="password" name="password" required><br>

            <button class="submit-btn" type="submit">Ingresar</button>
        </form>
    </div>
</div>

</body>
</html>
