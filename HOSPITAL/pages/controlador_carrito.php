<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

$accion = $_REQUEST['accion'] ?? '';

try {
    if ($accion == 'agregar_medicina') {
        $id = $_POST['id_medicamento'];
        $cant = intval($_POST['cantidad']);

        if($cant < 1) throw new Exception("La cantidad debe ser mayor a 0.");

        $stmt = $conn->prepare("SELECT * FROM MEDICAMENTO WHERE id_medicamento = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) throw new Exception("Medicamento no encontrado.");
        if ($producto['stock'] < $cant) throw new Exception("Stock insuficiente. Solo quedan: " . $producto['stock']);

        $_SESSION['carrito'][] = [
            'tipo' => 'medicina',
            'id' => $id,
            'nombre' => $producto['nombre'],
            'cantidad' => $cant,
            'precio' => $producto['precio'],
            'subtotal' => $producto['precio'] * $cant
        ];
    }

    if ($accion == 'agregar_servicio') {
        $id = $_POST['id_servicio'];
        $cant = 1; 

        $stmt = $conn->prepare("SELECT * FROM SERVICIO WHERE id_servicio = ?");
        $stmt->execute([$id]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$servicio) throw new Exception("Servicio no encontrado.");

        $_SESSION['carrito'][] = [
            'tipo' => 'servicio',
            'id' => $id,
            'nombre' => $servicio['nombre'],
            'cantidad' => $cant,
            'precio' => $servicio['precio'],
            'subtotal' => $servicio['precio'] * $cant
        ];
    }

    if ($accion == 'eliminar') {
        $indice = $_GET['indice'];
        if (isset($_SESSION['carrito'][$indice])) {
            unset($_SESSION['carrito'][$indice]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        }
    }

    if ($accion == 'vaciar') {
        $_SESSION['carrito'] = [];
    }

} catch (Exception $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='venta_farmacia.php';</script>";
    exit;
}

header("Location: venta_farmacia.php");
exit;
?>