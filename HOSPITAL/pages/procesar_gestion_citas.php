<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo'])) {
    header("Location: login.php");
    exit;
}

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$folio  = isset($_GET['folio']) ? $_GET['folio'] : '';
$fecha_redirect = isset($_GET['fecha_actual']) ? $_GET['fecha_actual'] : date('Y-m-d');

if ($folio && $accion) {

    try {
        if ($accion == 'cancelar') {
            
            $sqlEstado = "SELECT TOP 1 id_estado FROM ESTADO_CITA WHERE estado = 'Cancelada Recepcionista'";
            $stmtEstado = $conn->prepare($sqlEstado);
            $stmtEstado->execute();
            $resultadoEstado = $stmtEstado->fetch(PDO::FETCH_ASSOC);

            if ($resultadoEstado) {
                $id_nuevo_estado = $resultadoEstado['id_estado'];

                $sql = "UPDATE CITA SET id_estado = ? WHERE folio_cita = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$id_nuevo_estado, $folio]);
                
                $msg = "Cita cancelada correctamente por recepcionista.";
            } else {
                $msg = "Error: El estado 'Cancelada Recepcionista' no existe en la BD. Revisa la tabla ESTADO_CITA.";
            }

        } elseif ($accion == 'confirmar') {
            
             $sqlEstado = "SELECT TOP 1 id_estado FROM ESTADO_CITA WHERE estado = 'Confirmada'";
             $stmtEstado = $conn->prepare($sqlEstado);
             $stmtEstado->execute();
             $row = $stmtEstado->fetch(PDO::FETCH_ASSOC);
             
             if($row){
                $sql = "UPDATE CITA SET id_estado = ? WHERE folio_cita = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$row['id_estado'], $folio]);
                $msg = "Asistencia confirmada.";
             } else {
                 $msg = "Error: No se encontró el estado 'Confirmada' en la BD.";
             }
        }

    } catch (PDOException $e) {
        $msg = "Error de BD: " . $e->getMessage();
    }
} else {
    $msg = "Datos incompletos.";
}

header("Location: gestionar_citas.php?fecha=" . $fecha_redirect . "&msg=" . urlencode($msg));
exit;
?>