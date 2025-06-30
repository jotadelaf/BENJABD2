<?php
// Incluir archivo de configuración
include 'config.php';

// Procesar actualización de sueldo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $mensaje = "✅ Sueldo actualizado correctamente para el empleado ID: $id";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "❌ Error al actualizar: " . $stmt->error;
        $tipo_mensaje = "error";
    }
    
    // Cerrar statement
    $stmt->close();
}

// Obtener lista de empleados
$sql = "SELECT * FROM Empleado ORDER BY Nombre, Apellido";
$result = $conexion->query($sql);

if (!$result) {
    $error_lista = "❌ Error al obtener empleados: " . $conexion->error;
    $empleados = [];
} else {
    $empleados = [];
    while ($row = $result->fetch_assoc()) {
        $empleados[] = $row;
    }
}
?>

<style>
    .empleados-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-top: 20px;
    }

    .empleados-table th,
    .empleados-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .empleados-table th {
        background: #34495e;
        color: white;
        font-weight: bold;
    }

    .empleados-table tr:hover {
        background: #f8f9fa;
    }

    .btn {
        background: #3498db;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        margin: 2px;
        text-decoration: none;
        display: inline-block;
    }

    .btn:hover {
        background: #2980b9;
    }

    .btn-success {
        background: #27ae60;
    }

    .btn-success:hover {
        background: #229954;
    }

    .btn-danger {
        background: #e74c3c;
    }

    .btn-danger:hover {
        background: #c0392b;
    }

    .form-inline {
        display: inline-block;
        margin: 0;
    }

    .form-inline input[type='number'] {
        width: 80px;
        padding: 4px;
        margin: 0 5px;
        border: 1px solid #ddd;
        border-radius: 3px;
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
</style>

<?php if (isset($mensaje)): ?>
    <div class="message <?php echo $tipo_mensaje; ?>">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<?php if (isset($error_lista)): ?>
    <div class="message error">
        <?php echo $error_lista; ?>
    </div>
<?php endif; ?>

<?php if (!empty($empleados)): ?>
    <table class="empleados-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Sexo</th>
                <th>Sueldo Actual</th>
                <th>Actualizar Sueldo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empleados as $empleado): ?>
                <tr>
                    <td><?php echo htmlspecialchars($empleado['id']); ?></td>
                    <td><?php echo htmlspecialchars($empleado['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($empleado['Apellido']); ?></td>
                    <td><?php echo htmlspecialchars($empleado['Sexo']); ?></td>
                    <td>$<?php echo number_format($empleado['Sueldo'], 2); ?></td>
                    <td>
                        <form class="form-inline" action="actualizar.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $empleado['id']; ?>">
                            <input type="number" name="porcentaje" placeholder="%" min="-100" max="1000" required>
                            <button type="submit" class="btn btn-success">Actualizar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="message error">
        📋 No hay empleados registrados en la base de datos.
    </div>
<?php endif; ?> 