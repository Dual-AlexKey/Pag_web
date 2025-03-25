<?php
include __DIR__ . '/../websystem/conect/conexion.php'; // Incluye la conexión (mysqli)

// Consulta para obtener todas las tablas con prefijo 'menu_'
$sqlTablas = "SHOW TABLES LIKE 'menu_%'";
$resultTablas = $conn->query($sqlTablas);

$form = ''; // Inicializamos la variable que contendrá el valor de estilos

if ($resultTablas && $resultTablas->num_rows > 0) {
    // Recorremos cada tabla encontrada
    while ($rowTabla = $resultTablas->fetch_array()) {
        $tabla = $rowTabla[0]; // Nombre de la tabla actual

        // Consultar la tabla actual en busca de un valor en la columna 'estilos'
        $sql = "SELECT estilos FROM $tabla WHERE estilos IN ('registro', 'recuperar', 'login') LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            // Si encontramos un registro válido, tomamos el valor de estilos y salimos del bucle
            $row = $result->fetch_assoc();
            $form = $row['estilos'];
            break;
        }
    }
}

// Funciones para mostrar formularios
function mostrarFormularioRegistro() {
    echo '<form action="registro.php" method="post">
            <h2>Registro de Usuario</h2>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">Registrarse</button>
          </form>';
}

function mostrarFormularioRecuperacion() {
    echo '<form action="recuperacion.php" method="post">
            <h2>Recuperación de Contraseña</h2>
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required><br>

            <button type="submit">Recuperar Contraseña</button>
          </form>';
}

function mostrarFormularioLogin() {
    echo '<form action="login.php" method="post">
            <h2>Inicio de Sesión</h2>
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">Iniciar Sesión</button>
          </form>';
}

// Muestra el formulario según el valor de 'estilos'
switch ($form) {
    case 'registro':
        mostrarFormularioRegistro();
        break;
    case 'recuperar':
        mostrarFormularioRecuperacion();
        break;
    case 'login':
        mostrarFormularioLogin();
        break;
    default:
        echo '<p>No se encontró un formulario válido en las tablas "menu_".</p>';
}

$conn->close();
?>
