<?php
// Incluir archivo de configuración
include 'config.php';

echo "<h2>Lista de Todos los Empleados</h2>";

// Preparar la llamada al procedimiento almacenado
$sql = "CALL ListaEmpleados()";

// Ejecutar el procedimiento
$resultado = $conexion->query($sql);

if ($resultado) {
    // Verificar si hay registros
    if ($resultado->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #007bff; color: white;'>";
        echo "<th>ID</th>";
        echo "<th>Nombre</th>";
        echo "<th>Apellido</th>";
        echo "<th>Sexo</th>";
        echo "<th>Sueldo</th>";
        echo "</tr>";
        
        // Mostrar cada registro
        while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $fila['id'] . "</td>";
            echo "<td>" . $fila['Nombre'] . "</td>";
            echo "<td>" . $fila['Apellido'] . "</td>";
            echo "<td>" . $fila['Sexo'] . "</td>";
            echo "<td>$" . $fila['Sueldo'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No hay empleados registrados.</p>";
    }
} else {
    echo "<h2>Error al obtener la lista de empleados</h2>";
    echo "<p>Error: " . $conexion->error . "</p>";
}

// Cerrar conexión
$conexion->close();

// Botón para volver
echo "<br><a href='inicio.html'>Volver al inicio</a>";
?> 