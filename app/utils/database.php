<?php

function get_mysql_connection(){
    $server = "localhost";
    $user = "root";
    $pass = "";
    $db = "La_Lico";

    // Crear una conexión al servidor MySQL
    $mysqli = new mysqli($server, $user, $pass, $db);

    // Verificar si la conexión fue exitosa
    if ($mysqli->connect_error) {
        die("Error en la conexión a la base de datos: " . $mysqli->connect_error);
    }

    // Retornar la conexión establecida
    return $mysqli;
}

// Prueba de la conexión
$conexion = get_mysql_connection();
// Eliminar o comentar esta línea para evitar que se muestre el mensaje
// echo "Conexión exitosa a la base de datos.";
?>
