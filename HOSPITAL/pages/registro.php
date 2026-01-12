<?php 
include '../PLANTILLAS/header.php'; 
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8"> 
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">Crear Cuenta</h3>
                </div>
                <div class="card-body p-4">

                    <?php
                    if (isset($_SESSION['error_registro'])) {
                        echo '<div class="alert alert-danger alert-dismissible fade show">';
                        echo htmlspecialchars($_SESSION['error_registro']);
                        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                        echo '</div>';
                        unset($_SESSION['error_registro']);
                    }
                    if (isset($_SESSION['exito_registro'])) {
                        echo '<div class="alert alert-success alert-dismissible fade show">';
                        echo htmlspecialchars($_SESSION['exito_registro']);
                        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                        echo '</div>';
                        unset($_SESSION['exito_registro']);
                    }
                    ?>

                    <ul class="nav nav-pills nav-fill mb-4" id="registroTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="paciente-tab" data-bs-toggle="pill" data-bs-target="#form-paciente" type="button" role="tab">
                                <i class="bi bi-person"></i> Soy Paciente
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="empleado-tab" data-bs-toggle="pill" data-bs-target="#form-empleado" type="button" role="tab">
                                <i class="bi bi-briefcase"></i> Soy Médico/Enfermera
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="registroTabContent">
                        
                        <div class="tab-pane fade show active" id="form-paciente" role="tabpanel">
                            <div class="alert alert-info py-2 mb-3">
                                <small><i class="bi bi-info-circle"></i> Crea tu cuenta para agendar citas de inmediato.</small>
                            </div>
                            
                            <form action="/HOSPITAL/pages/procesar_registro.php" method="POST">
                                <input type="hidden" name="tipo_registro" value="paciente">
                                
                                <h5 class="text-primary mt-3">Datos de Acceso</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" name="correo" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" name="contrasenia" required>
                                    </div>
                                </div>

                                <h5 class="text-primary mt-3">Datos Personales</h5>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Primer Nombre</label>
                                        <input type="text" class="form-control" name="primer_nombre" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Seg. Nombre</label>
                                        <input type="text" class="form-control" name="segundo_nombre">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Ap. Paterno</label>
                                        <input type="text" class="form-control" name="a_paterno" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Ap. Materno</label>
                                        <input type="text" class="form-control" name="a_materno">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" name="nacimiento" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" name="telefono" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Sexo</label>
                                        <select class="form-select" name="sexo" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="Femenino">Femenino</option>
                                            <option value="Masculino">Masculino</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tipo Sangre</label>
                                        <select class="form-select" name="tipo_sangre" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Donante</label>
                                        <select class="form-select" name="donante" required>
                                            <option value="0">No</option>
                                            <option value="1">Sí</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Alergias (Opcional)</label>
                                    <input type="text" class="form-control" name="alergias">
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">Registrar Paciente</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="form-empleado" role="tabpanel">
                            
                            <div class="alert alert-warning py-2 mb-3">
                                <small><strong>Nota:</strong> Tu registro quedará como <u>Pendiente</u>. Una enfermera o administrador debe aprobar tu solicitud antes de que puedas iniciar sesión.</small>
                            </div>

                            <form action="/HOSPITAL/pages/procesar_registro.php" method="POST">
                                <input type="hidden" name="tipo_registro" value="empleado">

                                <h5 class="text-primary mt-3">Datos Laborales</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Puesto Solicitado</label>
                                        <select class="form-select" name="rol_solicitado" required>
                                            <option value="">Seleccionar Rol...</option>
                                            <option value="Medico">Médico</option>
                                            <option value="Enfermera">Enfermera</option>
                                            <option value="Farmaceutico">Farmacéutico</option>
                                            <option value="Recepcionista">Recepcionista</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">CURP</label>
                                        <input type="text" class="form-control" name="curp" required maxlength="18" style="text-transform: uppercase;">
                                    </div>
                                </div>

                                <h5 class="text-primary mt-3">Datos de Acceso</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Correo Institucional/Personal</label>
                                        <input type="email" class="form-control" name="correo" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" name="contrasenia" required>
                                    </div>
                                </div>

                                <h5 class="text-primary mt-3">Datos Personales</h5>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Nombre(s)</label>
                                        <input type="text" class="form-control" name="primer_nombre" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Seg. Nombre</label>
                                        <input type="text" class="form-control" name="segundo_nombre">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Ap. Paterno</label>
                                        <input type="text" class="form-control" name="a_paterno" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Ap. Materno</label>
                                        <input type="text" class="form-control" name="a_materno">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Género</label>
                                        <select class="form-select" name="sexo" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="Femenino">Femenino</option>
                                            <option value="Masculino">Masculino</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono Móvil</label>
                                        <input type="tel" class="form-control" name="telefono" required>
                                    </div>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-warning text-dark fw-bold btn-lg">Enviar Solicitud de Registro</button>
                                </div>
                            </form>
                        </div>
                        
                    </div> 
                    <div class="text-center mt-4">
                        <p>¿Ya tienes una cuenta? <a href="/HOSPITAL/pages/login.php">Inicia Sesión</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include '../PLANTILLAS/footer.php'; 
?>