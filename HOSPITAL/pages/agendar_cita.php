<?php
include '../PLANTILLAS/header.php'; 
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || $_SESSION['usuario_tipo'] != 'Paciente') {
    $_SESSION['error_login'] = "Acceso denegado.";
    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

$id_paciente = 0;
try {
    $stmt_paciente_id = $conn->prepare("SELECT id_paciente FROM PACIENTE WHERE correo = ?");
    $stmt_paciente_id->execute([$_SESSION['usuario_correo']]);
    $paciente = $stmt_paciente_id->fetch(PDO::FETCH_ASSOC);
    if ($paciente) $id_paciente = $paciente['id_paciente'];
} catch (PDOException $e) { /* ... */ }
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <?php
            if (isset($_SESSION['exito_cita'])) {
                echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['exito_cita']) . '</div>';
                unset($_SESSION['exito_cita']);
            }
            if (isset($_SESSION['error_cita'])) {
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['error_cita']) . '</div>';
                unset($_SESSION['error_cita']);
            }
            ?>
            
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center mb-0">Agendar Nueva Cita</h3>
                </div>
                <div class="card-body p-4">
                    
                    <form action="/HOSPITAL/pages/procesar_cita.php" method="POST" id="formCita">
                        
                        <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                        <input type="hidden" name="hora_cita" id="hora_cita_seleccionada">

                        <div class="mb-3">
                            <label for="id_especialidad" class="form-label">Especialidad</label>
                            <select class="form-select" id="id_especialidad" name="id_especialidad" required>
                                <option value="">Seleccione una especialidad...</option>
                                <?php
                                try {
                                    $sql_especialidades = "SELECT * FROM ESPECIALIDAD ORDER BY nombre";
                                    foreach ($conn->query($sql_especialidades) as $fila) {
                                        echo "<option value='" . htmlspecialchars($fila['id_especialidad']) . "'>" 
                                             . htmlspecialchars($fila['nombre']) 
                                             . "</option>";
                                    }
                                } catch (PDOException $e) { echo "<option>Error</option>"; }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_doctor" class="form-label">Doctor</label>
                            <select class="form-select" id="id_doctor" name="id_doctor" required disabled>
                                <option value="">Seleccione especialidad...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_cita" class="form-label">Fecha de la Cita</label>
                            <input type="date" class="form-control" id="fecha_cita" name="fecha_cita" required disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Horarios Disponibles</label>
                            <div id="lista-horarios" class="border p-3 rounded" style="min-height: 50px;">
                                <small class="text-muted">Seleccione un doctor y una fecha...</small>
                            </div>
                            <div id="error-horarios" class="text-danger mt-2"></div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnConfirmarCita" disabled>Confirmar Cita</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include '../PLANTILLAS/footer.php'; 
$conn = null;
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const selectEspecialidad = document.getElementById('id_especialidad');
    const selectDoctor = document.getElementById('id_doctor');
    const inputFecha = document.getElementById('fecha_cita');
    const divHorarios = document.getElementById('lista-horarios');
    const divErrorHorarios = document.getElementById('error-horarios');
    const inputHoraSeleccionada = document.getElementById('hora_cita_seleccionada');
    const btnConfirmar = document.getElementById('btnConfirmarCita');

    const today = new Date();
    const minDate = new Date();
    minDate.setDate(today.getDate() + 2);
    const minDateStr = minDate.toISOString().split('T')[0];
    const maxDate = new Date();
    maxDate.setMonth(today.getMonth() + 3);
    const maxDateStr = maxDate.toISOString().split('T')[0];
    inputFecha.setAttribute('min', minDateStr);
    inputFecha.setAttribute('max', maxDateStr);

    selectEspecialidad.addEventListener('change', function() {
        const especialidadId = this.value;
        selectDoctor.innerHTML = '<option value="">Cargando...</option>';
        inputFecha.disabled = true;
        divHorarios.innerHTML = '<small class="text-muted">...</small>';

        if (especialidadId) {
            fetch('/HOSPITAL/pages/api_doctores.php?id_especialidad=' + especialidadId)
                .then(response => response.json())
                .then(data => {
                    selectDoctor.innerHTML = '<option value="">Seleccione un doctor...</option>';
                    data.forEach(doc => {
                        selectDoctor.innerHTML += `<option value="${doc.id}">${doc.nombre}</option>`;
                    });
                    selectDoctor.disabled = false;
                });
        } else {
            selectDoctor.innerHTML = '<option value="">Seleccione especialidad...</option>';
            selectDoctor.disabled = true;
        }
    });

    function buscarHorarios() {
        const doctorId = selectDoctor.value;
        const fecha = inputFecha.value;
        
        divHorarios.innerHTML = '<small class="text-muted">Cargando horarios...</small>';
        divErrorHorarios.textContent = '';
        btnConfirmar.disabled = true;
        inputHoraSeleccionada.value = '';

        if (!doctorId || !fecha) return;

        fetch(`/HOSPITAL/pages/api_horarios.php?id_doctor=${doctorId}&fecha=${fecha}`)
            .then(response => response.json())
            .then(data => {
                divHorarios.innerHTML = ''; 
                
                if (data.error) {
                    divErrorHorarios.textContent = 'Error: ' + data.error;
                    return;
                }
                
                if (data.length === 0) {
                    divHorarios.innerHTML = '<small class="text-muted">No hay horarios disponibles para este día.</small>';
                    return;
                }

                data.forEach(hora => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-primary btn-sm m-1';
                    btn.textContent = hora;
                    btn.dataset.hora = hora;
                    divHorarios.appendChild(btn);
                });
            });
    }

    selectDoctor.addEventListener('change', function() {
        if(this.value) {
            inputFecha.disabled = false;
        } else {
            inputFecha.disabled = true;
        }
        buscarHorarios();
    });

    inputFecha.addEventListener('change', buscarHorarios);

    divHorarios.addEventListener('click', function(e) {
        if (e.target.tagName === 'BUTTON') {
            divHorarios.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');
            inputHoraSeleccionada.value = e.target.dataset.hora;
            btnConfirmar.disabled = false;
        }
    });
});
</script>