<?php
// Incluir archivo de configuración
include 'config.php';

// Verificar si se enviaron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $sexo = $_POST['sexo'];
    $sueldo = $_POST['sueldo'];
    
    // Generar ID automáticamente (buscar el máximo ID y sumar 1)
    $sql_max_id = "SELECT MAX(id) as max_id FROM Empleado";
    $result = $conexion->query($sql_max_id);
    $row = $result->fetch_assoc();
    $id = ($row['max_id'] !== null) ? $row['max_id'] + 1 : 1;
    
    // Preparar la llamada al procedimiento almacenado
    $sql = "CALL InsertaEmpleado(?, ?, ?, ?, ?)";
    
    // Crear statement preparado
    $stmt = $conexion->prepare($sql);
    
    // Vincular parámetros
    $stmt->bind_param("isssi", $id, $nombre, $apellido, $sexo, $sueldo);
    
    // Ejecutar el procedimiento
    if ($stmt->execute()) {
        $mensaje = "✅ Empleado insertado correctamente";
        $tipo_mensaje = "success";
        $datos_empleado = [
            'id' => $id,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'sexo' => $sexo,
            'sueldo' => $sueldo
        ];
    } else {
        $mensaje = "❌ Error al insertar empleado: " . $stmt->error;
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
    <title>Insertar Empleado - Sistema de Gestión de Empleados</title>
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

        .empleado-info {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .empleado-info h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            background: white;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .info-label {
            display: block;
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .info-value {
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
            <h1>👤 Insertar Empleado</h1>
        </div>
        
        <div class="content">
            <div class="message <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>

            <?php if (isset($datos_empleado)): ?>
                <div class="empleado-info">
                    <h3>📋 Datos del Empleado Insertado</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">ID</span>
                            <span class="info-value"><?php echo $datos_empleado['id']; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nombre</span>
                            <span class="info-value"><?php echo htmlspecialchars($datos_empleado['nombre']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Apellido</span>
                            <span class="info-value"><?php echo htmlspecialchars($datos_empleado['apellido']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Sexo</span>
                            <span class="info-value"><?php echo htmlspecialchars($datos_empleado['sexo']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Sueldo</span>
                            <span class="info-value">$<?php echo number_format($datos_empleado['sueldo'], 2); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <a href="inicio.html" class="back-btn">← Volver al Menú Principal</a>
        </div>
    </div>
</body>
</html> 