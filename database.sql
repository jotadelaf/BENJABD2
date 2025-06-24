-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS empresa;
USE empresa;

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

-- Crear tabla de respaldo para DELETE
CREATE TABLE IF NOT EXISTS temp_Empleado_Delete (
    id INT,
    Nombre VARCHAR(100),
    Apellido VARCHAR(100),
    Sexo VARCHAR(20),
    Sueldo INT,
    fecha_eliminacion DATETIME
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

-- PROCEDIMIENTO 2: Actualizar sueldo
DELIMITER //
CREATE PROCEDURE ActualizaSueldo(IN porcentaje INT, IN idE INT)
BEGIN
    UPDATE Empleado
    SET Sueldo = Sueldo * (1 + porcentaje/100)
    WHERE id = idE;
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

-- TRIGGER 3: Para DELETE
DELIMITER //
CREATE TRIGGER EmpleadoDelete 
AFTER DELETE ON Empleado
FOR EACH ROW
BEGIN
    INSERT INTO temp_Empleado_Delete (id, Nombre, Apellido, Sexo, Sueldo, fecha_eliminacion)
    VALUES (OLD.id, OLD.Nombre, OLD.Apellido, OLD.Sexo, OLD.Sueldo, NOW());
END //
DELIMITER ;

-- Insertar algunos datos de ejemplo
INSERT INTO Empleado VALUES (1, 'Juan', 'Pérez', 'Masculino', 2500);
INSERT INTO Empleado VALUES (2, 'María', 'García', 'Femenino', 2800);
INSERT INTO Empleado VALUES (3, 'Carlos', 'López', 'Masculino', 2200);
INSERT INTO Empleado VALUES (4, 'Ana', 'Martínez', 'Femenino', 3000); 