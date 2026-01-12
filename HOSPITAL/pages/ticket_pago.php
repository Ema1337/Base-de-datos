<?php
include '../config/conexion.php';
session_start();

$folio_pago = isset($_GET['folio']) ? $_GET['folio'] : 0;

$sql = "SELECT p.folio_pago, p.total, p.fecha as fecha_pago,
               c.folio_cita, c.fecha_cita,
               pac.primer_nombre, pac.a_paterno, pac.correo,
               doc_emp.primer_nom_emp, doc_emp.a_pat_emp,
               esp.nombre as especialidad
        FROM PAGO p
        JOIN CITA c ON p.folio_cita = c.folio_cita
        JOIN PACIENTE pac ON c.id_paciente = pac.id_paciente
        JOIN DOCTOR doc ON c.id_doctor = doc.id_doctor
        JOIN EMPLEADO doc_emp ON doc.id_empleado = doc_emp.id_empleado
        JOIN ESPECIALIDAD esp ON doc.id_especialidad = esp.id_especialidad
        WHERE p.folio_pago = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$folio_pago]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$datos) die("Error: Ticket no encontrado.");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?= $folio_pago ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background-color: #eee; padding: 20px; }
        .ticket {
            width: 320px;
            background: white;
            padding: 20px;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h3 { margin: 5px 0; }
        .divider { border-top: 2px dashed #000; margin: 15px 0; }
        .info { text-align: left; font-size: 13px; line-height: 1.4; }
        .total { font-size: 20px; font-weight: bold; margin-top: 15px; border-top: 2px solid #000; padding-top: 10px; }
        .btn-print { 
            margin-top: 20px; padding: 10px 20px; background: #0d6efd; color: white; border: none; cursor: pointer; border-radius: 5px; font-family: sans-serif;
        }
        @media print {
            .btn-print, .btn-back { display: none; }
            body { background: white; margin: 0; padding: 0; }
            .ticket { box-shadow: none; width: 100%; margin: 0; }
        }
    </style>
</head>
<body>

<div class="ticket">
    <div class="header">
        <h3>HOSPITAL SAN ANGEL INN</h3>
        <p>Av. Universidad 123, CDMX</p>
        <p>RFC: HOSP900101-XXX</p>
    </div>
    
    <div class="divider"></div>
    
    <div class="info">
        <p><strong>FOLIO PAGO:</strong> #<?= str_pad($datos['folio_pago'], 6, "0", STR_PAD_LEFT) ?></p>
        <p><strong>FECHA:</strong> <?= date('d/m/Y H:i', strtotime($datos['fecha_pago'])) ?></p>
        <br>
        
        <p><strong>PACIENTE:</strong><br><?= strtoupper($datos['primer_nombre'] . " " . $datos['a_paterno']) ?></p>
        <p><strong>CONTACTO:</strong> <?= $datos['correo'] ?></p>
        
        <div class="divider"></div>
        
        <p><strong>CONCEPTO:</strong> Consulta Médica</p>
        <p><strong>ESPECIALIDAD:</strong> <?= $datos['especialidad'] ?></p>
        <p><strong>MÉDICO:</strong><br>Dr. <?= $datos['primer_nom_emp'] . " " . $datos['a_pat_emp'] ?></p>
    </div>

    <div class="total">
        TOTAL: $<?= number_format($datos['total'], 2) ?> MXN
    </div>
    
    <div class="divider"></div>
    <p style="font-size: 11px;">¡Gracias por su confianza!</p>
    
    <button class="btn-print" onclick="window.print()">🖨️ IMPRIMIR</button>
    <br><br>
    <a href="caja.php" class="btn-back" style="text-decoration: none; color: #666; font-size: 12px; font-family: sans-serif;">&larr; Volver a Caja</a>
</div>

</body>
</html>