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

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$sqlCitas = "SELECT 
                c.folio_cita, c.fecha_cita, c.id_estado,
                c.id_paciente, 
                p.primer_nombre, p.a_paterno, p.a_materno, p.sexo, p.nacimiento,
                ec.estado
             FROM CITA c
             JOIN PACIENTE p ON c.id_paciente = p.id_paciente
             JOIN ESTADO_CITA ec ON c.id_estado = ec.id_estado
             WHERE c.id_doctor = ? 
             AND CAST(c.fecha_cita AS DATE) = ?
             ORDER BY c.fecha_cita ASC";

$stmt = $conn->prepare($sqlCitas);
$stmt->execute([$id_doctor, $fecha]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold"><i class="bi bi-journal-medical"></i> Agenda Médica</h2>
            <p class="text-muted mb-0">Historial y Consultas del día</p>
        </div>
        <a href="dashboard_doctor.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Panel
        </a>
    </div>

    <div class="card shadow-sm mb-4 border-0 bg-light">
        <div class="card-body py-3">
            <form class="row g-3 align-items-center" method="GET">
                <div class="col-auto">
                    <label class="fw-bold text-secondary"><i class="bi bi-calendar-day"></i> Ver fecha:</label>
                </div>
                <div class="col-auto">
                    <input type="date" name="fecha" class="form-control" value="<?= $fecha ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <?php if($fecha != date('Y-m-d')): ?>
                        <a href="agenda_doctor.php" class="btn btn-link text-decoration-none">Ir a Hoy</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($citas)): ?>
        <div class="text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted"></i>
            <h4 class="mt-3 text-muted">No hay citas registradas en esta fecha.</h4>
        </div>
    <?php else: ?>
        
        <div class="row">
            <?php foreach ($citas as $cita): 
                $hora = date('H:i A', strtotime($cita['fecha_cita']));
                $edad = date_diff(date_create($cita['nacimiento']), date_create('today'))->y;
                $id_estado = $cita['id_estado'];
                
                $borde = "border-secondary";
                $badgeClase = "bg-secondary";
                $botonHtml = "";

                switch ($id_estado) {
                    case 1: 
                        $borde = "border-warning";
                        $badgeClase = "bg-warning text-dark";
                        $botonHtml = '
                            <button class="btn btn-warning w-100 disabled mb-2"><i class="bi bi-hourglass-split"></i> Pago Pendiente</button>
                            <a href="ver_historial_paciente.php?id_paciente='.$cita['id_paciente'].'" class="btn btn-outline-info w-100 btn-sm">
                                <i class="bi bi-clock-history"></i> Ver Historial Previo
                            </a>';
                        break;
                    
                    case 2: 
                        $borde = "border-primary";
                        $badgeClase = "bg-primary";
                        $botonHtml = '
                            <a href="consulta_medica.php?folio='.$cita['folio_cita'].'" class="btn btn-primary w-100 shadow-sm mb-2">
                                <i class="bi bi-clipboard-pulse"></i> <strong>Atender Cita</strong>
                            </a>
                            
                            <a href="ver_historial_paciente.php?id_paciente='.$cita['id_paciente'].'" class="btn btn-outline-info w-100 btn-sm mb-2">
                                <i class="bi bi-clock-history"></i> Ver Historial Previo
                            </a>

                            <div class="row g-1">
                                <div class="col-6">
                                    <a href="procesar_solicitud_cancelacion.php?folio='.$cita['folio_cita'].'" class="btn btn-outline-secondary w-100 btn-sm" onclick="return confirm(\'¿Solicitar cancelación?\')">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="procesar_no_acudió.php?folio='.$cita['folio_cita'].'" class="btn btn-outline-danger w-100 btn-sm" onclick="return confirm(\'¿Marcar como NO ASISTIÓ? Esto cerrará la cita sin reembolso.\')">
                                        <i class="bi bi-person-x-fill"></i> No Llegó
                                    </a>
                                </div>
                            </div>';
                        break;

                    case 3: 
                    case 5: 
                    case 7: 
                        $borde = "border-danger";
                        $badgeClase = "bg-danger";
                        $botonHtml = '
                            <button class="btn btn-outline-danger w-100 disabled mb-2">'.htmlspecialchars($cita['estado']).'</button>
                            <a href="ver_historial_paciente.php?id_paciente='.$cita['id_paciente'].'" class="btn btn-outline-info w-100 btn-sm">
                                <i class="bi bi-clock-history"></i> Ver Historial
                            </a>';
                        break;

                    case 4:
                    case 6: 
                        $borde = "border-success";
                        $badgeClase = "bg-success";
                        $botonHtml = '
                            <a href="imprimir_receta.php?folio='.$cita['folio_cita'].'" class="btn btn-outline-success w-100 mb-2" target="_blank">
                                <i class="bi bi-printer-fill"></i> Ver Receta / Imprimir
                            </a>
                            <a href="ver_historial_paciente.php?id_paciente='.$cita['id_paciente'].'" class="btn btn-outline-info w-100 btn-sm">
                                <i class="bi bi-clock-history"></i> Ver Historial Completo
                            </a>';
                        break;
                        
                    default: 
                         $botonHtml = '<button class="btn btn-secondary w-100 disabled">'.htmlspecialchars($cita['estado']).'</button>';
                         break;
                }
            ?>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow border-0 border-start border-5 <?= $borde ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fw-bold text-dark mb-0"><?= $hora ?></h3>
                            <span class="badge rounded-pill <?= $badgeClase ?> px-3 py-2"><?= $cita['estado'] ?></span>
                        </div>
                        <h5 class="card-title fw-bold text-primary mb-1">
                            <?= htmlspecialchars($cita['primer_nombre'] . " " . $cita['a_paterno']) ?>
                        </h5>
                        <div class="d-flex gap-3 text-muted small mb-3">
                            <span><?= $cita['sexo'] ?></span>
                            <span><?= $edad ?> años</span>
                        </div>
                        <hr class="text-muted opacity-25">
                        <div class="mt-3"><?= $botonHtml ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php include '../PLANTILLAS/footer.php'; ?>
