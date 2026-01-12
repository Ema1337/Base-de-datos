<?php

$servidor_sql_server = "EMALAP\SQLEXPRESS"; 

$database = "HOSPITAL"; 
$usuario_sql_server = "sa";
$password_sql_server = "Elizk1007"; 

try {
    $conn = new PDO("sqlsrv:Server=$servidor_sql_server;Database=$database", $usuario_sql_server, $password_sql_server);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Error de conexión con SQL Server: "."<br>".$e->getMessage());
}
?>
