<?php
session_start();
include '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente = isset($_POST['nombre_cliente']) ? $_POST['nombre_cliente'] : 'Público General';
    $id_med = $_POST['id_medicamento'];
    $cantidad = $_POST['cantidad'];
    
    try {
        $conn->beginTransaction();

        $sqlFarm = "SELECT f.id_farmaceutico 
                    FROM FARMACEUTICO f 
                    JOIN EMPLEADO e ON f.id_empleado = e.id_empleado 
                    WHERE e.correo = ?";
        $stmtF = $conn->prepare($sqlFarm);
        $stmtF->execute([$_SESSION['usuario_correo']]);
        $id_farmaceutico = $stmtF->fetchColumn();

        if (!$id_farmaceutico) throw new Exception("Error: No se encontró perfil de farmacéutico.");

        $stmtP = $conn->prepare("SELECT precio FROM MEDICAMENTO WHERE id_medicamento = ?");
        $stmtP->execute([$id_med]);
        $precio = $stmtP->fetchColumn();
        $subtotal = $precio * $cantidad;

        $sqlVenta = "INSERT INTO VENTA (fecha, subtotal, id_farmaceutico, nombre_cliente_externo) 
                     VALUES (GETDATE(), ?, ?, ?); 
                     SELECT SCOPE_IDENTITY() as id;";
        
        $stmtV = $conn->prepare($sqlVenta);
        $stmtV->execute([$subtotal, $id_farmaceutico, $cliente]);
        
        $folio_venta = $stmtV->fetchColumn(); 

        if (!$folio_venta) {
            $folio_venta = $conn->lastInsertId(); 
            if (!$folio_venta) throw new Exception("Error crítico: No se pudo generar el folio de venta.");
        }

        $sqlDet = "INSERT INTO DETALLE_VENTA (folio_venta, id_medicamento, cantidad, precio_unitario) 
                   VALUES (?, ?, ?, ?)";
        $stmtD = $conn->prepare($sqlDet);
        $stmtD->execute([$folio_venta, $id_med, $cantidad, $precio]);

        $conn->commit();
        
        echo "<script>alert('Venta realizada con éxito. Total: $$subtotal'); window.location.href='venta_farmacia.php';</script>";

    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>