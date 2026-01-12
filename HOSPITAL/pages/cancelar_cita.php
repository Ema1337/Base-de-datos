<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || $_SESSION['usuario_tipo'] != 'Paciente') {
    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

$folio_cita = isset($_GET['folio_cita']) ? $_GET['folio_cita'] : 0;

if ($folio_cita > 0) {
    try {
        $conn->beginTransaction();

        $sql_check = "SELECT c.id_estado, p.correo, pa.total as total_pagado
                      FROM CITA c
                      JOIN PACIENTE p ON c.id_paciente = p.id_paciente
                      LEFT JOIN PAGO pa ON c.folio_cita = pa.folio_cita
                      WHERE c.folio_cita = ? AND p.correo = ?";
        
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([$folio_cita, $_SESSION['usuario_correo']]);
        $cita = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($cita) {
            
            if ($cita['id_estado'] >= 3) {
                 $_SESSION['error_cita'] = "Esta cita ya ha sido procesada o cancelada anteriormente.";
            } else {
                
                $mensaje_resultado = "";

                if ($cita['id_estado'] == 1) {
                    $mensaje_resultado = "Cita cancelada correctamente. No se generó ningún cargo.";
                }
                
                elseif ($cita['id_estado'] == 2) {
                    
                    $sql_pol = "SELECT dbo.FN_CalcularDevolucion(?) as porcentaje";
                    $stmt_pol = $conn->prepare($sql_pol);
                    $stmt_pol->execute([$folio_cita]);
                    $res_pol = $stmt_pol->fetch(PDO::FETCH_ASSOC);
                    
                    $porcentaje = $res_pol['porcentaje'];
                    $total_pagado = $cita['total_pagado'] ? $cita['total_pagado'] : 0;
                    
                    $monto_devolucion = $total_pagado * ($porcentaje / 100);

                    if ($porcentaje == 100) {
                        $mensaje_resultado = "Cancelación exitosa. Se aplicó un reembolso del 100% ($" . number_format($monto_devolucion, 2) . " MXN) por hacerlo con 48h de anticipación.";
                    } elseif ($porcentaje == 50) {
                        $mensaje_resultado = "Cancelación exitosa. Se aplicó un reembolso parcial del 50% ($" . number_format($monto_devolucion, 2) . " MXN) por hacerlo entre 24h y 48h de anticipación.";
                    } else {
                        $mensaje_resultado = "Cita cancelada. No aplica reembolso ($0.00) porque la cancelación fue con menos de 24h de anticipación.";
                    }
                }

                $sql_cancel = "UPDATE CITA SET id_estado = 4 WHERE folio_cita = ?";
                $stmt_cancel = $conn->prepare($sql_cancel);
                $stmt_cancel->execute([$folio_cita]);
                
                $conn->commit();
                $_SESSION['exito_cita'] = $mensaje_resultado;
            }

        } else {
            $_SESSION['error_cita'] = "No se encontró la cita o no tienes permiso para cancelarla.";
        }

    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        $_SESSION['error_cita'] = "Error al cancelar: " . $e->getMessage();
    }
} else {
    $_SESSION['error_cita'] = "Referencia de cita inválida.";
}

header("Location: /HOSPITAL/pages/mis_citas.php");
exit;
?>