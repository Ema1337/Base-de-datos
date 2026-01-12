<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo'])) {
    header("Location: login.php");
    exit;
}

$fecha_filtro = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$sql = "SELECT 
            c.folio_cita, 
            c.fecha_cita, 
            c.fecha_fin_cita,
            p.primer_nombre AS p_nom, p.a_paterno AS p_ape, p.telefono AS p_tel,
            doc_emp.primer_nom_emp AS d_nom, doc_emp.a_pat_emp AS d_ape,
            cons.no_consultorio,
            ec.estado, ec.id_estado
        FROM CITA c
        JOIN PACIENTE p ON c.id_paciente = p.id_paciente
        JOIN DOCTOR d ON c.id_doctor = d.id_doctor
        JOIN EMPLEADO doc_emp ON d.id_empleado = doc_emp.id_empleado
        JOIN CONSULTORIO cons ON c.no_consultorio = cons.no_consultorio
        JOIN ESTADO_CITA ec ON c.id_estado = ec.id_estado
        WHERE CAST(c.fecha_cita AS DATE) = ?
        ORDER BY c.fecha_cita ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([$fecha_filtro]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><i class="bi bi-calendar-week"></i> Gestión de Citas</h2>
        <a href="mi_cuenta.php" class="btn btn-outline-secondary">Volver al Panel</a>
    </div>

    <div class="card shadow-sm mb-4 bg-light">
        <div class="card-body py-3">
            <form action="gestionar_citas.php" method="GET" class="row g-3 align-items-end">
                <div class="col-auto"><label class="fw-bold">Ver agenda del día:</label></div>
                <div class="col-auto"><input type="date" name="fecha" class="form-control" value="<?php echo $fecha_filtro; ?>"></div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="gestionar_citas.php?fecha=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-primary">Hoy</a>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($citas)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-calendar-x fs-1"></i><br>
            No hay citas programadas para el <strong><?php echo date('d/m/Y', strtotime($fecha_filtro)); ?></strong>.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered shadow-sm bg-white align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Doctor / Consultorio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($citas as $cita): ?>
                        <?php 
                            $hora = date('H:i', strtotime($cita['fecha_cita']));
                            
                            $badgeColor = 'bg-secondary';
                            if (stripos($cita['estado'], 'Confirmada') !== false) $badgeColor = 'bg-primary';
                            if (stripos($cita['estado'], 'Completada') !== false || stripos($cita['estado'], 'Atendida') !== false) $badgeColor = 'bg-success';
                            if (stripos($cita['estado'], 'Pagada') !== false) $badgeColor = 'bg-warning text-dark';
               
                            if (stripos($cita['estado'], 'Cancelada') !== false) $badgeColor = 'bg-danger';
                        ?>
                        <tr>
                            <td class="text-center fw-bold fs-5"><?php echo $hora; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo $cita['p_nom'] . " " . $cita['p_ape']; ?></div>
                                <small class="text-muted"><i class="bi bi-telephone"></i> <?php echo $cita['p_tel']; ?></small>
                            </td>
                            <td>
                                <div>Dr(a). <?php echo $cita['d_nom'] . " " . $cita['d_ape']; ?></div>
                                <small class="text-primary fw-bold">Consultorio #<?php echo $cita['no_consultorio']; ?></small>
                            </td>
                            <td class="text-center">
                                <span class="badge <?php echo $badgeColor; ?> p-2">
                                    <?php echo $cita['estado']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php 
                                $estados_finales = [
                                    'Cancelada',
                                    'Cancelada Falta de pago',
                                    'Cancelada Paciente', 
                                    'Cancelada Recepcionista', 
                                    'Completada',
                                    'Atendida'
                                ];

                                if (!in_array($cita['estado'], $estados_finales)): 
                                ?>
                                    <a href="procesar_gestion_citas.php?accion=confirmar&folio=<?php echo $cita['folio_cita']; ?>&fecha_actual=<?php echo $fecha_filtro; ?>" 
                                       class="btn btn-sm btn-success mb-1" title="Confirmar Asistencia">
                                       <i class="bi bi-check-lg"></i>
                                    </a>

                                    <a href="procesar_gestion_citas.php?accion=cancelar&folio=<?php echo $cita['folio_cita']; ?>&fecha_actual=<?php echo $fecha_filtro; ?>" 
                                       class="btn btn-sm btn-danger mb-1" title="Cancelar Cita"
                                       onclick="return confirm('¿Seguro que deseas cancelar esta cita como recepcionista?');">
                                       <i class="bi bi-x-lg"></i>
                                    </a>

                                <?php else: ?>
                                    <small class="text-muted fst-italic">
                                        <i class="bi bi-lock-fill"></i> Cerrada
                                    </small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>