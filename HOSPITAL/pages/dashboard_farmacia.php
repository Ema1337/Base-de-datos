<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || $_SESSION['usuario_tipo'] != 'Farmaceutico') {
    header("Location: login.php"); exit;
}

$correo = $_SESSION['usuario_correo'];

$sql = "SELECT e.primer_nom_emp, e.a_pat_emp 
        FROM EMPLEADO e WHERE e.correo = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$correo]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre = $datos['primer_nom_emp'] . " " . $datos['a_pat_emp'];
?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h2 class="display-5 text-success"><i class="bi bi-capsule"></i> Módulo de Farmacia</h2>
        <p class="lead">Bienvenido, <?php echo htmlspecialchars($nombre); ?></p>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-md-5">
            <div class="card shadow h-100 border-success">
                <div class="card-body text-center p-5">
                    <i class="bi bi-cart-check-fill text-success" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Punto de Venta</h3>
                    <p class="text-muted">Vender medicamentos a pacientes o público general.</p>
                    <a href="venta_farmacia.php" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-cash-coin"></i> Ir a Caja
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow h-100 border-primary">
                <div class="card-body text-center p-5">
                    <i class="bi bi-boxes text-primary" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Inventario</h3>
                    <p class="text-muted">Consultar stock, precios y agregar productos.</p>
                    <a href="inventario.php" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-clipboard-data"></i> Gestionar Stock
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../PLANTILLAS/footer.php'; ?>