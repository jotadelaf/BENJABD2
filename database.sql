-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS empresa;
USE empresa;

-- Eliminar procedimientos existentes si existen
DROP PROCEDURE IF EXISTS InsertaEmpleado;
DROP PROCEDURE IF EXISTS ActualizaSueldo;
DROP PROCEDURE IF EXISTS EliminaEmpleado;
DROP PROCEDURE IF EXISTS ListaEmpleados;
DROP PROCEDURE IF EXISTS CalculaEstadisticas;
DROP PROCEDURE IF EXISTS BuscarYActualizarSueldo;
DROP PROCEDURE IF EXISTS ActualizarSueldoYListar;

-- Eliminar triggers existentes si existen
DROP TRIGGER IF EXISTS EmpleadoInsert;
DROP TRIGGER IF EXISTS EmpleadoUpdate;

-- Crear la tabla Empleado
CREATE TABLE IF NOT EXISTS Empleado (
    id INT PRIMARY KEY,
    Nombre VARCHAR(100),
    Apellido VARCHAR(100),
    Sexo VARCHAR(20),
    Sueldo INT
);

-- Crear tabla de respaldo para INSERT
CREATE TABLE IF NOT EXISTS temp_Empleado_Insert (
    id INT,
    Nombre VARCHAR(100),
    Apellido VARCHAR(100),
    Sexo VARCHAR(20),
    Sueldo INT,
    fecha_insercion DATETIME
);

-- Crear tabla de respaldo para UPDATE
CREATE TABLE IF NOT EXISTS temp_Empleado_Update (
    id INT,
    Nombre_anterior VARCHAR(100),
    Nombre_nuevo VARCHAR(100),
    Apellido_anterior VARCHAR(100),
    Apellido_nuevo VARCHAR(100),
    Sexo_anterior VARCHAR(20),
    Sexo_nuevo VARCHAR(20),
    Sueldo_anterior INT,
    Sueldo_nuevo INT,
    fecha_actualizacion DATETIME
);

-- PROCEDIMIENTO 1: Insertar empleado
DELIMITER //
CREATE PROCEDURE InsertaEmpleado(
    IN IdE INT,
    IN NomE VARCHAR(100),
    IN ApeE VARCHAR(100),
    IN SexoE VARCHAR(20),
    IN SueldoE INT
)
BEGIN
    INSERT INTO Empleado(id, Nombre, Apellido, Sexo, Sueldo) 
    VALUES (IdE, NomE, ApeE, SexoE, SueldoE);
END //
DELIMITER ;

-- PROCEDIMIENTO 2: Actualizar sueldo y listar empleados (VERSIÓN FINAL)
DELIMITER //
CREATE PROCEDURE ActualizarSueldoYListar(
    IN porcentaje INT, 
    IN idE INT
)
BEGIN
    DECLARE empleado_existe INT DEFAULT 0;
    DECLARE sueldo_anterior INT;
    DECLARE sueldo_nuevo INT;
    DECLARE nombre_empleado VARCHAR(200);
    DECLARE mensaje_resultado VARCHAR(255);
    
    -- Verificar si el empleado existe y obtener sus datos
    SELECT COUNT(*) INTO empleado_existe
    FROM Empleado 
    WHERE id = idE;
    
    IF empleado_existe > 0 THEN
        -- Obtener datos del empleado
        SELECT Sueldo, CONCAT(Nombre, ' ', Apellido) 
        INTO sueldo_anterior, nombre_empleado
        FROM Empleado 
        WHERE id = idE;
        
        -- Calcular nuevo sueldo
        SET sueldo_nuevo = sueldo_anterior * (1 + porcentaje/100);
        
        -- Actualizar sueldo
        UPDATE Empleado
        SET Sueldo = sueldo_nuevo
        WHERE id = idE;
        
        -- Configurar mensaje de éxito
        SET mensaje_resultado = CONCAT('✅ Sueldo actualizado correctamente para ', nombre_empleado, 
                           ' (ID: ', idE, '). De $', sueldo_anterior, ' a $', sueldo_nuevo, 
                           ' (aumento del ', porcentaje, '%)');
    ELSE
        SET mensaje_resultado = CONCAT('❌ Error: No se encontró un empleado con ID ', idE);
    END IF;
    
    -- Devolver mensaje como primer resultado
    SELECT mensaje_resultado as mensaje;
    
    -- Devolver lista de empleados como segundo resultado
    SELECT * FROM Empleado ORDER BY Nombre, Apellido;
END //
DELIMITER ;

-- PROCEDIMIENTO 3: Eliminar empleado
DELIMITER //
CREATE PROCEDURE EliminaEmpleado(IN idE INT)
BEGIN
    DELETE FROM Empleado WHERE id = idE;
END //
DELIMITER ;

-- PROCEDIMIENTO 4: Listar todos los empleados
DELIMITER //
CREATE PROCEDURE ListaEmpleados()
BEGIN
    SELECT * FROM Empleado;
END //
DELIMITER ;

