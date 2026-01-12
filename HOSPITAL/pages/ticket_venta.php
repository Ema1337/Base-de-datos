<?php
include '../config/conexion.php';

if (!isset($_GET['folio'])) die("Folio no especificado.");
$folio = $_GET['folio'];
$cliente = $_GET['cliente'] ?? 'Público General';

$sql = "SELECT 
            d.cantidad, d.precio_unitario,
            COALESCE(m.nombre, s.nombre) as concepto,
            CASE WHEN m.id_medicamento IS NOT NULL THEN 'MED' ELSE 'SRV' END as tipo,
            v.fecha, v.subtotal, 
            e.primer_nom_emp, e.a_pat_emp
        FROM VENTA v
        JOIN DETALLE_VENTA d ON v.folio_venta = d.folio_venta
        JOIN FARMACEUTICO f ON v.id_farmaceutico = f.id_farmaceutico
        JOIN EMPLEADO e ON f.id_empleado = e.id_empleado
        LEFT JOIN MEDICAMENTO m ON d.id_medicamento = m.id_medicamento
        LEFT JOIN SERVICIO s ON d.id_servicio = s.id_servicio
        WHERE v.folio_venta = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$folio]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) die("<h3>Venta no encontrada.</h3><a href='venta_farmacia.php'>Volver</a>");
$cabecera = $items[0];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket #<?= $folio ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background: #555; display: flex; justify-content: center; padding-top: 30px; }
        .ticket { 
            width: 300px; 
            background: #fff; 
            padding: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.5); 
        }
        .center { text-align: center; }
        .table { width: 100%; font-size: 12px; border-collapse: collapse; margin-top: 10px; }
        .table td { padding: 4px 0; border-bottom: 1px dotted #ccc; }
        .total { font-size: 18px; font-weight: bold; text-align: right; margin-top: 15px; border-top: 2px solid #000; padding-top: 5px; }
        
        .no-print { margin-top: 20px; text-align: center; }
        .btn { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-family: sans-serif; font-weight: bold; display: inline-block; }
        
        @media print {
            body { background: none; padding: 0; display: block; }
            .ticket { box-shadow: none; width: 100%; margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="ticket">
        <div class="center">
            <h3>HOSPITAL SAN ÁNGEL</h3>
            <p><strong>FARMACIA</strong></p>
            <p>Folio: <?= str_pad($folio, 6, "0", STR_PAD_LEFT) ?></p>
            <p><?= date('d/m/Y H:i', strtotime($cabecera['fecha'])) ?></p>
            <p>Atendió: <?= $cabecera['primer_nom_emp'] ?></p>
            <p>Cliente: <?= htmlspecialchars($cliente) ?></p>
        </div>
        
        <table class="table">
            <thead>
                <tr style="border-bottom: 2px solid #000;">
                    <th>Cant</th>
                    <th>Concepto</th>
                    <th style="text-align:right">$$</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $i): ?>
                <tr>
                    <td><?= $i['cantidad'] ?></td>
                    <td><?= substr($i['concepto'], 0, 20) ?></td>
                    <td style="text-align:right">
                        $<?= number_format($i['cantidad'] * $i['precio_unitario'], 2) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            TOTAL: $<?= number_format($cabecera['subtotal'], 2) ?>
        </div>
        
        <div class="center" style="margin-top:20px">
            <small>¡Gracias por su compra!</small>
        </div>

        <div class="no-print">
            <a href="venta_farmacia.php" class="btn">Nueva Venta / Salir</a>
        </div>
    </div>

</body>
</html>