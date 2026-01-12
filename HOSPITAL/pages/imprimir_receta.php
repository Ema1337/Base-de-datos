<?php
include '../config/conexion.php';
session_start();

if (!isset($_GET['folio'])) {
    die("Error: Folio de receta no especificado.");
}

$folio_cita = $_GET['folio'];

$sql = "SELECT * FROM V_DatosReceta WHERE folio_cita = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$folio_cita]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$datos) {
    die("Error: No se encontró la receta o faltan datos.");
}

$edad = date_diff(date_create($datos['nacimiento']), date_create('today'))->y;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta Médica - Folio <?= $folio_cita ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #555; /* Fondo oscuro para resaltar la hoja */
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hoja-receta {
            background: white;
            width: 21cm;        
            min-height: 29.7cm; 
            margin: 0 auto;
            padding: 2.5cm;
            position: relative;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }
        .header {
            border-bottom: 3px solid #0056b3;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .datos-medico {
            margin-bottom: 20px;
        }
        .datos-paciente {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }
        .seccion-titulo {
            color: #0056b3;
            text-transform: uppercase;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        .contenido-texto {
            font-size: 1.1rem;
            margin-bottom: 30px;
            white-space: pre-line; 
        }
        .tratamiento-box {
            font-family: 'Courier New', Courier, monospace;
            min-height: 200px;
        }
        .firma-box {
            margin-top: 80px;
            text-align: center;
            display: flex;
            justify-content: end;
        }
        .firma-linea {
            width: 250px;
            border-top: 1px solid black;
            padding-top: 5px;
            text-align: center;
        }
        .footer {
            position: absolute;
            bottom: 1.5cm;
            left: 0; 
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
            width: 80%;
            margin: 0 auto;
        }

        @media print {
            body { 
                background: white; 
                padding: 0; 
                margin: 0; 
            }
            .hoja-receta { 
                width: 100%; 
                height: 100%; 
                margin: 0; 
                box-shadow: none; 
                padding: 1cm; 
            }
            .no-print { 
                display: none !important; 
            }
        }
    </style>
</head>
<body>

    <div class="no-print position-fixed top-0 end-0 m-4 d-grid gap-2">
        <button onclick="window.print()" class="btn btn-primary shadow">
            <i class="bi bi-printer"></i> Imprimir
        </button>
        <button onclick="window.close()" class="btn btn-secondary shadow">
            Cerrar
        </button>
    </div>

    <div class="hoja-receta">
        
        <div class="header d-flex justify-content-between align-items-end">
            <div>
                <h1 class="text-primary fw-bold m-0" style="font-size: 2rem;">HOSPITAL SAN ANGEL INN</h1>
                <small class="text-muted">Salud y Confianza a su alcance</small><br>
                <small>Av. Universidad 123, Ciudad de México | Tel: 55-1234-5678</small>
            </div>
            <div class="text-end">
                <h4 class="text-secondary fw-bold m-0">RECETA MÉDICA</h4>
                <div class="mt-2">
                    <strong>Folio:</strong> #<?= str_pad($folio_cita, 6, "0", STR_PAD_LEFT) ?><br>
                    <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($datos['fecha_cita'])) ?>
                </div>
            </div>
        </div>

        <div class="datos-medico">
            <h5 class="fw-bold m-0">Dr(a). <?= htmlspecialchars($datos['d_nombre'] . " " . $datos['d_paterno']) ?></h5>
            <p class="m-0 text-secondary"><?= htmlspecialchars($datos['especialidad']) ?></p>
            <small class="text-muted">Cédula Prof: <strong><?= htmlspecialchars($datos['cedula_profesional']) ?></strong> | Consultorio: <strong><?= htmlspecialchars($datos['no_consultorio']) ?></strong></small>
        </div>

        <div class="datos-paciente row">
            <div class="col-8">
                <small class="text-muted text-uppercase d-block">Paciente</small>
                <strong class="fs-5"><?= htmlspecialchars($datos['p_nombre'] . " " . $datos['p_paterno'] . " " . $datos['p_materno']) ?></strong>
            </div>
            <div class="col-4">
                <small class="text-muted text-uppercase d-block">Detalles</small>
                <span>Edad: <?= $edad ?> años</span> &nbsp;|&nbsp; <span>Sexo: <?= $datos['sexo'] ?></span>
            </div>
        </div>

        <div class="mb-4">
            <div class="seccion-titulo">Diagnóstico Médico</div>
            <div class="contenido-texto">
                <?= nl2br(htmlspecialchars($datos['diagnostico'])) ?>
            </div>
        </div>

        <div class="mb-5">
            <div class="seccion-titulo">Tratamiento e Indicaciones (Rp)</div>
            <div class="contenido-texto tratamiento-box">
                <?= nl2br(htmlspecialchars($datos['tratamiento'])) ?>
            </div>
        </div>

        <div class="firma-box">
            <div class="firma-linea">
                <img src="" alt="" height="40"> <br>
                Firma del Médico
                <br>
                <small class="text-muted">Dr. <?= htmlspecialchars($datos['d_nombre']) ?></small>
            </div>
        </div>

        <div class="footer">
            <p class="m-0">Este documento es una receta médica válida emitida por Hospital San Angel Inn.</p>
            <p class="m-0">Surtir en su farmacia de preferencia. En caso de emergencia llame al 911 o acuda a urgencias.</p>
            <small>Generado el <?= date('d/m/Y H:i') ?></small>
        </div>

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</body>
</html>