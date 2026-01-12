<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || ($_SESSION['usuario_tipo'] != 'Doctor' && $_SESSION['usuario_tipo'] != 'Medico')) {
    header("Location: login.php"); exit;
}
if (!isset($_GET['folio'])) { header("Location: agenda_doctor.php"); exit; }

$folio_cita = $_GET['folio'];
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->beginTransaction();

        $diagnostico = $_POST['diagnostico'];
        $tratamiento = $_POST['tratamiento']; 
        $id_paciente = $_POST['id_paciente'];
        
        $sqlHist = "INSERT INTO HISTORIAL_MEDICO (padecimiento, presion_sistolica, presion_diastolica, peso, estatura, fecha, oxigenacion, detalles, id_paciente)
                    VALUES (?, ?, ?, ?, ?, GETDATE(), ?, ?, ?)";
        $stmtHist = $conn->prepare($sqlHist);
        $stmtHist->execute([$diagnostico, $_POST['presion_sis'], $_POST['presion_dias'], $_POST['peso'], $_POST['estatura'], $_POST['oxigenacion'], $tratamiento, $id_paciente]);

        $sqlReceta = "INSERT INTO RECETA (fecha, diagnostico, folio_cita) VALUES (GETDATE(), ?, ?)";
        $stmtReceta = $conn->prepare($sqlReceta);
        $stmtReceta->execute([$diagnostico, $folio_cita]);
        
        $sqlUpd = "UPDATE CITA 
                   SET id_estado = (SELECT id_estado FROM ESTADO_CITA WHERE estado = 'Atendida') 
                   WHERE folio_cita = ?";
        $stmtUpd = $conn->prepare($sqlUpd);
        $stmtUpd->execute([$folio_cita]);

        $conn->commit();
        
        echo "<script>
                alert('Consulta finalizada correctamente.');
                window.open('imprimir_receta.php?folio=$folio_cita', '_blank'); 
                window.location.href='agenda_doctor.php';
              </script>";
        exit;

    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        $mensaje = "Error: " . $e->getMessage();
    }
}

$sql = "SELECT p.*, c.fecha_cita FROM CITA c JOIN PACIENTE p ON c.id_paciente = p.id_paciente WHERE c.folio_cita = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$folio_cita]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);
$edad = date_diff(date_create($paciente['nacimiento']), date_create('today'))->y;

$historial_previo = [];
try {
    $sqlSP = "EXEC SP_ObtenerHistorialPaciente @id_paciente = ?";
    $stmtSP = $conn->prepare($sqlSP);
    $stmtSP->execute([$paciente['id_paciente']]);
    $historial_previo = $stmtSP->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { /* Ignorar si falla la carga del historial */ }
?>

<div class="container mt-5 mb-5">
    
    <?php if ($mensaje): ?><div class="alert alert-danger"><?= $mensaje ?></div><?php endif; ?>
    
    <div class="row">
        <div class="col-md-4">
            
            <div class="card bg-light mb-3 shadow-sm">
                <div class="card-header bg-info text-white fw-bold">Paciente Actual</div>
                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($paciente['primer_nombre'] . " " . $paciente['a_paterno']) ?></h4>
                    <p class="card-text mb-1">Edad: <strong><?= $edad ?> años</strong></p>
                    <p class="card-text mb-1">Sexo: <?= htmlspecialchars($paciente['sexo']) ?></p>
                    <p class="card-text text-danger small">Alergias: <?= htmlspecialchars($paciente['alergias'] ?? 'Ninguna') ?></p>
                </div>
            </div>

            <div class="card shadow-sm" style="max-height: 500px; overflow-y: auto;">
                <div class="card-header bg-dark text-white"><i class="bi bi-clock-history"></i> Historial Previo</div>
                <ul class="list-group list-group-flush">
                    <?php if (empty($historial_previo)): ?>
                        <li class="list-group-item text-muted text-center py-3">Sin consultas anteriores.</li>
                    <?php else: ?>
                        <?php foreach($historial_previo as $h): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted fw-bold"><?= date('d/m/Y', strtotime($h['Fecha_movimiento'])) ?></small>
                                    <span class="badge bg-secondary" style="font-size: 0.6rem;"><?= htmlspecialchars($h['Especialidad']) ?></span>
                                </div>
                                <div class="mt-1">
                                    <strong><?= htmlspecialchars($h['Diagnostico']) ?></strong>
                                </div>
                                <div class="text-muted small fst-italic mt-1">
                                    Dr. <?= htmlspecialchars($h['Usuario_Medico']) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

        </div>

        <div class="col-md-8">
            <div class="card shadow border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-pulse"></i> Registrar Consulta</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <input type="hidden" name="id_paciente" value="<?= $paciente['id_paciente'] ?>">
                        
                        <h6 class="text-secondary border-bottom pb-2 mb-3">Signos Vitales</h6>
                        <div class="row mb-3">
                            <div class="col-md-3 mb-2">
                                <label class="form-label small">Presión (Sis)</label>
                                <input type="number" name="presion_sis" class="form-control" required placeholder="120">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label small">Presión (Dias)</label>
                                <input type="number" name="presion_dias" class="form-control" required placeholder="80">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label small">Peso (kg)</label>
                                <input type="number" step="0.1" name="peso" class="form-control" required placeholder="70.5">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label small">Estatura (m)</label>
                                <input type="number" step="0.01" name="estatura" class="form-control" required placeholder="1.75">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small">Oxigenación (%)</label>
                            <input type="number" name="oxigenacion" class="form-control w-25" required placeholder="98">
                        </div>
                        
                        <h6 class="text-secondary border-bottom pb-2 mb-3">Diagnóstico y Tratamiento</h6>
                        
                        <div class="mb-3">
                            <label class="fw-bold form-label">Diagnóstico Médico</label>
                            <textarea name="diagnostico" class="form-control" rows="2" required placeholder="Ej. Faringitis aguda bacteriana"></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="fw-bold form-label">Receta / Indicaciones (Rp)</label>
                            <textarea name="tratamiento" class="form-control font-monospace" rows="6" placeholder="1. Amoxicilina 500mg...&#10;2. Paracetamol..." required></textarea>
                            <div class="form-text">Esto se imprimirá en la receta del paciente.</div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm">
                            <i class="bi bi-check-circle-fill"></i> Finalizar y Generar Receta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>