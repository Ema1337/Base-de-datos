<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || !in_array($_SESSION['usuario_tipo'], ['Recepcionista', 'Administrador'])) {
    header("Location: login.php"); exit;
}

if (isset($_POST['accion'])) {
    $folio = $_POST['folio'];
    $accion = $_POST['accion'];
    
    try {
        if ($accion == 'aprobar') {
            $sql = "UPDATE CITA SET id_estado = 3 WHERE folio_cita = ?";
            $msg = "Cancelación autorizada correctamente.";
            
            $conn->query("DELETE FROM PAGO WHERE folio_cita = $folio");
            
        } else {
            $sql = "UPDATE CITA SET id_estado = 2 WHERE folio_cita = ?";
            $msg = "Solicitud rechazada. La cita sigue activa.";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$folio]);
        echo "<div class='alert alert-success'>$msg</div>";
        
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

$sql = "SELECT c.folio_cita, c.fecha_cita, 
               p.primer_nombre, p.a_paterno,
               d_emp.primer_nom_emp as doc_nombre, d_emp.a_pat_emp as doc_ape
        FROM CITA c
        JOIN PACIENTE p ON c.id_paciente = p.id_paciente
        JOIN DOCTOR doc ON c.id_doctor = doc.id_doctor
        JOIN EMPLEADO d_emp ON doc.id_empleado = d_emp.id_empleado
        WHERE c.id_estado = 5"; 
$solicitudes = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h3 class="text-danger"><i class="bi bi-exclamation-circle"></i> Autorizar Cancelaciones</h3>
    <p>Los médicos han solicitado cancelar las siguientes citas:</p>

    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info">No hay solicitudes pendientes.</div>
    <?php else: ?>
        <table class="table table-bordered bg-white">
            <thead class="table-dark">
                <tr><th>Folio</th><th>Fecha</th><th>Paciente</th><th>Médico Solicitante</th><th>Acción</th></tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $s): ?>
                <tr>
                    <td><?= $s['folio_cita'] ?></td>
                    <td><?= $s['fecha_cita'] ?></td>
                    <td><?= $s['primer_nombre'] . ' ' . $s['a_paterno'] ?></td>
                    <td>Dr. <?= $s['doc_nombre'] . ' ' . $s['doc_ape'] ?></td>
                    <td>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="folio" value="<?= $s['folio_cita'] ?>">
                            <button type="submit" name="accion" value="aprobar" class="btn btn-success btn-sm">Autorizar</button>
                            <button type="submit" name="accion" value="rechazar" class="btn btn-danger btn-sm">Rechazar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="mi_cuenta.php" class="btn btn-secondary">Volver</a>
</div>
<?php include '../PLANTILLAS/footer.php'; ?>