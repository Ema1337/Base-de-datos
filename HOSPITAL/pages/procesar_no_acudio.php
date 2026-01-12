<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || !in_array($_SESSION['usuario_tipo'], ['Doctor', 'Medico'])) {
    header("Location: login.php"); exit;
}

if (!isset($_GET['folio'])) {
    header("Location: agenda_doctor.php"); exit;
}

$folio = $_GET['folio'];

try {
    $sql = "UPDATE CITA 
            SET id_estado = (SELECT id_estado FROM ESTADO_CITA WHERE estado = 'No acudió') 
            WHERE folio_cita = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$folio]);


    header("Location: agenda_doctor.php?msg=Se registró la inasistencia del paciente.");

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>