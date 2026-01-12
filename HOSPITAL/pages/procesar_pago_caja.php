<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || !in_array($_SESSION['usuario_tipo'], ['Recepcionista', 'Administrador', 'Empleado'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $folio_cita = $_POST['folio_cita'];
    $total = $_POST['total'];

    try {
        
        $sqlPago = "INSERT INTO PAGO (total, fecha, folio_cita) VALUES (?, GETDATE(), ?)";
        $stmtPago = $conn->prepare($sqlPago);
        $stmtPago->execute([$total, $folio_cita]);
        
        $folio_pago = $conn->lastInsertId(); 
        if(!$folio_pago) {
             $stmtID = $conn->query("SELECT @@IDENTITY AS id");
             $rowID = $stmtID->fetch(PDO::FETCH_ASSOC);
             $folio_pago = $rowID['id'];
        }

        header("Location: ticket_pago.php?folio=" . $folio_pago);
        exit;

    } catch (Exception $e) {
        echo "<script>alert('Error al cobrar: " . $e->getMessage() . "'); window.location.href='caja.php';</script>";
    }
}
?>