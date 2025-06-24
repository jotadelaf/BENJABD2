<?php
// Incluir archivo de configuración
include 'config.php';

// Verificar si se enviaron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener datos del formulario
    $sexo = $_POST['sexo'];
    
    echo "<h2>Estadísticas para empleados de sexo: " . $sexo . "</h2>";
    
    // Preparar la llamada al procedimiento almacenado
    $sql = "CALL CalculaEstadisticas(?, @total, @promedio, @empleado_mayor)";
    
    // Crear statement preparado
    $stmt = $conexion->prepare($sql);
    
    // Vincular parámetros
    $stmt->bind_param("s", $sexo);
    
    // Ejecutar el procedimiento
    if ($stmt->execute()) {
        // Obtener los valores de los parámetros OUT
        $resultado = $conexion->query("SELECT @total as total, @promedio as promedio, @empleado_mayor as empleado_mayor");
        $fila = $resultado->fetch_assoc();
        
        echo "<div style='background-color: #e9ecef; padding: 15px; border-radius: 5px;'>";
        echo "<h3>Resultados:</h3>";
        echo "<p><strong>Total de empleados " . $sexo . ":</strong> " . $fila['total'] . "</p>";
        echo "<p><strong>Promedio de sueldo:</strong> $" . number_format($fila['promedio'], 2) . "</p>";
        echo "<p><strong>Empleado con mayor sueldo:</strong> " . $fila['empleado_mayor'] . "</p>";
        echo "</div>";
        
    } else {
        echo "<h2>Error al calcular estadísticas</h2>";
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