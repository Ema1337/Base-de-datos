<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || 
    !in_array($_SESSION['usuario_tipo'], ['Administrador', 'Recepcionista'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM V_Bitacora_General ORDER BY ID DESC";
$registros = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-danger fw-bold"><i class="bi bi-shield-lock-fill"></i> Bitácora del Sistema</h2>
            <p class="text-muted">Registro de auditoría (Datos protegidos).</p>
        </div>
        <a href="mi_cuenta.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Folio</th>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Detalles</th>
                            <th>Ref ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registros)): ?>
                            <tr><td colspan="6" class="text-center py-4">Sin registros.</td></tr>
                        <?php else: ?>
                            <?php foreach ($registros as $row): 
                                $accion = strtoupper($row['Accion']);
                                $color = 'bg-secondary';
                                if (strpos($accion, 'ALTA') !== false || strpos($accion, 'NUEVO') !== false) $color = 'bg-success';
                                if (strpos($accion, 'BAJA') !== false || strpos($accion, 'DELETE') !== false || strpos($accion, 'REEMBOLSO') !== false) $color = 'bg-danger';
                                if (strpos($accion, 'VENTA') !== false || strpos($accion, 'COBRO') !== false) $color = 'bg-warning text-dark';
                            ?>
                            <tr>
                                <td class="text-muted small">#<?= $row['ID'] ?></td>
                                <td><?= $row['Fecha_Hora'] ?></td> <td class="fw-bold text-primary"><?= htmlspecialchars($row['Usuario']) ?></td>
                                <td><span class="badge <?= $color ?>"><?= htmlspecialchars($row['Accion']) ?></span></td>
                                <td><?= htmlspecialchars($row['Detalles']) ?></td>
                                <td class="text-center"><span class="badge bg-light text-dark border"><?= $row['ID_Referencia'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>