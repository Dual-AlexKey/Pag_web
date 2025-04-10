<?php
session_start(); // Necesario para usar tokens CSRF

include __DIR__ . '/websystem/conect/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF inválido.");
    }

    // Sanitizar y validar usuario
    $usuario = htmlspecialchars(trim($_POST["usuario"]), ENT_QUOTES, 'UTF-8');
    $password = $_POST["password"];

    if (strlen($usuario) < 4 || strlen($usuario) > 20) {
        die("Usuario inválido.");
    }

    // Verificar si el usuario ya existe
    $check = $conn->prepare("SELECT id FROM log WHERE usu = ?");
    $check->bind_param("s", $usuario);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        die("El usuario ya existe.");
    }
    $check->close();

    // Encriptar y guardar
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO log (usu, con) VALUES (?, ?)");
    $stmt->bind_param("ss", $usuario, $hash);

    if ($stmt->execute()) {
        echo "Usuario registrado correctamente.";
    } else {
        echo "Error al registrar usuario.";
    }

    $stmt->close();
}

// Generar nuevo token CSRF
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<form method="post">
    Usuario: <input type="text" name="usuario" required><br>
    Contraseña: <input type="password" name="password" required><br>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <button type="submit">Registrar</button>
</form>
