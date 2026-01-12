<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

$sql = "SELECT d.id_doctor, d.cedula_profesional, 
               e.primer_nom_emp, e.a_pat_emp, e.a_mat_emp, 
               esp.nombre as especialidad
        FROM DOCTOR d
        JOIN EMPLEADO e ON d.id_empleado = e.id_empleado
        JOIN ESPECIALIDAD esp ON d.id_especialidad = esp.id_especialidad
        WHERE e.activo = 1"; 
$doctores = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 mb-5">
    <div class="text-center mb-5">
        <h1 class="display-5 text-primary fw-bold">Nuestro Equipo Médico</h1>
        <p class="lead text-muted">Profesionales certificados comprometidos con tu bienestar.</p>
    </div>

    <div class="row g-4">
        <?php foreach ($doctores as $doc): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card shadow border-0 h-100">
                <div class="card-body text-center pt-4">
                    <img src="https://cdn-icons-png.flaticon.com/512/3774/3774299.png" class="rounded-circle mb-3 shadow-sm" width="100" alt="Doctor">
                    <h4 class="card-title fw-bold text-dark">
                        Dr(a). <?= htmlspecialchars($doc['primer_nom_emp'] . " " . $doc['a_pat_emp']) ?>
                    </h4>
                    <span class="badge bg-primary mb-2"><?= htmlspecialchars($doc['especialidad']) ?></span>
                    <p class="text-muted small mb-3">Cédula: <?= htmlspecialchars($doc['cedula_profesional']) ?></p>
                    
                    <div class="d-grid gap-2">
                        <a href="agendar_cita.php" class="btn btn-outline-success">
                            <i class="bi bi-calendar-check"></i> Agendar Cita
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($doctores)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No hay doctores registrados en este momento.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>