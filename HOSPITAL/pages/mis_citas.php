<?php
include '../PLANTILLAS/header.php'; 
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || $_SESSION['usuario_tipo'] != 'Paciente') {
    $_SESSION['error_login'] = "Acceso denegado.";
    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

$id_paciente = 0;
try {
    $stmt_paciente = $conn->prepare("SELECT id_paciente FROM PACIENTE WHERE correo = ?");
    $stmt_paciente->execute([$_SESSION['usuario_correo']]);
    $paciente = $stmt_paciente->fetch(PDO::FETCH_ASSOC);
    if ($paciente) {
        $id_paciente = $paciente['id_paciente'];
    } else {
        throw new Exception("Error: Paciente no encontrado.");
    }
} catch (Exception $e) { 
    die("Error de autenticación o base de datos: " . $e->getMessage());
}

try {
    $sql_expiradas = "SELECT folio_cita 
                      FROM CITA 
                      WHERE id_paciente = ? AND id_estado = 1 
                      AND dbo.FN_TiempoRestantePago(folio_cita, GETDATE()) <= 0"; 
    
    $stmt_expiradas = $conn->prepare($sql_expiradas);
    $stmt_expiradas->execute([$id_paciente]); 
    $citas_a_cancelar = $stmt_expiradas->fetchAll(PDO::FETCH_ASSOC);

    $cancelaciones_count = 0;
    
    if (!empty($citas_a_cancelar)) {
        $conn->beginTransaction();
        $id_estado_cancelado = 3;  
        $sql_update = "UPDATE CITA SET id_estado = ? WHERE folio_cita = ?";
        $stmt_update = $conn->prepare($sql_update);
        
        foreach ($citas_a_cancelar as $cita_expirada) {
            $stmt_update->execute([$id_estado_cancelado, $cita_expirada['folio_cita']]);
            $cancelaciones_count++;
        }
        $conn->commit();
    }
    
    if ($cancelaciones_count > 0) {
        $_SESSION['error_cita'] = "ATENCIÓN: Se cancelaron $cancelaciones_count cita(s) porque el plazo de 8 horas para pagar expiró.";
    }

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
}

$sql_base = "SELECT * FROM V_CitasDetalladas WHERE id_paciente = ?"; 

try {
    $sql_proximas = $sql_base . " AND (estado_actual IN ('Agendada pendiente de pago', 'Pagada pendiente por atender')) 
                                 ORDER BY fecha_cita ASC";
    $stmt_proximas = $conn->prepare($sql_proximas);
    $stmt_proximas->execute([$id_paciente]);
    $proximas_citas = $stmt_proximas->fetchAll(PDO::FETCH_ASSOC);

    $sql_historial = $sql_base . " AND (estado_actual NOT IN ('Agendada pendiente de pago', 'Pagada pendiente por atender')) 
                                 ORDER BY fecha_cita DESC";
    $stmt_historial = $conn->prepare($sql_historial);
    $stmt_historial->execute([$id_paciente]);
    $historial_citas = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al consultar citas: " . $e->getMessage());
}
?>

<div class="container mt-5 mb-5">

    <?php
    if (isset($_SESSION['exito_cita'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo '<i class="bi bi-check-circle-fill me-2"></i>' . htmlspecialchars($_SESSION['exito_cita']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['exito_cita']);
    }
    if (isset($_SESSION['error_cita'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo '<i class="bi bi-exclamation-triangle-fill me-2"></i>' . htmlspecialchars($_SESSION['error_cita']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['error_cita']);
    }
    ?>

    <h1 class="display-5 mb-4 text-primary">Mis Citas</h1>

    <ul class="nav nav-tabs" id="citasTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="proximas-tab" data-bs-toggle="tab" data-bs-target="#proximas" type="button">
                Próximas Citas (<?php echo count($proximas_citas); ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button">
                Historial (<?php echo count($historial_citas); ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-4 bg-white shadow-sm rounded-bottom">
        
        <div class="tab-pane fade show active" id="proximas">
            <?php if (empty($proximas_citas)): ?>
                <div class="alert alert-info mt-3"><i class="bi bi-calendar-x"></i> No tienes citas programadas.</div>
            <?php else: ?>
                <?php foreach ($proximas_citas as $cita): 
                    
                    $es_pendiente_pago = ($cita['estado_actual'] == 'Agendada pendiente de pago');
                    $borde_clase = $es_pendiente_pago ? 'border-warning' : 'border-success';
                    $badge_clase = $es_pendiente_pago ? 'bg-warning text-dark' : 'bg-success';
                ?>
                    <div class="card shadow-sm mb-3 border-start border-5 <?php echo $borde_clase; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title text-primary fw-bold mb-1"><?php echo htmlspecialchars($cita['especialidad']); ?></h5>
                                    <p class="mb-1"><strong>Doctor:</strong> <?php echo htmlspecialchars($cita['nombre_doctor']); ?></p>
                                    <p class="mb-1"><i class="bi bi-calendar-event"></i> <?php echo (new DateTime($cita['fecha_cita']))->format('d/m/Y \a \l\a\s H:i'); ?> hrs</p>
                                    <small class="text-muted">Consultorio: <?php echo htmlspecialchars($cita['no_consultorio']); ?></small>
                                </div>
                                <span class="badge <?php echo $badge_clase; ?> p-2">
                                    <?php echo htmlspecialchars($cita['estado_actual']); ?>
                                </span>
                            </div>
                            
                            <hr>

                            <?php if ($es_pendiente_pago): ?>
                                <?php
                                $sql_horas = "SELECT dbo.FN_TiempoRestantePago(?, GETDATE()) AS HorasRestantes";
                                $stmt_horas = $conn->prepare($sql_horas);
                                $stmt_horas->execute([$cita['folio_cita']]);
                                $resultado = $stmt_horas->fetch(PDO::FETCH_ASSOC);
                                $horas = round($resultado['HorasRestantes'], 2); 
                                ?>

                                <?php if ($horas > 0): ?>
                                    <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded mb-3">
                                        <span class="text-danger fw-bold"><i class="bi bi-stopwatch"></i> Tienes <?php echo $horas; ?> horas para pagar</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="/HOSPITAL/pages/pagar_cita.php?folio_cita=<?php echo $cita['folio_cita']; ?>" class="btn btn-success">
                                            <i class="bi bi-credit-card"></i> Pagar Ahora
                                        </a>
                                        <a href="/HOSPITAL/pages/cancelar_cita.php?folio_cita=<?php echo $cita['folio_cita']; ?>" class="btn btn-outline-danger" onclick="return confirm('¿Cancelar cita?');">
                                            Cancelar
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger mb-0">
                                        <i class="bi bi-x-circle"></i> Plazo de pago expirado. La cita se cancelará en breve.
                                    </div>
                                <?php endif; ?>
                            
                            <?php else: ?>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-secondary disabled"><i class="bi bi-check-all"></i> Pagado</button>
                                    <a href="/HOSPITAL/pages/cancelar_cita.php?folio_cita=<?php echo $cita['folio_cita']; ?>" class="btn btn-outline-danger" onclick="return confirm('¿Cancelar cita? Se aplicarán políticas de reembolso.');">
                                        Cancelar Cita
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="historial">
            <?php if (empty($historial_citas)): ?>
                <div class="alert alert-light text-center border mt-3">No hay historial disponible.</div>
            <?php else: ?>
                <div class="list-group">
                <?php foreach ($historial_citas as $cita): ?>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($cita['especialidad']); ?></h6>
                            <small class="text-muted"><?php echo (new DateTime($cita['fecha_cita']))->format('d/m/Y'); ?></small>
                        </div>
                        <p class="mb-1">Dr(a). <?php echo htmlspecialchars($cita['nombre_doctor']); ?></p>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($cita['estado_actual']); ?></span>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<?php 
include '../PLANTILLAS/footer.php';
$conn = null;
?>