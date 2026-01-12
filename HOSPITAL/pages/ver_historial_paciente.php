<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || !in_array($_SESSION['usuario_tipo'], ['Doctor', 'Medico'])) {
    header("Location: login.php"); exit;
}

$id_paciente = $_GET['id_paciente'];

$sqlPac = "SELECT * FROM PACIENTE WHERE id_paciente = ?";
$stmtPac = $conn->prepare($sqlPac);
$stmtPac->execute([$id_paciente]);
$paciente = $stmtPac->fetch(PDO::FETCH_ASSOC);
$edad = date_diff(date_create($paciente['nacimiento']), date_create('today'))->y;

$historial = [];
try {
    $sqlSP = "EXEC SP_ObtenerHistorialPaciente @id_paciente = ?";
    $stmtSP = $conn->prepare($sqlSP);
    $stmtSP->execute([$id_paciente]);
    $historial = $stmtSP->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { /*...*/ }
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><i class="bi bi-journal-medical"></i> Expediente Clínico</h2>
        <a href="javascript:history.back()" class="btn btn-secondary">Regresar</a>
    </div>

    <div class="card mb-4 border-primary">
        <div class="card-body">
            <h4 class="fw-bold"><?= htmlspecialchars($paciente['primer_nombre'].' '.$paciente['a_paterno']) ?></h4>
            <span class="badge bg-info text-dark"><?= $edad ?> Años</span>
            <span class="badge bg-warning text-dark"><?= htmlspecialchars($paciente['tipo_sangre']) ?></span>
            <span class="badge bg-danger"><?= htmlspecialchars($paciente['alergias'] ?? 'Sin Alergias') ?></span>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="bi bi-clock-history"></i> Historial de Consultas
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Médico (Usuario)</th>
                        <th>Especialidad</th>
                        <th>Consultorio</th>
                        <th>Diagnóstico</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historial)): ?>
                        <tr><td colspan="6" class="text-center">Sin historial previo.</td></tr>
                    <?php else: ?>
                        <?php foreach ($historial as $h): ?>
                        <tr>
                            <td style="white-space:nowrap;"><?= date('d/m/Y', strtotime($h['Fecha_movimiento'])) ?></td>
                            <td class="fw-bold text-primary">Dr. <?= htmlspecialchars($h['Usuario_Medico']) ?></td>
                            <td><?= htmlspecialchars($h['Especialidad']) ?></td>
                            <td class="text-center"><span class="badge bg-secondary">#<?= htmlspecialchars($h['Consultorio']) ?></span></td>
                            <td class="fw-bold"><?= htmlspecialchars($h['Diagnostico']) ?></td>
                            <td><small><?= htmlspecialchars($h['Tratamiento_Observaciones']) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../PLANTILLAS/footer.php'; ?>