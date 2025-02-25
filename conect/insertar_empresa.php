<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "EmpresaDB";

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recibir datos del formulario
$url_pagina = $_POST['url_pagina'];
$nombre = $_POST['nombre'];
$idioma = $_POST['idioma'];
$logo = $_POST['logo'];
$favicon = $_POST['favicon'];
$seo_titulo = $_POST['seo_titulo'];
$seo_descripcion = $_POST['seo_descripcion'];
$seo_metatags = $_POST['seo_metatags'];
$pie_pagina = $_POST['pie_pagina'];
$empresa = $_POST['empresa'];
$ruc = $_POST['ruc'];
$descripcion = $_POST['descripcion'];
$pais = $_POST['pais'];
$dpto = $_POST['dpto'];
$city = $_POST['city'];
$direccion_principal = $_POST['direccion_principal'];
$email_contactos = $_POST['email_contactos'];
$email_ventas = $_POST['email_ventas'];
$telefono_fijo = $_POST['telefono_fijo'];
$telefono_movil = $_POST['telefono_movil'];
$moneda = $_POST['moneda']; 
$precios = $_POST['precios'];
$carrito_compras = $_POST['carrito_compras'];
$zona_usuarios = $_POST['zona_usuarios'];
$terminos_condiciones = $_POST['terminos_condiciones'];

// Verificar si ya existe una empresa en la base de datos
$sql_check = "SELECT COUNT(*) AS total FROM Empresa";
$result = $conn->query($sql_check);
$row = $result->fetch_assoc();

if ($row['total'] > 0) {
    // Si ya existe una empresa, actualizar sus datos
    $sql_update = "UPDATE Empresa SET 
        url_pagina='$url_pagina', nombre='$nombre', idioma='$idioma', logo='$logo', favicon='$favicon', 
        seo_titulo='$seo_titulo', seo_descripcion='$seo_descripcion', seo_metatags='$seo_metatags', 
        empresa='$empresa', pie_pagina='$pie_pagina', ruc='$ruc', descripcion='$descripcion', pais='$pais', 
        dpto='$dpto', city='$city', 
        direccion_principal='$direccion_principal', email_contactos='$email_contactos', 
        email_ventas='$email_ventas', telefono_fijo='$telefono_fijo', telefono_movil='$telefono_movil', 
        moneda='$moneda', precios='$precios', carrito_compras='$carrito_compras', 
        zona_usuarios='$zona_usuarios', terminos_condiciones='$terminos_condiciones'";

    if ($conn->query($sql_update) === TRUE) {
        echo "Configuración actualizada con éxito.";
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
} else {
    // Si no hay ninguna empresa, insertar una nueva
    $sql_insert = "INSERT INTO Empresa (url_pagina, nombre, idioma, logo, favicon, seo_titulo, 
        seo_descripcion, seo_metatags, empresa,pie_pagina, ruc, descripcion, pais, dpto, 
        city, direccion_principal, email_contactos, email_ventas, telefono_fijo, 
        telefono_movil, moneda, precios, carrito_compras, zona_usuarios, terminos_condiciones) 
        VALUES ('$url_pagina', '$nombre', '$idioma', '$logo', '$favicon', '$seo_titulo', 
        '$seo_descripcion', '$seo_metatags', '$empresa','$pie_pagina', '$ruc', '$descripcion', '$pais', 
        '$dpto', '$city', '$direccion_principal', '$email_contactos', 
        '$email_ventas', '$telefono_fijo', '$telefono_movil', '$moneda', '$precios', 
        '$carrito_compras', '$zona_usuarios', '$terminos_condiciones')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "Configuración guardada con éxito.";
    } else {
        echo "Error al insertar: " . $conn->error;
    }
}
// Cerrar la conexión
$conn->close();

// Redirigir de vuelta al panel
header("Location: ../panel.php");
exit;
?>