<!-- Footer -->
<?php

// Consulta para obtener los valores de 'pie_pagina' y 'piefondo' en la tabla "Empresa"
$sql = "SELECT pie_pagina, piefondo FROM Empresa LIMIT 1"; // Suponemos que hay un solo registro relevante
$result = $conn->query($sql);

$footerStyle = ''; // Inicializamos la variable para los estilos dinámicos
$footerText = '';  // Inicializamos la variable para el texto del pie de página

if ($result && $row = $result->fetch_assoc()) {
    if (!empty($row['piefondo'])) {
        // Si 'piefondo' tiene un valor, usarlo como color de fondo
        $footerStyle = "background-color: {$row['piefondo']};";
    }
    if (!empty($row['pie_pagina'])) {
        // Si 'pie_pagina' tiene un valor, asignarlo al texto del footer
        $footerText = $row['pie_pagina'];
    }
}
?>
<footer class="py-3 text-white text-center" style="<?= $footerStyle; ?>">
    <div class="container">
        <p class="mb-0"><?= $footerText; ?></p>
    </div>
</footer>

<script type="text/javascript" src="js/script.js "></script>
</body>
</html>