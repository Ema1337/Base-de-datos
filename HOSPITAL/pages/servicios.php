<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

$sqlEsp = "SELECT * FROM ESPECIALIDAD";
$especialidades = $conn->query($sqlEsp)->fetchAll(PDO::FETCH_ASSOC);

$sqlServ = "SELECT * FROM SERVICIO";
$servicios = $conn->query($sqlServ)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 mb-5">
    <div class="text-center mb-5">
        <h1 class="display-5 text-primary fw-bold">Nuestros Servicios</h1>
        <p class="lead text-muted">Atención integral para tu salud con los mejores especialistas.</p>
    </div>

    <h3 class="border-bottom pb-2 mb-4 text-secondary"><i class="bi bi-heart-pulse"></i> Especialidades Médicas</h3>
    <div class="row g-4 mb-5">
        <?php foreach ($especialidades as $esp): ?>
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0 hover-card">
                <div class="card-body text-center">
                    <div class="mb-3 text-primary">
                        <i class="bi bi-hospital fs-1"></i>
                    </div>
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($esp['nombre']) ?></h5>
                    <p class="card-text small text-muted">Consulta Especializada</p>
                    <a href="agendar_cita.php" class="btn btn-outline-primary btn-sm rounded-pill mt-2">Agendar</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($servicios)): ?>
    <h3 class="border-bottom pb-2 mb-4 text-secondary"><i class="bi bi-bandaid"></i> Servicios Clínicos y Enfermería</h3>
    <div class="row g-4">
        <?php foreach ($servicios as $serv): ?>
        <div class="col-md-6">
            <div class="card border-start border-4 border-success shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($serv['nombre']) ?></h5>
                        <p class="mb-0 text-muted small"><?= htmlspecialchars($serv['detalles']) ?></p>
                    </div>
                    <span class="fs-5 fw-bold text-success">$<?= number_format($serv['precio'], 2) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<style>
    .hover-card:hover { transform: translateY(-5px); transition: 0.3s; }
</style>

<?php include '../PLANTILLAS/footer.php'; ?>