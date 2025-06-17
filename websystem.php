<?php
session_start();
include __DIR__ . '/websystem/conect/conexion.php';

// Cerrar sesión si se pasa "?logout=1"
if (isset($_GET["logout"])) {
    session_unset();
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
    // Verifica token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Token CSRF inválido.");
    }

    $usuario = trim($_POST["usuario"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT con FROM log WHERE correo = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($hash);
    $stmt->fetch();
    $stmt->close();

    if ($hash && password_verify($password, $hash)) {
        $_SESSION["usuario"] = $usuario;
        $_SESSION["ultimo_acceso"] = time(); // Registrar la hora de inicio de sesión
        session_regenerate_id(true); // Seguridad extra
        header("Location: websystem/panel.php");
        exit();
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos');</script>";
    }
}

// Generar token CSRF para el formulario
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

$conn->close();
?>

<!DOCTYPE html>
<html lang="es" class="websystem">
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
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">


            <button class="submit-btn" type="submit">Ingresar</button>
        </form>

    </div>
</div>

</body>
</html>
