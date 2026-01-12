<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || ($_SESSION['usuario_tipo'] != 'Doctor' && $_SESSION['usuario_tipo'] != 'Medico')) {
    header("Location: login.php");
    exit;
}

$sqlID = "SELECT d.id_doctor 
          FROM DOCTOR d 
          JOIN EMPLEADO e ON d.id_empleado = e.id_empleado 
          WHERE e.correo = ?";
$stmtID = $conn->prepare($sqlID);
$stmtID->execute([$_SESSION['usuario_correo']]);
$docData = $stmtID->fetch(PDO::FETCH_ASSOC);
$id_doctor = $docData['id_doctor'];

$sqlRecetas = "SELECT 
                r.folio_receta, 
                r.fecha, 
                r.diagnostico,
                p.primer_nombre, p.a_paterno, p.a_materno
               FROM RECETA r
               JOIN CITA c ON r.folio_cita = c.folio_cita
               JOIN PACIENTE p ON c.id_paciente = p.id_paciente
               WHERE c.id_doctor = ?
               ORDER BY r.fecha DESC";

$stmt = $conn->prepare($sqlRecetas);
$stmt->execute([$id_doctor]);
$recetas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold"><i class="bi bi-file-earmark-medical"></i> Historial de Recetas</h2>
            <p class="text-muted mb-0">Todas las recetas emitidas por usted</p>
        </div>
        <a href="dashboard_doctor.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Panel
        </a>
    </div>

    <?php if (empty($recetas)): ?>
        <div class="alert alert-info text-center py-5 shadow-sm">
            <i class="bi bi-clipboard-x fs-1"></i>
            <h4 class="mt-3">Sin recetas emitidas</h4>
            <p>Aún no ha generado ninguna receta médica.</p>
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="ps-4">Folio</th>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Diagnóstico</th>
                                <th class="text-end pe-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recetas as $r): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= str_pad($r['folio_receta'], 6, "0", STR_PAD_LEFT) ?></td>
                                <td><?= date('d/m/Y', strtotime($r['fecha'])) ?></td>
                                <td class="fw-bold">
                                    <?= htmlspecialchars($r['primer_nombre'] . " " . $r['a_paterno'] . " " . $r['a_materno']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars(substr($r['diagnostico'], 0, 50)) . (strlen($r['diagnostico']) > 50 ? '...' : '') ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="imprimir_receta.php?folio=<?= $r['folio_cita'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-printer"></i> Ver / Imprimir
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>