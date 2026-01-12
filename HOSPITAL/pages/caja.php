<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || 
    !in_array($_SESSION['usuario_tipo'], ['Recepcionista', 'Administrador', 'Empleado'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT c.folio_cita, c.fecha_cita, 
               p.primer_nombre, p.a_paterno, p.correo, 
               doc.primer_nom_emp as doc_nombre, doc.a_pat_emp as doc_ape,
               e.nombre as especialidad
        FROM CITA c
        JOIN PACIENTE p ON c.id_paciente = p.id_paciente
        JOIN DOCTOR d ON c.id_doctor = d.id_doctor
        JOIN EMPLEADO doc ON d.id_empleado = doc.id_empleado
        JOIN ESPECIALIDAD e ON d.id_especialidad = e.id_especialidad
        WHERE c.id_estado = 1
        ORDER BY c.fecha_cita ASC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$citas_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$precio_consulta = 500.00;
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><i class="bi bi-cash-coin"></i> Caja - Cobro de Consultas</h2>
        <a href="mi_cuenta.php" class="btn btn-outline-secondary">Volver al Panel</a>
    </div>

    <?php if (empty($citas_pendientes)): ?>
        <div class="alert alert-success py-4 text-center shadow-sm">
            <i class="bi bi-check-circle-fill fs-1"></i>
            <h4 class="mt-3">Todo al día</h4>
            <p>No hay pacientes con pagos pendientes en este momento.</p>
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="ps-4">Folio</th>
                                <th>Fecha/Hora</th>
                                <th>Paciente</th>
                                <th>Servicio</th>
                                <th>Total</th>
                                <th class="text-end pe-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($citas_pendientes as $c): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= $c['folio_cita'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($c['fecha_cita'])) ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($c['primer_nombre'] . " " . $c['a_paterno']) ?></div>
                                    <small class="text-muted"><i class="bi bi-envelope"></i> <?= htmlspecialchars($c['correo']) ?></small>
                                </td>
                                <td>
                                    Consulta General<br>
                                    <small class="text-muted">Dr. <?= htmlspecialchars($c['doc_nombre']) ?></small>
                                </td>
                                <td class="text-success fw-bold fs-5">$<?= number_format($precio_consulta, 2) ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-success btn-sm px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalCobro<?= $c['folio_cita'] ?>">
                                        <i class="bi bi-credit-card-2-back"></i> Cobrar
                                    </button>

                                    <div class="modal fade text-start" id="modalCobro<?= $c['folio_cita'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="procesar_pago_caja.php" method="POST">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title">Confirmar Pago</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <i class="bi bi-wallet2 text-success" style="font-size: 3rem;"></i>
                                                        <h4 class="mt-3">Total a Pagar</h4>
                                                        <h2 class="fw-bold text-success mb-3">$<?= number_format($precio_consulta, 2) ?></h2>
                                                        
                                                        <div class="alert alert-light border text-start">
                                                            <small class="text-muted d-block">Paciente:</small>
                                                            <strong><?= $c['primer_nombre'] . " " . $c['a_paterno'] ?></strong>
                                                            <small class="text-muted d-block mt-2">Concepto:</small>
                                                            <strong>Consulta - <?= $c['especialidad'] ?></strong>
                                                        </div>

                                                        <input type="hidden" name="folio_cita" value="<?= $c['folio_cita'] ?>">
                                                        <input type="hidden" name="total" value="<?= $precio_consulta ?>">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-success fw-bold">
                                                            <i class="bi bi-printer-fill"></i> Registrar y Ticket
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>