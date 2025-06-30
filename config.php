<?php
// Configuración de la base de datos
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "empresa";

// Función para crear la base de datos automáticamente
function crearBaseDatosAutomaticamente($servidor, $usuario, $password, $base_datos) {
    try {
        // Conectar sin especificar base de datos
        $conexion_temp = new mysqli($servidor, $usuario, $password);
        
        if ($conexion_temp->connect_error) {
            return false;
        }
        
        // Verificar si la base de datos existe
        $result = $conexion_temp->query("SHOW DATABASES LIKE '$base_datos'");
        if ($result->num_rows > 0) {
            // Verificar si los procedimientos almacenados existen
            $conexion_temp->select_db($base_datos);
            $result = $conexion_temp->query("SHOW PROCEDURE STATUS WHERE Db = '$base_datos'");
            if ($result->num_rows >= 4) { // Deberían existir 4 procedimientos
                $conexion_temp->close();
                return true; // La base de datos y procedimientos ya existen
            }
        }
        
        // Crear la base de datos
        if (!$conexion_temp->query("CREATE DATABASE IF NOT EXISTS $base_datos")) {
            $conexion_temp->close();
            return false;
        }
        
        // Seleccionar la base de datos
        $conexion_temp->select_db($base_datos);
        
        // Leer y ejecutar el archivo database.sql
        $sql_file = 'database.sql';
        if (file_exists($sql_file)) {
            $sql_content = file_get_contents($sql_file);
            if ($sql_content) {
                // Procesar el archivo SQL correctamente
                $queries = procesarSQL($sql_content);
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query) && !preg_match('/^(CREATE DATABASE|USE)/i', $query)) {
                        $conexion_temp->query($query);
                    }
                }
            }
        }
        
        $conexion_temp->close();
        return true;
        
    } catch (Exception $e) {
        return false;
    }
}

// Función para procesar correctamente el archivo SQL con DELIMITER
function procesarSQL($sql_content) {
    $queries = [];
    $current_query = '';
    $delimiter = ';';
    
    // Dividir por líneas
    $lines = explode("\n", $sql_content);
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Saltar líneas vacías
        if (empty($line)) {
            continue;
        }
        
        // Saltar comentarios que empiecen con --
        if (preg_match('/^--/', $line)) {
            continue;
        }
        
        // Verificar si es un cambio de delimitador
        if (preg_match('/^DELIMITER\s+(.+)$/i', $line, $matches)) {
            // Guardar la consulta actual si existe
            if (!empty($current_query)) {
                $current_query = trim($current_query);
                if (!empty($current_query)) {
                    $queries[] = $current_query;
                }
                $current_query = '';
            }
            $delimiter = $matches[1];
            continue;
        }
        
        // Agregar la línea a la consulta actual
        $current_query .= $line . "\n";
        
        // Verificar si la consulta termina con el delimitador actual
        if (substr($line, -strlen($delimiter)) === $delimiter) {
            // Remover el delimitador del final
            $current_query = substr($current_query, 0, -strlen($delimiter));
            $current_query = trim($current_query);
            if (!empty($current_query)) {
                $queries[] = $current_query;
            }
            $current_query = '';
        }
    }
    
    // Agregar la última consulta si existe
    if (!empty($current_query)) {
        $current_query = trim($current_query);
        if (!empty($current_query)) {
            $queries[] = $current_query;
        }
    }
    
    return $queries;
}

// Intentar crear la base de datos automáticamente si no existe
crearBaseDatosAutomaticamente($servidor, $usuario, $password, $base_datos);

// Crear conexión
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar charset
$conexion->set_charset("utf8");
?> 