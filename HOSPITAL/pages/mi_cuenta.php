<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo'])) {
    header("Location: login.php");
    exit;
}

$correo = $_SESSION['usuario_correo'];
$tipo_usuario = $_SESSION['usuario_tipo'];
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
        $mensaje = "¡Tus datos han sido actualizados correctamente!";
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Error al actualizar: " . $e->getMessage();
    }
}

$datos = [];

if ($tipo_usuario == 'Empleado' || $tipo_usuario == 'Recepcionista' || $tipo_usuario == 'Medico' || $tipo_usuario == 'Enfermera' || $tipo_usuario == 'Farmaceutico') {
    $sql = "SELECT * FROM EMPLEADO WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$correo]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $nombre_completo = $datos['primer_nom_emp'] . " " . $datos['a_pat_emp'];
} else {
    $sql = "SELECT * FROM PACIENTE WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$correo]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre_completo = $datos['primer_nombre'] . " " . $datos['a_paterno'];
}
?>

<div class="container mt-5 mb-5">
    
    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> <?= $mensaje ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm text-center p-4 mb-4">
                <div class="mb-3">
                    <i class="bi bi-person-circle text-secondary" style="font-size: 5rem;"></i>
                </div>
                <h4 class="fw-bold"><?= htmlspecialchars($nombre_completo) ?></h4>
                <p class="text-muted mb-1"><?= htmlspecialchars($correo) ?></p>
                <span class="badge bg-primary fs-6"><?= htmlspecialchars($tipo_usuario) ?></span>
                
                <?php if (isset($datos['curp'])): ?>
                    <hr>
                    <small class="text-muted d-block">CURP</small>
                    <strong><?= htmlspecialchars($datos['curp']) ?></strong>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8">
            
            <?php if ($tipo_usuario == 'Recepcionista' || $tipo_usuario == 'Empleado' || $tipo_usuario == 'Administrador'): ?>
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-grid-fill"></i> Panel de Recepción</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        
                        <div class="col-md-6">
                            <a href="ver_solicitudes.php" class="btn btn-outline-primary w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-person-lines-fill fs-2 mb-2"></i>
                                <span class="fw-bold">Solicitudes de Empleo</span>
                                <small class="text-muted">Aprobar contrataciones</small>
                            </a>
                        </div>
                        
                        <div class="col-md-6">
                            <a href="gestionar_citas.php" class="btn btn-outline-success w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-calendar-check fs-2 mb-2"></i>
                                <span class="fw-bold">Gestionar Citas</span>
                                <small class="text-muted">Ver agenda del día</small>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="registro.php" class="btn btn-outline-secondary w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-person-plus-fill fs-2 mb-2"></i>
                                <span class="fw-bold">Nuevo Paciente</span>
                                <small class="text-muted">Registro manual</small>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="caja.php" class="btn btn-outline-warning text-dark w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-cash-coin fs-2 mb-2"></i>
                                <span class="fw-bold">Caja / Cobros</span>
                                <small class="text-muted">Cobrar consultas pendientes</small>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="ver_historial.php" class="btn btn-outline-info text-dark w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-journal-medical fs-2 mb-2"></i>
                                <span class="fw-bold">Historial Clínico</span>
                                <small class="text-muted">Buscar expedientes</small>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="autorizar_cancelaciones.php" class="btn btn-outline-danger w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-exclamation-octagon fs-2 mb-2"></i>
                                <span class="fw-bold">Autorizar Cancelaciones</span>
                                <small class="text-muted">Solicitudes de doctores</small>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="gestion_empleados.php" class="btn btn-outline-dark w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-people-fill fs-2 mb-2"></i>
                                <span class="fw-bold">Gestión de Personal</span>
                                <small class="text-muted">Dar de baja empleados</small>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="bitacora.php" class="btn btn-outline-secondary text-dark w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-eye-fill fs-2 mb-2"></i>
                                <span class="fw-bold">Ver Bitácora</span>
                                <small class="text-muted">Auditoría del sistema</small>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Mis Datos</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="mi_cuenta.php">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre(s)</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($datos['primer_nom_emp'] ?? $datos['primer_nombre']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars(($datos['a_pat_emp'] ?? $datos['a_paterno']) . ' ' . ($datos['a_mat_emp'] ?? $datos['a_materno'])) ?>" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Teléfono de Contacto</label>
                                <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($datos['telefono']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" name="contrasenia" class="form-control" placeholder="Dejar en blanco para no cambiar">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>