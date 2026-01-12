<?php
session_start();

include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || $_SESSION['usuario_tipo'] != 'Paciente') {
    $_SESSION['error_login'] = "Acceso denegado.";
    
    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        $id_paciente = $_POST['id_paciente'];
        $presion_sistolica = $_POST['presion_sistolica'];
        $presion_diastolica = $_POST['presion_diastolica'];
        $peso = $_POST['peso'];
        $estatura = $_POST['estatura'];
        $oxigenacion = $_POST['oxigenacion'];
        
        $fecha_actual = date('Y-m-d'); 

        $sql = "INSERT INTO HISTORIAL_MEDICO 
                    (padecimiento, presion_sistolica, presion_diastolica, peso, estatura, fecha, oxigenacion, detalles, id_paciente)
                VALUES 
                    (NULL, ?, ?, ?, ?, ?, ?, NULL, ?)";
        
        $sentencia = $conn->prepare($sql);
        
        $sentencia->execute([
            $presion_sistolica,
            $presion_diastolica,
            $peso,
            $estatura,
            $fecha_actual,
            $oxigenacion,
            $id_paciente
        ]);

        $_SESSION['exito_historial'] = "¡Tu registro de salud se guardó correctamente!";

    } catch (PDOException $e) {
        $_SESSION['error_historial'] = "Error al guardar tu registro: " . $e->getMessage();
    }

    header("Location: /HOSPITAL/pages/perfil.php");
    exit;

} else {

    header("Location: /HOSPITAL/pages/perfil.php");
    exit;
}
?>