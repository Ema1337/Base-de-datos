<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || 
    !in_array($_SESSION['usuario_tipo'], ['Recepcionista', 'Empleado', 'Administrador'])) {
    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

$sql = "SELECT * FROM SOLICITUD_EMPLEADO WHERE id_estado = 1 ORDER BY fecha_solicitud DESC";
$stmt = $conn->query($sql);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$especialidades = [];
$consultorios = [];

if (!empty($solicitudes)) {
    $especialidades = $conn->query("SELECT * FROM ESPECIALIDAD")->fetchAll(PDO::FETCH_ASSOC);
    $consultorios = $conn->query("SELECT * FROM CONSULTORIO")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container mt-5 mb-5">
    <h3><i class="bi bi-person-lines-fill"></i> Solicitudes de Empleo Pendientes</h3>
    <hr>

    <?php if (isset($_SESSION['exito_solicitud'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['exito_solicitud']); unset($_SESSION['exito_solicitud']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_solicitud'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error_solicitud']); unset($_SESSION['error_solicitud']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info">
            <i class="bi bi-inbox"></i> No hay solicitudes pendientes por revisar en este momento.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered shadow-sm bg-white align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Rol Solicitado</th>
                        <th>Datos de Contacto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($solicitudes as $sol): ?>
                        <tr>
                            <td><?php echo (new DateTime($sol['fecha_solicitud']))->format('d/m/Y'); ?></td>
                            <td class="fw-bold">
                                <?php echo htmlspecialchars($sol['primer_nombre'] . ' ' . $sol['a_paterno']); ?>
                            </td>
                            <td>
                                <span class="badge bg-primary text-white p-2">
                                    <?php echo htmlspecialchars($sol['rol_solicitado']); ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="bi bi-person-vcard"></i> <?php echo htmlspecialchars($sol['curp']); ?><br>
                                    <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($sol['correo']); ?>
                                </small>
                            </td>
                            <td>
                                <button type="button" class="btn btn-success btn-sm me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalAprobar"
                                        data-id="<?php echo $sol['id_solicitud']; ?>"
                                        data-rol="<?php echo $sol['rol_solicitado']; ?>"
                                        data-nombre="<?php echo $sol['primer_nombre'] . ' ' . $sol['a_paterno']; ?>">
                                    <i class="bi bi-check-circle"></i> Aprobar
                                </button>
                                
                                <form action="/HOSPITAL/pages/procesar_aprobacion.php" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de RECHAZAR y eliminar esta solicitud?');">
                                    <input type="hidden" name="id_solicitud" value="<?= $sol['id_solicitud'] ?>">
                                    <input type="hidden" name="accion" value="rechazar">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalAprobar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/HOSPITAL/pages/procesar_aprobacion.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-person-check-fill"></i> Contratar Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" name="id_solicitud" id="modal_id_solicitud">
                    <input type="hidden" name="rol_detectado" id="modal_rol">
                    <input type="hidden" name="accion" value="aprobar">
                    
                    <div class="alert alert-light border text-center">
                        <p class="mb-1">Estás aprobando a:</p>
                        <h4 id="modal_nombre_empleado" class="text-success fw-bold"></h4>
                        <span class="badge bg-secondary" id="modal_rol_texto"></span>
                    </div>

                    <div id="campos_doctor" style="display:none;" class="bg-light p-3 rounded border mt-3">
                        <h6 class="text-primary border-bottom pb-2 mb-3">Datos Clínicos Requeridos</h6>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cédula Profesional</label>
                            <input type="text" name="cedula" class="form-control" placeholder="Ej. 12345678">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Especialidad</label>
                            <select name="id_especialidad" class="form-select">
                                <option value="">Seleccione...</option>
                                <?php foreach ($especialidades as $esp): ?>
                                    <option value="<?php echo $esp['id_especialidad']; ?>">
                                        <?php echo htmlspecialchars($esp['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Consultorio Asignado</label>
                            <select name="no_consultorio" class="form-select">
                                <option value="">Seleccione...</option>
                                <?php foreach ($consultorios as $cons): ?>
                                    <option value="<?php echo $cons['no_consultorio']; ?>">
                                        <?php echo htmlspecialchars($cons['tipo'] . ' #' . $cons['no_consultorio']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                        <div>
                            <small>Al confirmar, el sistema creará automáticamente el <strong>Usuario</strong> y <strong>Contraseña</strong> del empleado.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold">Confirmar Contratación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var modalAprobar = document.getElementById('modalAprobar');
    modalAprobar.addEventListener('show.bs.modal', function (event) {

        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var rol = button.getAttribute('data-rol'); 
        var nombre = button.getAttribute('data-nombre');

        document.getElementById('modal_id_solicitud').value = id;
        document.getElementById('modal_rol').value = rol;
        document.getElementById('modal_nombre_empleado').textContent = nombre;
        document.getElementById('modal_rol_texto').textContent = rol;

        var divDoctor = document.getElementById('campos_doctor');
        var inputsDoctor = divDoctor.querySelectorAll('input, select');

        var rolLower = rol.toLowerCase();

        if (rolLower.includes('medico') || rolLower.includes('médico') || rolLower.includes('doctor')) {
            divDoctor.style.display = 'block';
            inputsDoctor.forEach(function(input) { input.required = true; });
        } else {
            divDoctor.style.display = 'none';
            inputsDoctor.forEach(function(input) { input.required = false; });
        }
    });
</script>

<?php include '../PLANTILLAS/footer.php'; ?>