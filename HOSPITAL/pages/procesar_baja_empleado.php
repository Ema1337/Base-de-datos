<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo'])) {
    header("Location: login.php");
    exit;
}

$id_empleado = isset($_GET['id']) ? $_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion'] : null;

if ($id_empleado && $accion) {
    try {
        $nuevo_estado = ($accion == 'alta') ? 1 : 0;

        $sql = "UPDATE EMPLEADO SET activo = ? WHERE id_empleado = ?";
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([$nuevo_estado, $id_empleado]);

        if ($nuevo_estado == 0) {
            $msg = "Empleado dado de baja exitosamente.";
            $tipo = "warning"; 
        } else {
            $msg = "Empleado reactivado correctamente.";
            $tipo = "success";
        }

    } catch (PDOException $e) {
        $error_mensaje = $e->getMessage();
        
        if (strpos($error_mensaje, 'ACCION DENEGADA') !== false) {
            $msg = "⛔ BLOQUEO DE SEGURIDAD: El doctor tiene citas pendientes. Debes cancelarlas o reasignarlas antes de darlo de baja.";
        } else {
            $msg = "Error técnico: " . $error_mensaje;
        }
        
        $tipo = "danger";
    }
} else {
    $msg = "Datos incompletos.";
    $tipo = "warning";
}

header("Location: gestion_empleados.php?msg=" . urlencode($msg) . "&tipo=" . $tipo);
exit;
?>