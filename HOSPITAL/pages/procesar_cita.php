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
        $id_doctor = $_POST['id_doctor'];
        $fecha_cita_dia = $_POST['fecha_cita']; 
        $hora_cita = $_POST['hora_cita'];     
        
        $fecha_cita_str = $fecha_cita_dia . ' ' . $hora_cita . ':00';

        $sql = "EXEC SP_AgendarCita @id_paciente = ?, @id_doctor = ?, @fecha_hora = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $id_paciente, 
            $id_doctor, 
            $fecha_cita_str
        ]);

        $_SESSION['exito_cita'] = "¡Cita agendada correctamente! Tienes 8 horas para realizar tu pago.";
        header("Location: /HOSPITAL/pages/mis_citas.php"); 
        exit;

    } catch (PDOException $e) {
        $error_msg = $e->getMessage();
        
        if (strpos($error_msg, 'SQLSTATE[42000]') !== false) {
           
            $parts = explode(']', $error_msg);
            $clean_msg = end($parts);
        } else {
            $clean_msg = $error_msg;
        }

        $_SESSION['error_cita'] = "No se pudo agendar: " . $clean_msg;
        header("Location: /HOSPITAL/pages/agendar_cita.php");
        exit;
    }

} else {
    header("Location: /HOSPITAL/pages/agendar_cita.php");
    exit;
}
?>