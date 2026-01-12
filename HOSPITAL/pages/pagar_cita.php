<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || $_SESSION['usuario_tipo'] != 'Paciente') {
    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

$folio_cita = isset($_GET['folio_cita']) ? $_GET['folio_cita'] : 0;
$error = "";
$cita = null; 
$precio = 0;  

try {
    $sql_cita = "SELECT c.folio_cita, c.fecha_cita, c.id_estado, 
                        doc.no_consultorio, 
                        e.primer_nom_emp + ' ' + e.a_pat_emp AS nombre_doctor,
                        esp.nombre as especialidad
                 FROM CITA c
                 JOIN DOCTOR doc ON c.id_doctor = doc.id_doctor
                 JOIN EMPLEADO e ON doc.id_empleado = e.id_empleado
                 JOIN ESPECIALIDAD esp ON doc.id_especialidad = esp.id_especialidad
                 JOIN PACIENTE p ON c.id_paciente = p.id_paciente
                 WHERE c.folio_cita = ? AND p.correo = ? AND c.id_estado = 1"; 

    $stmt = $conn->prepare($sql_cita);
    $stmt->execute([$folio_cita, $_SESSION['usuario_correo']]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        echo "<script>alert('Cita no encontrada o ya pagada.'); window.location.href='/HOSPITAL/pages/mis_citas.php';</script>";
        exit;
    }

    $precio = 800.00; 
    if (stripos($cita['especialidad'], 'General') !== false) {
        $precio = 450.00; 
    }

} catch (Exception $e) {
    $error = "Error al cargar: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$error) {
    try {
        $fecha_pago = date('Y-m-d H:i:s');
        $sql_pago = "INSERT INTO PAGO (total, fecha, folio_cita) VALUES (?, ?, ?)";
        $stmt_pago = $conn->prepare($sql_pago);
        $stmt_pago->execute([$precio, $fecha_pago, $folio_cita]);

        $_SESSION['exito_cita'] = "¡Pago procesado! Tu cita ha sido confirmada automáticamente.";
        header("Location: /HOSPITAL/pages/mis_citas.php");
        exit;

    } catch (Exception $e) {
        $error = "Falló el procesamiento del pago: " . $e->getMessage();
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0"><i class="bi bi-credit-card-2-front"></i> Pasarela de Pago</h4>
                </div>
                <div class="card-body p-4">

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <a href="/HOSPITAL/pages/mis_citas.php" class="btn btn-secondary w-100">Regresar</a>
                    <?php else: ?>

                        <div class="text-center mb-4">
                            <h6 class="text-muted text-uppercase">Total a Pagar</h6>
                            <h1 class="display-4 fw-bold text-success">$<?php echo number_format($precio, 2); ?></h1>
                            <span class="badge bg-light text-dark border">MXN</span>
                        </div>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-primary"><i class="bi bi-info-circle"></i> Detalles</h6>
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Especialidad:</span>
                                    <strong><?php echo htmlspecialchars($cita['especialidad']); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Doctor:</span>
                                    <strong><?php echo htmlspecialchars($cita['nombre_doctor']); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Consultorio:</span>
                                    <strong><?php echo htmlspecialchars($cita['no_consultorio']); ?></strong>
                                </div>
                            </div>
                        </div>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Titular</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Número de Tarjeta</label>
                                <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required id="cardNumber">
                            </div>
                            <div class="row mb-4">
                                <div class="col-6"><input type="text" class="form-control" placeholder="MM/AA" required></div>
                                <div class="col-6"><input type="text" class="form-control" placeholder="CVC" required></div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">Pagar Ahora</button>
                                <a href="/HOSPITAL/pages/mis_citas.php" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const cardInput = document.getElementById('cardNumber');
    if(cardInput){
        cardInput.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
        });
    }
</script>

<?php include '../PLANTILLAS/footer.php'; ?>