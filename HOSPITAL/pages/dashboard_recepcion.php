<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || 
    !in_array($_SESSION['usuario_tipo'], ['Recepcionista', 'Empleado'])) {
    
    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

try {
    $sql_solicitudes = "SELECT COUNT(*) as total FROM SOLICITUD_EMPLEADO WHERE id_estado = 1";
    $stmt1 = $conn->query($sql_solicitudes);
    $pendientes = $stmt1->fetch(PDO::FETCH_ASSOC)['total'];

    $hoy = date('Y-m-d');
    $sql_citas = "SELECT COUNT(*) as total FROM CITA WHERE CAST(fecha_cita AS DATE) = '$hoy' AND id_estado = 2";
    $stmt2 = $conn->query($sql_citas);
    $citas_hoy = $stmt2->fetch(PDO::FETCH_ASSOC)['total'];

} catch (Exception $e) {
    $pendientes = 0;
    $citas_hoy = 0;
}
?>

<div class="container mt-5">
    <h2 class="mb-4">Panel de Recepción</h2>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3 shadow">
                <div class="card-header fw-bold"><i class="bi bi-person-lines-fill"></i> Recursos Humanos</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $pendientes; ?> Solicitudes Nuevas</h5>
                    <p class="card-text">Médicos/Enfermeras esperando aprobación.</p>
                    <a href="/HOSPITAL/pages/ver_solicitudes.php" class="btn btn-light btn-sm text-dark fw-bold">Gestionar</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3 shadow">
                <div class="card-header fw-bold"><i class="bi bi-calendar-event"></i> Agenda de Hoy</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $citas_hoy; ?> Citas Programadas</h5>
                    <p class="card-text">Pacientes confirmados para hoy.</p>
                    <button class="btn btn-light btn-sm text-primary fw-bold" disabled>Ver Agenda (Próximamente)</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>