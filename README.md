# Sistema de Gestión de Empleados

Este proyecto implementa un sistema simple de gestión de empleados usando PHP, MySQL, procedimientos almacenados y triggers.

## Estructura del Proyecto

- `database.sql` - Script para crear la base de datos, tablas, procedimientos almacenados y triggers
- `config.php` - Configuración de conexión a la base de datos
- `inicio.html` - Página principal con formularios
- `insertar.php` - Script para insertar empleados
- `actualizar.php` - Script para actualizar sueldos
- `eliminar.php` - Script para eliminar empleados
- `listar1.php` - Script para listar todos los empleados
- `listar2.php` - Script para calcular estadísticas

## Instalación y Configuración

### 1. Configurar XAMPP
- Asegúrate de que XAMPP esté instalado y funcionando
- Inicia Apache y MySQL desde el panel de control de XAMPP

### 2. Crear la Base de Datos
- Abre phpMyAdmin (http://localhost/phpmyadmin)
- Ve a la pestaña "SQL"
- Copia y pega todo el contenido del archivo `database.sql`
- Ejecuta el script

### 3. Colocar Archivos
- Copia todos los archivos del proyecto a la carpeta: `C:\xampp\htdocs\BENJABD2\`

### 4. Acceder al Sistema
- Abre tu navegador
- Ve a: `http://localhost/BENJABD2/inicio.html`

## Funcionalidades

### 1. Insertar Empleado
- Formulario para agregar nuevos empleados
- Llama al procedimiento almacenado `InsertaEmpleado`
- El trigger `EmpleadoInsert` respalda la información

### 2. Actualizar Sueldo
- Formulario para aumentar el sueldo de un empleado por porcentaje
- Llama al procedimiento almacenado `ActualizaSueldo`
- El trigger `EmpleadoUpdate` respalda los cambios

### 3. Eliminar Empleado
- Formulario para eliminar empleados por ID
- Llama al procedimiento almacenado `EliminaEmpleado`
- El trigger `EmpleadoDelete` respalda la información eliminada

### 4. Listar Empleados
- Muestra todos los empleados en una tabla
- Llama al procedimiento almacenado `ListaEmpleados`

### 5. Estadísticas
- Calcula estadísticas por sexo (total, promedio, empleado con mayor sueldo)
- Llama al procedimiento almacenado `CalculaEstadisticas` con parámetros IN/OUT

## Procedimientos Almacenados

1. `InsertaEmpleado` - Inserta un nuevo empleado
2. `ActualizaSueldo` - Actualiza el sueldo de un empleado
3. `EliminaEmpleado` - Elimina un empleado por ID
4. `ListaEmpleados` - Lista todos los empleados
5. `CalculaEstadisticas` - Calcula estadísticas con parámetros IN/OUT

## Triggers

1. `EmpleadoInsert` - Respalda información al insertar
2. `EmpleadoUpdate` - Respalda cambios al actualizar
3. `EmpleadoDelete` - Respalda información al eliminar

## Tablas de Respaldo

- `temp_Empleado_Insert` - Registra inserciones
- `temp_Empleado_Update` - Registra actualizaciones
- `temp_Empleado_Delete` - Registra eliminaciones

## Notas para Estudiantes

- El código está diseñado para ser simple y fácil de entender
- Cada archivo tiene comentarios explicativos
- Los procedimientos almacenados usan sintaxis básica de MySQL
- Los triggers respaldan automáticamente todas las operaciones
- El diseño web es simple y funcional 