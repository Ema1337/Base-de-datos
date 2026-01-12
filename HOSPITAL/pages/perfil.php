<?php
include '../PLANTILLAS/header.php'; 
include '../config/conexion.php';


if (!isset($_SESSION['usuario_correo']) || $_SESSION['usuario_tipo'] != 'Paciente') {
    $_SESSION['error_login'] = "Acceso denegado.";
    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

$id_paciente = 0;
$paciente_data = null;
try {
    $correo_paciente = $_SESSION['usuario_correo'];
    
    $sql_paciente = "SELECT * FROM V_InfoPacientes WHERE correo = ?";
    $stmt_paciente = $conn->prepare($sql_paciente);
    $stmt_paciente->execute([$_SESSION['usuario_correo']]);
    $paciente_data = $stmt_paciente->fetch(PDO::FETCH_ASSOC);

    if ($paciente_data) {
        $id_paciente = $paciente_data['id_paciente'];
    } else {
        die("Error: No se pudieron cargar los datos del paciente.");
    }
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}

$historial = [];
try {
   
    $sql_historial = "SELECT * FROM V_HistorialMedico WHERE id_paciente = ? ORDER BY fecha DESC";
    $stmt_historial = $conn->prepare($sql_historial);
    $stmt_historial->execute([$id_paciente]);
    $historial = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar el historial: " . $e->getMessage());
}

?>

<div class="container mt-5 mb-5">

    <?php
    if (isset($_SESSION['exito_historial'])) {
        echo '<div class="alert alert-success" role="alert">';
        echo htmlspecialchars($_SESSION['exito_historial']);
        echo '</div>';
        unset($_SESSION['exito_historial']);
    }
    if (isset($_SESSION['error_historial'])) {
        echo '<div class="alert alert-danger" role="alert">';
        echo htmlspecialchars($_SESSION['error_historial']);
        echo '</div>';
        unset($_SESSION['error_historial']);
    }
    ?>
    
    <h1 class="display-5 mb-4">Mi Perfil y Mi Historial</h1>

    <div class="row">
        <div class="col-md-5">
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h4 class="mb-0">Mis Datos Personales</h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Nombre:</strong> <?php echo htmlspecialchars($paciente_data['nombre_paciente']); ?></li>
                    <li class="list-group-item"><strong>Correo:</strong> <?php echo htmlspecialchars($paciente_data['correo']); ?></li>
                    <li class="list-group-item"><strong>Teléfono:</strong> <?php echo htmlspecialchars($paciente_data['telefono']); ?></li>
                    <li class="list-group-item"><strong>Nacimiento:</strong> <?php echo htmlspecialchars($paciente_data['nacimiento']); ?></li>
                    <li class="list-group-item"><strong>Tipo Sangre:</strong> <?php echo htmlspecialchars($paciente_data['tipo_sangre']); ?></li>
                    <li class="list-group-item"><strong>Alergias:</strong> <?php echo htmlspecialchars($paciente_data['alergias'] ?? 'Ninguna registrada'); ?></li>
                </ul>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Agregar Registro de Salud</h4>
                </div>
                <div class="card-body">
                    <p>Agrega tus signos vitales para que tu doctor pueda revisarlos.</p>
                    <form action="/HOSPITAL/pages/procesar_historial.php" method="POST">
                        <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="presion_sistolica" class="form-label">Presión Sistólica</label>
                                <input type="number" class="form-control" name="presion_sistolica" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="presion_diastolica" class="form-label">Presión Diastólica</label>
                                <input type="number" class="form-control" name="presion_diastolica" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="peso" class="form-label">Peso (kg)</label>
                                <input type="number" step="0.01" class="form-control" name="peso" placeholder="Ej: 70.5" required>
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="estatura" class="form-label">Estatura (m)</label>
                                <input type="number" step="0.01" class="form-control" name="estatura" placeholder="Ej: 1.75" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="oxigenacion" class="form-label">Oxigenación (%)</label>
                            <input type="number" class="form-control" name="oxigenacion" placeholder="Ej: 98" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Guardar Registro</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="col-md-7">
            <h4 class="text-primary">Historial Médico Completo</h4>
            <hr>

            <?php if (empty($historial)): ?>
                <div class="alert alert-info">No tienes ningún registro en tu historial médico.</div>
            <?php else: ?>
                <?php foreach ($historial as $registro): ?>
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($registro['fecha']); ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($registro['padecimiento']) || !empty($registro['detalles'])): ?>
                                <h5 class="card-title text-primary">Diagnóstico del Doctor</h5>
                                <p class="card-text">
                                    <strong>Padecimiento:</strong> <?php echo htmlspecialchars($registro['padecimiento'] ?? 'N/A'); ?><br>
                                    <strong>Detalles:</strong> <?php echo htmlspecialchars($registro['detalles'] ?? 'N/A'); ?>
                                </p>
                                <hr>
                            <?php endif; ?>

                            <h6 class="card-subtitle mb-2 text-muted">Signos Vitales Registrados</h6>
                            <ul class="list-unstyled">
                                <li><strong>Presión:</strong> <?php echo htmlspecialchars($registro['presion_sistolica'] . '/' . $registro['presion_diastolica']); ?> mmHg</li>
                                <li><strong>Peso:</strong> <?php echo htmlspecialchars($registro['peso']); ?> kg</li>
                                <li><strong>Estatura:</strong> <?php echo htmlspecialchars($registro['estatura']); ?> m</li>
                                <li><strong>Oxigenación:</strong> <?php echo htmlspecialchars($registro['oxigenacion']); ?> %</li>
                                
                                <?php
                                $sql_imc = "SELECT dbo.FN_CalcularIMC(?, ?) AS IMC";
                                $stmt_imc = $conn->prepare($sql_imc);
                               
                                $stmt_imc->execute([$registro['peso'], $registro['estatura']]);
                                $imc = $stmt_imc->fetch(PDO::FETCH_ASSOC);
                                ?>
                                
                                <li class="mt-2"><strong>IMC (Índice de Masa Corporal):</strong> 
                                    <span class="badge bg-info text-dark">
                                        <?php echo round($imc['IMC'], 2); ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php 
include '../PLANTILLAS/footer.php'; 
$conn = null;
?>