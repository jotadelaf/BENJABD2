<?php
// Incluir archivo de configuración
include 'config.php';

// Verificar si se enviaron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener datos del formulario
    $id = $_POST['id'];
    $porcentaje = $_POST['porcentaje'];
    
    // Preparar la llamada al procedimiento almacenado
    $sql = "CALL ActualizaSueldo(?, ?)";
    
    // Crear statement preparado
    $stmt = $conexion->prepare($sql);
    
    // Vincular parámetros
    $stmt->bind_param("ii", $porcentaje, $id);
    
    // Ejecutar el procedimiento
    if ($stmt->execute()) {
        echo "<h2>Sueldo actualizado correctamente</h2>";
        echo "<p>ID del empleado: " . $id . "</p>";
        echo "<p>Porcentaje de aumento: " . $porcentaje . "%</p>";
        
        // Verificar si se actualizó algún registro
        if ($stmt->affected_rows > 0) {
            echo "<p>Se actualizó el sueldo del empleado.</p>";
        } else {
            echo "<p>No se encontró un empleado con ese ID.</p>";
        }
    } else {
        echo "<h2>Error al actualizar sueldo</h2>";
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