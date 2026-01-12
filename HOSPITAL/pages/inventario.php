<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if ($_SESSION['usuario_tipo'] != 'Farmaceutico') { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $detalles = $_POST['detalles'];
    $presentacion = $_POST['presentacion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    
    $sql = "INSERT INTO MEDICAMENTO (nombre, detalles, precio, stock, presentacion) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if($stmt->execute([$nombre, $detalles, $precio, $stock, $presentacion])){
        $msg = "Medicamento agregado correctamente.";
    }
}

$lista = $conn->query("SELECT * FROM MEDICAMENTO")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-box-seam"></i> Inventario de Farmacia</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevo">
            <i class="bi bi-plus-lg"></i> Nuevo Producto
        </button>
    </div>

    <?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Presentación</th>
                        <th>Precio</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista as $m): ?>
                    <tr class="<?php echo ($m['stock'] < 10) ? 'table-danger' : ''; ?>">
                        <td>#<?php echo $m['id_medicamento']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($m['nombre']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($m['detalles']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($m['presentacion']); ?></td>
                        <td class="fw-bold text-success">$<?php echo number_format($m['precio'], 2); ?></td>
                        <td>
                            <?php echo $m['stock']; ?>
                            <?php if($m['stock'] < 10) echo '<span class="badge bg-danger ms-1">Bajo</span>'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="dashboard_farmacia.php" class="btn btn-secondary mt-3">Volver</a>
</div>

<div class="modal fade" id="modalNuevo" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Registrar Medicamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label>Nombre Comercial</label><input type="text" name="nombre" class="form-control" required></div>
                <div class="mb-3"><label>Detalles/Sustancia</label><input type="text" name="detalles" class="form-control" required></div>
                <div class="mb-3"><label>Presentación</label><input type="text" name="presentacion" class="form-control" placeholder="Ej. Caja 20 tabs" required></div>
                <div class="row">
                    <div class="col-6"><label>Precio $</label><input type="number" step="0.50" name="precio" class="form-control" required></div>
                    <div class="col-6"><label>Stock Inicial</label><input type="number" name="stock" class="form-control" required></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php include '../PLANTILLAS/footer.php'; ?>