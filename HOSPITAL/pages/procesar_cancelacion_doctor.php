<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || !in_array($_SESSION['usuario_tipo'], ['Doctor', 'Medico'])) {
    header("Location: login.php"); exit;
}

$folio = $_GET['folio'];

try {
    $sql = "UPDATE CITA SET id_estado = 5 WHERE folio_cita = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$folio]);

    header("Location: agenda_doctor.php?msg=Solicitud de cancelación enviada a Recepción.");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>