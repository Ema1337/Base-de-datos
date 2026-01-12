<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || 
    ($_SESSION['usuario_tipo'] != 'Doctor' && $_SESSION['usuario_tipo'] != 'Medico')) {
    header("Location: login.php");
    exit;
}

$correo = $_SESSION['usuario_correo'];
$mensaje = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevo_telefono = $_POST['telefono'];
    $nueva_password = $_POST['contrasenia'];
    
    try {
        $conn->beginTransaction();
        $sqlEmp = "UPDATE EMPLEADO SET telefono = ? WHERE correo = ?";
        $stmtEmp = $conn->prepare($sqlEmp);
        $stmtEmp->execute([$nuevo_telefono, $correo]);

        if (!empty($nueva_password)) {
            $sqlUser = "UPDATE USUARIO SET contrasenia = ? WHERE correo = ?";
            $stmtUser = $conn->prepare($sqlUser);
            $stmtUser->execute([$nueva_password, $correo]);
        }
        $conn->commit();
        $mensaje = "Datos actualizados correctamente.";
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}

$sql = "SELECT 
            e.primer_nom_emp, e.a_pat_emp, e.a_mat_emp, e.telefono, e.curp,
            d.cedula_profesional, d.id_doctor,
            esp.nombre AS nombre_especialidad,
            c.no_consultorio
        FROM EMPLEADO e
        JOIN DOCTOR d ON e.id_empleado = d.id_empleado
        JOIN ESPECIALIDAD esp ON d.id_especialidad = esp.id_especialidad
        JOIN CONSULTORIO c ON d.no_consultorio = c.no_consultorio
        WHERE e.correo = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$correo]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$datos) {
    echo "<div class='container mt-5 alert alert-danger'>Error: No se encontró perfil médico asociado a este usuario.</div>";
    include '../PLANTILLAS/footer.php';
    exit;
}

$nombre_dr = "Dr. " . $datos['primer_nom_emp'] . " " . $datos['a_pat_emp'];
?>

<div class="container mt-5 mb-5">
    
    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> <?= $mensaje ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow border-0 mb-4">
                <div class="card-body text-center pt-5 pb-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/3774/3774299.png" alt="Doctor" class="img-fluid rounded-circle mb-3" style="width: 120px;">
                    <h4 class="fw-bold text-primary"><?= htmlspecialchars($nombre_dr) ?></h4>
                    <h5 class="text-secondary"><?= htmlspecialchars($datos['nombre_especialidad']) ?></h5>
                    <p class="text-muted small mb-4">Cédula: <?= htmlspecialchars($datos['cedula_profesional']) ?></p>
                    
                    <ul class="list-group list-group-flush text-start">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-hospital"></i> Consultorio</span>
                            <span class="fw-bold">#<?= htmlspecialchars($datos['no_consultorio']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-fingerprint"></i> CURP</span>
                            <small><?= htmlspecialchars($datos['curp']) ?></small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-primary">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title fw-bold text-primary mb-1">Mi Agenda de Hoy</h4>
                                <p class="card-text text-muted">Revisa tus pacientes y comienza las consultas.</p>
                            </div>
                            <a href="agenda_doctor.php" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-calendar-event"></i> Ver Citas
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Configuración de Cuenta</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Teléfono de Contacto</label>
                                <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($datos['telefono']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cambiar Contraseña</label>
                                <input type="password" name="contrasenia" class="form-control" placeholder="Dejar vacío para mantener actual">
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-outline-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>