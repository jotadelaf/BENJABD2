<?php
include 'config.php';

echo "🔄 Actualizando base de datos...\n";

// Ejecutar consultas por separado
$queries = [
    "DROP PROCEDURE IF EXISTS InsertaEmpleado",
    "DROP PROCEDURE IF EXISTS ActualizaSueldo", 
    "DROP PROCEDURE IF EXISTS EliminaEmpleado",
    "DROP PROCEDURE IF EXISTS ListaEmpleados",
    "DROP PROCEDURE IF EXISTS CalculaEstadisticas",
    "DROP PROCEDURE IF EXISTS BuscarYActualizarSueldo",
    "DROP PROCEDURE IF EXISTS ActualizarSueldoYListar",
    "DROP TRIGGER IF EXISTS EmpleadoInsert",
    "DROP TRIGGER IF EXISTS EmpleadoUpdate"
];

foreach ($queries as $query) {
    if (!$conexion->query($query)) {
        echo "⚠️  Advertencia: " . $conexion->error . "\n";
    }
}

// Crear procedimientos usando consultas directas
$procedures = [
    "CREATE PROCEDURE InsertaEmpleado(IN IdE INT, IN NomE VARCHAR(100), IN ApeE VARCHAR(100), IN SexoE VARCHAR(20), IN SueldoE INT) BEGIN INSERT INTO Empleado(id, Nombre, Apellido, Sexo, Sueldo) VALUES (IdE, NomE, ApeE, SexoE, SueldoE); END",
    
    "CREATE PROCEDURE ActualizarSueldoYListar(IN porcentaje INT, IN idE INT) BEGIN DECLARE empleado_existe INT DEFAULT 0; DECLARE sueldo_anterior INT; DECLARE sueldo_nuevo INT; DECLARE nombre_empleado VARCHAR(200); DECLARE mensaje_resultado VARCHAR(255); SELECT COUNT(*) INTO empleado_existe FROM Empleado WHERE id = idE; IF empleado_existe > 0 THEN SELECT Sueldo, CONCAT(Nombre, ' ', Apellido) INTO sueldo_anterior, nombre_empleado FROM Empleado WHERE id = idE; SET sueldo_nuevo = sueldo_anterior * (1 + porcentaje/100); UPDATE Empleado SET Sueldo = sueldo_nuevo WHERE id = idE; SET mensaje_resultado = CONCAT('✅ Sueldo actualizado correctamente para ', nombre_empleado, ' (ID: ', idE, '). De $', sueldo_anterior, ' a $', sueldo_nuevo, ' (aumento del ', porcentaje, '%)'); ELSE SET mensaje_resultado = CONCAT('❌ Error: No se encontró un empleado con ID ', idE); END IF; SELECT mensaje_resultado as mensaje; SELECT * FROM Empleado ORDER BY Nombre, Apellido; END",
    
    "CREATE PROCEDURE EliminaEmpleado(IN idE INT) BEGIN DELETE FROM Empleado WHERE id = idE; END",
    
    "CREATE PROCEDURE ListaEmpleados() BEGIN SELECT * FROM Empleado; END",
    
    "CREATE PROCEDURE CalculaEstadisticas(IN sexo_filtro VARCHAR(20), OUT total_empleados INT, OUT promedio_sueldo DECIMAL(10,2), OUT empleado_mayor_sueldo VARCHAR(200)) BEGIN DECLARE max_sueldo INT; DECLARE nombre_max VARCHAR(100); DECLARE apellido_max VARCHAR(100); SELECT COUNT(*) INTO total_empleados FROM Empleado WHERE Sexo = sexo_filtro; SELECT AVG(Sueldo) INTO promedio_sueldo FROM Empleado WHERE Sexo = sexo_filtro; SELECT MAX(Sueldo) INTO max_sueldo FROM Empleado WHERE Sexo = sexo_filtro; SELECT Nombre, Apellido INTO nombre_max, apellido_max FROM Empleado WHERE Sexo = sexo_filtro AND Sueldo = max_sueldo LIMIT 1; IF nombre_max IS NOT NULL THEN SET empleado_mayor_sueldo = CONCAT(nombre_max, ' ', apellido_max); ELSE SET empleado_mayor_sueldo = 'No hay empleados'; END IF; END",
    
    "CREATE PROCEDURE BuscarYActualizarSueldo(IN nombre_buscar VARCHAR(100), IN porcentaje_aumento INT, OUT empleado_encontrado BOOLEAN, OUT mensaje VARCHAR(255)) BEGIN DECLARE empleado_id INT; DECLARE sueldo_actual INT; DECLARE sueldo_nuevo INT; DECLARE nombre_completo VARCHAR(200); SELECT id, Sueldo, CONCAT(Nombre, ' ', Apellido) INTO empleado_id, sueldo_actual, nombre_completo FROM Empleado WHERE Nombre LIKE CONCAT('%', nombre_buscar, '%') OR Apellido LIKE CONCAT('%', nombre_buscar, '%') LIMIT 1; IF empleado_id IS NOT NULL THEN SET sueldo_nuevo = sueldo_actual * (1 + porcentaje_aumento/100); UPDATE Empleado SET Sueldo = sueldo_nuevo WHERE id = empleado_id; SET empleado_encontrado = TRUE; SET mensaje = CONCAT('Empleado encontrado: ', nombre_completo, '. Sueldo actualizado de $', sueldo_actual, ' a $', sueldo_nuevo, ' (aumento del ', porcentaje_aumento, '%)'); ELSE SET empleado_encontrado = FALSE; SET mensaje = CONCAT('No se encontró ningún empleado con el nombre: ', nombre_buscar); END IF; END"
];

