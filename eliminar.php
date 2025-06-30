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
        if ($stmt->affected_rows > 0) {
            $mensaje = "✅ Empleado eliminado correctamente";
            $tipo_mensaje = "success";
            $detalle = "Se eliminó el empleado con ID: " . $id;
        } else {
            $mensaje = "⚠️ No se encontró un empleado con ese ID";
            $tipo_mensaje = "warning";
            $detalle = "ID del empleado: " . $id;
        }
    } else {
        $mensaje = "❌ Error al eliminar empleado";
        $tipo_mensaje = "error";
        $detalle = "Error: " . $stmt->error;
    }
    
    // Cerrar statement
    $stmt->close();
    
} else {
    $mensaje = "❌ No se recibieron datos del formulario";
    $tipo_mensaje = "error";
    $detalle = "Debe enviar el ID del empleado a eliminar";
}

// Cerrar conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Empleado - Sistema de Gestión de Empleados</title>
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

        .message.warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }

        .detail-box {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .detail-box h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .detail-text {
            color: #2c3e50;
            font-size: 16px;
            line-height: 1.5;
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
            <h1>🗑️ Eliminar Empleado</h1>
        </div>
        
        <div class="content">
            <div class="message <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>

            <div class="detail-box">
                <h3>📋 Detalles de la Operación</h3>
                <p class="detail-text"><?php echo $detalle; ?></p>
            </div>

            <a href="inicio.html" class="back-btn">← Volver al Menú Principal</a>
        </div>
    </div>
</body>
</html> 