<?php
// Incluir archivo de configuración
include 'config.php';

// Preparar la llamada al procedimiento almacenado
$sql = "CALL ListaEmpleados()";

// Ejecutar el procedimiento
$resultado = $conexion->query($sql);

// Cerrar conexión
$conexion->close();
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

    .message {
        padding: 15px;
        margin: 15px 0;
        border-radius: 4px;
        border-left: 4px solid;
        background: #f8d7da;
        border-color: #e74c3c;
        color: #721c24;
        text-align: center;
    }
</style>

<?php if ($resultado && $resultado->num_rows > 0): ?>
    <table class="empleados-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Sexo</th>
                <th>Sueldo</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila['id']); ?></td>
                    <td><?php echo htmlspecialchars($fila['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['Apellido']); ?></td>
                    <td><?php echo htmlspecialchars($fila['Sexo']); ?></td>
                    <td>$<?php echo number_format($fila['Sueldo'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="message">
        📋 No hay empleados registrados en la base de datos.
    </div>
<?php endif; ?> 