foreach ($procedures as $procedure) {
    if (!$conexion->query($procedure)) {
        echo "❌ Error al crear procedimiento: " . $conexion->error . "\n";
    } else {
        echo "✅ Procedimiento creado correctamente\n";
    }
}

// Crear triggers
$triggers = [
    "CREATE TRIGGER EmpleadoInsert AFTER INSERT ON Empleado FOR EACH ROW BEGIN INSERT INTO temp_Empleado_Insert (id, Nombre, Apellido, Sexo, Sueldo, fecha_insercion) VALUES (NEW.id, NEW.Nombre, NEW.Apellido, NEW.Sexo, NEW.Sueldo, NOW()); END",
    
    "CREATE TRIGGER EmpleadoUpdate AFTER UPDATE ON Empleado FOR EACH ROW BEGIN INSERT INTO temp_Empleado_Update (id, Nombre_anterior, Nombre_nuevo, Apellido_anterior, Apellido_nuevo, Sexo_anterior, Sexo_nuevo, Sueldo_anterior, Sueldo_nuevo, fecha_actualizacion) VALUES (NEW.id, OLD.Nombre, NEW.Nombre, OLD.Apellido, NEW.Apellido, OLD.Sexo, NEW.Sexo, OLD.Sueldo, NEW.Sueldo, NOW()); END"
];

foreach ($triggers as $trigger) {
    if (!$conexion->query($trigger)) {
        echo "❌ Error al crear trigger: " . $conexion->error . "\n";
    } else {
        echo "✅ Trigger creado correctamente\n";
    }
}

// Insertar datos de ejemplo
$conexion->query("DELETE FROM Empleado");
$conexion->query("INSERT INTO Empleado VALUES (1, 'Juan', 'Pérez', 'Masculino', 2500)");
$conexion->query("INSERT INTO Empleado VALUES (2, 'María', 'García', 'Femenino', 2800)");
$conexion->query("INSERT INTO Empleado VALUES (3, 'Carlos', 'López', 'Masculino', 2200)");
$conexion->query("INSERT INTO Empleado VALUES (4, 'Ana', 'Martínez', 'Femenino', 3000)");

echo "✅ Datos de ejemplo insertados\n";
echo "🎉 Base de datos actualizada completamente\n";

$conexion->close();
?> 