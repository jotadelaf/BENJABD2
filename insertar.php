<?php
// Incluir archivo de configuración
include 'config.php';

// Verificar si se enviaron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener datos del formulario
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $sexo = $_POST['sexo'];
    $sueldo = $_POST['sueldo'];
    
    // Preparar la llamada al procedimiento almacenado
    $sql = "CALL InsertaEmpleado(?, ?, ?, ?, ?)";
    
    // Crear statement preparado
    $stmt = $conexion->prepare($sql);
    
    // Vincular parámetros
    $stmt->bind_param("isssi", $id, $nombre, $apellido, $sexo, $sueldo);
    
    // Ejecutar el procedimiento
    if ($stmt->execute()) {
        echo "<h2>Empleado insertado correctamente</h2>";
        echo "<p>ID: " . $id . "</p>";
        echo "<p>Nombre: " . $nombre . "</p>";
        echo "<p>Apellido: " . $apellido . "</p>";
        echo "<p>Sexo: " . $sexo . "</p>";
        echo "<p>Sueldo: $" . $sueldo . "</p>";
    } else {
        echo "<h2>Error al insertar empleado</h2>";
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    
    // Cerrar statement
    $stmt->close();
    
} else {
    echo "<h2>No se recibieron datos del formulario</h2>";
}

// Cerrar conexión
$conexion->close();

// Botón para volver
echo "<br><a href='inicio.html'>Volver al inicio</a>";
?> 