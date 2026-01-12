<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include '../config/conexion.php';

if (empty($_SESSION['carrito'])) {
    header("Location: venta_farmacia.php");
    exit;
}
if (!isset($_SESSION['usuario_correo'])) {
    die("Error: Sesión expirada.");
}

$nombre_cliente = !empty($_POST['nombre_cliente']) ? $_POST['nombre_cliente'] : 'Público General';
$total = 0;

foreach ($_SESSION['carrito'] as $item) {
    $total += $item['subtotal'];
}

try {
    $conn->beginTransaction();

    $sqlFarm = "SELECT f.id_farmaceutico 
                FROM FARMACEUTICO f 
                JOIN EMPLEADO e ON f.id_empleado = e.id_empleado 
                WHERE e.correo = ?";
    $stmtF = $conn->prepare($sqlFarm);
    $stmtF->execute([$_SESSION['usuario_correo']]);
    $id_farmaceutico = $stmtF->fetchColumn();

    if (!$id_farmaceutico) throw new Exception("Farmacéutico no identificado.");

    $sqlVenta = "INSERT INTO VENTA (fecha, subtotal, id_farmaceutico, id_paciente) 
                 VALUES (GETDATE(), ?, ?, NULL); 
                 SELECT SCOPE_IDENTITY() as id;";
    
    $stmtV = $conn->prepare($sqlVenta);
    $stmtV->execute([$total, $id_farmaceutico]);
    $stmtV->nextRowset(); 
    $folio_venta = $stmtV->fetchColumn();

    if (!$folio_venta) throw new Exception("No se generó el folio de venta.");

    $sqlDetalle = "INSERT INTO DETALLE_VENTA (folio_venta, id_medicamento, id_servicio, cantidad, precio_unitario) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmtD = $conn->prepare($sqlDetalle);

    foreach ($_SESSION['carrito'] as $item) {
        $id_med = ($item['tipo'] == 'medicina') ? $item['id'] : NULL;
        $id_serv = ($item['tipo'] == 'servicio') ? $item['id'] : NULL;

        $stmtD->execute([
            $folio_venta,
            $id_med,
            $id_serv,
            $item['cantidad'],
            $item['precio']
        ]);
    }

    $conn->commit();

    $_SESSION['carrito'] = [];

    $urlTicket = "ticket_venta.php?folio=" . $folio_venta . "&cliente=" . urlencode($nombre_cliente);
    header("Location: " . $urlTicket);
    exit;

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    die("Error crítico: " . $e->getMessage() . " <a href='venta_farmacia.php'>Volver</a>");
}
?>