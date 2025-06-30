<?php
// Incluir archivo de configuración
include 'config.php';

// Verificar si se enviaron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener datos del formulario
    $sexo = $_POST['sexo'];
    
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
        
        $estadisticas = [
            'sexo' => $sexo,
            'total' => $fila['total'],
            'promedio' => $fila['promedio'],
            'empleado_mayor' => $fila['empleado_mayor']
        ];
        
        $mensaje = "✅ Estadísticas calculadas correctamente";
        $tipo_mensaje = "success";
        
    } else {
        $mensaje = "❌ Error al calcular estadísticas: " . $stmt->error;
        $tipo_mensaje = "error";
    }
    
    // Cerrar statement
    $stmt->close();
    
} else {
    $mensaje = "❌ No se recibieron datos del formulario";
    $tipo_mensaje = "error";
}

// Cerrar conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas por Sexo - Sistema de Gestión de Empleados</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
        }

        .message {
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            border-left: 4px solid;
        }

        .message.success {
            background: #d4edda;
            border-color: #27ae60;
            color: #155724;
        }

        .message.error {
            background: #f8d7da;
            border-color: #e74c3c;
            color: #721c24;
        }

        .stats-container {
            margin-top: 30px;
            padding: 20px;
            background: #ecf0f1;
            border-radius: 6px;
        }

        .stats-container h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-label {
            display: block;
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .stat-value {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }

        .back-btn {
            background: #34495e;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Estadísticas por Sexo</h1>
        </div>
        
        <div class="content">
            <a href="inicio.html" class="back-btn">← Volver al Menú Principal</a>
            
            <div class="message <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>

            <?php if (isset($estadisticas)): ?>
                <div class="stats-container">
                    <h3>📈 Estadísticas para empleados de sexo: <?php echo htmlspecialchars($estadisticas['sexo']); ?></h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-label">Total de Empleados</span>
                            <span class="stat-value"><?php echo $estadisticas['total']; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Promedio de Sueldo</span>
                            <span class="stat-value">$<?php echo number_format($estadisticas['promedio'], 2); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Empleado con Mayor Sueldo</span>
                            <span class="stat-value"><?php echo htmlspecialchars($estadisticas['empleado_mayor']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 