-- PROCEDIMIENTO 5: Calcular estadísticas (con IN, OUT, variables locales e IF)
DELIMITER //
CREATE PROCEDURE CalculaEstadisticas(
    IN sexo_filtro VARCHAR(20),
    OUT total_empleados INT,
    OUT promedio_sueldo DECIMAL(10,2),
    OUT empleado_mayor_sueldo VARCHAR(200)
)
BEGIN
    DECLARE max_sueldo INT;
    DECLARE nombre_max VARCHAR(100);
    DECLARE apellido_max VARCHAR(100);
    
    -- Contar total de empleados del sexo especificado
    SELECT COUNT(*) INTO total_empleados 
    FROM Empleado 
    WHERE Sexo = sexo_filtro;
    
    -- Calcular promedio de sueldo
    SELECT AVG(Sueldo) INTO promedio_sueldo 
    FROM Empleado 
    WHERE Sexo = sexo_filtro;
    
    -- Encontrar empleado con mayor sueldo
    SELECT MAX(Sueldo) INTO max_sueldo 
    FROM Empleado 
    WHERE Sexo = sexo_filtro;
    
    SELECT Nombre, Apellido INTO nombre_max, apellido_max 
    FROM Empleado 
    WHERE Sexo = sexo_filtro AND Sueldo = max_sueldo 
    LIMIT 1;
    
    -- Usar IF para asignar el nombre completo
    IF nombre_max IS NOT NULL THEN
        SET empleado_mayor_sueldo = CONCAT(nombre_max, ' ', apellido_max);
    ELSE
        SET empleado_mayor_sueldo = 'No hay empleados';
    END IF;
END //
DELIMITER ;

-- TRIGGER 1: Para INSERT
DELIMITER //
CREATE TRIGGER EmpleadoInsert 
AFTER INSERT ON Empleado
FOR EACH ROW
BEGIN
    INSERT INTO temp_Empleado_Insert (id, Nombre, Apellido, Sexo, Sueldo, fecha_insercion)
    VALUES (NEW.id, NEW.Nombre, NEW.Apellido, NEW.Sexo, NEW.Sueldo, NOW());
END //
DELIMITER ;

-- TRIGGER 2: Para UPDATE
DELIMITER //
CREATE TRIGGER EmpleadoUpdate 
AFTER UPDATE ON Empleado
FOR EACH ROW
BEGIN
    INSERT INTO temp_Empleado_Update (
        id, 
        Nombre_anterior, Nombre_nuevo,
        Apellido_anterior, Apellido_nuevo,
        Sexo_anterior, Sexo_nuevo,
        Sueldo_anterior, Sueldo_nuevo,
        fecha_actualizacion
    )
    VALUES (
        NEW.id,
        OLD.Nombre, NEW.Nombre,
        OLD.Apellido, NEW.Apellido,
        OLD.Sexo, NEW.Sexo,
        OLD.Sueldo, NEW.Sueldo,
        NOW()
    );
END //
DELIMITER ;

-- Insertar algunos datos de ejemplo
DELETE FROM Empleado;
INSERT INTO Empleado VALUES (1, 'Juan', 'Pérez', 'Masculino', 2500);
INSERT INTO Empleado VALUES (2, 'María', 'García', 'Femenino', 2800);
INSERT INTO Empleado VALUES (3, 'Carlos', 'López', 'Masculino', 2200);
INSERT INTO Empleado VALUES (4, 'Ana', 'Martínez', 'Femenino', 3000);

-- PROCEDIMIENTO 6: Buscar empleado por nombre y actualizar sueldo
DELIMITER //
CREATE PROCEDURE BuscarYActualizarSueldo(
    IN nombre_buscar VARCHAR(100),
    IN porcentaje_aumento INT,
    OUT empleado_encontrado BOOLEAN,
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE empleado_id INT;
    DECLARE sueldo_actual INT;
    DECLARE sueldo_nuevo INT;
    DECLARE nombre_completo VARCHAR(200);
    
    -- Buscar empleado por nombre (búsqueda parcial)
    SELECT id, Sueldo, CONCAT(Nombre, ' ', Apellido) 
    INTO empleado_id, sueldo_actual, nombre_completo
    FROM Empleado 
    WHERE Nombre LIKE CONCAT('%', nombre_buscar, '%') 
       OR Apellido LIKE CONCAT('%', nombre_buscar, '%')
    LIMIT 1;
    
    -- Verificar si se encontró el empleado
    IF empleado_id IS NOT NULL THEN
        -- Calcular nuevo sueldo
        SET sueldo_nuevo = sueldo_actual * (1 + porcentaje_aumento/100);
        
        -- Actualizar sueldo
        UPDATE Empleado 
        SET Sueldo = sueldo_nuevo 
        WHERE id = empleado_id;
        
        -- Configurar variables de salida
        SET empleado_encontrado = TRUE;
        SET mensaje = CONCAT('Empleado encontrado: ', nombre_completo, 
                           '. Sueldo actualizado de $', sueldo_actual, 
                           ' a $', sueldo_nuevo, ' (aumento del ', porcentaje_aumento, '%)');
    ELSE
        SET empleado_encontrado = FALSE;
        SET mensaje = CONCAT('No se encontró ningún empleado con el nombre: ', nombre_buscar);
    END IF;
END //
DELIMITER ; 