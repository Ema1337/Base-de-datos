<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo'])) {
    header("Location: login.php");
    exit;
}

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$historiales = [];

if (!empty($busqueda)) {
    try {
        $sql = "EXEC sp_BuscarHistorialConDoctor ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$busqueda]);
        $historiales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container mt-5 mb-5">
    <h2 class="text-primary mb-4"><i class="bi bi-journal-medical"></i> Historial Clínico</h2>

    <div class="card bg-light shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto"><label class="fw-bold">Buscar Paciente:</label></div>
                <div class="col-md-6">
                    <input type="text" name="busqueda" class="form-control" placeholder="Nombre o ID..." value="<?php echo htmlspecialchars($busqueda); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="ver_historial.php" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($historiales)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered shadow-sm bg-white align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Fecha / Atendió</th> <th>Paciente</th>
                        <th>Diagnóstico</th>
                        <th>Signos Vitales</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historiales as $his): ?>
                        <tr>
                            <td class="text-center" style="width: 150px;">
                                <div class="fw-bold fs-5"><?php echo date("d/m/Y", strtotime($his['fecha'])); ?></div>
                                <hr class="my-1">
                                <small class="text-muted d-block">Dr(a). Atendió:</small>
                                <span class="badge bg-info text-dark text-wrap">
                                    <i class="bi bi-person-workspace"></i> 
                                    <?php echo $his['nombre_doctor']; ?>
                                </span>
                            </td>

                            <td>
                                <div class="fw-bold text-primary text-uppercase">
                                    <?php echo $his['primer_nombre'] . " " . $his['a_paterno']; ?>
                                </div>
                                <small class="text-muted">ID: <?php echo $his['id_paciente']; ?></small>
                            </td>

                            <td>
                                <span class="badge bg-warning text-dark text-wrap text-start">
                                    <?php echo $his['padecimiento']; ?>
                                </span>
                            </td>

                            <td style="min-width: 160px;">
                                <ul class="list-unstyled mb-0 small">
                                    <li><i class="bi bi-heart-pulse"></i> <strong>P.A:</strong> <?php echo $his['presion_sistolica']."/".$his['presion_diastolica']; ?></li>
                                    <li><i class="bi bi-speedometer2"></i> <strong>Peso:</strong> <?php echo $his['peso']; ?> kg</li>
                                    <li><i class="bi bi-lungs"></i> <strong>O2:</strong> <?php echo $his['oxigenacion']; ?>%</li>
                                </ul>
                            </td>

                            <td>
                                <em class="text-muted small">"<?php echo $his['detalles']; ?>"</em>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (!empty($busqueda)): ?>
        <div class="alert alert-warning">No se encontraron registros.</div>
    <?php endif; ?>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>