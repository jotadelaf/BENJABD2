<?php
// Incluir archivo de configuración
include 'config.php';

// Verificar si se enviaron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener datos del formulario
    $id = $_POST['id'];
    
    // Preparar la llamada al procedimiento almacenado
    $sql = "CALL EliminaEmpleado(?)";
    
    // Crear statement preparado
    $stmt = $conexion->prepare($sql);
    
    // Vincular parámetros
    $stmt->bind_param("i", $id);
    
    // Ejecutar el procedimiento
    if ($stmt->execute()) {
        echo "<h2>Empleado eliminado correctamente</h2>";
        echo "<p>ID del empleado eliminado: " . $id . "</p>";
        
        // Verificar si se eliminó algún registro
        if ($stmt->affected_rows > 0) {
            echo "<p>Se eliminó el empleado de la base de datos.</p>";
        } else {
            echo "<p>No se encontró un empleado con ese ID.</p>";
        }
    } else {
        echo "<h2>Error al eliminar empleado</h2>";
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