<?php
include '../PLANTILLAS/header.php'; 
include '../config/conexion.php';


if (!isset($_SESSION['usuario_correo']) || $_SESSION['usuario_tipo'] != 'Paciente') {
    
    $_SESSION['error_login'] = "Acceso denegado. Debes iniciar sesión como paciente.";

    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

$nombre_paciente = "Paciente"; 
$correo_paciente = $_SESSION['usuario_correo'];

try {
    $sql_nombre = "SELECT primer_nombre, a_paterno FROM PACIENTE WHERE correo = ?";
    $sentencia_nombre = $conn->prepare($sql_nombre);
    $sentencia_nombre->execute([$correo_paciente]);
    
    $paciente = $sentencia_nombre->fetch(PDO::FETCH_ASSOC);

    if ($paciente) {
        $nombre_paciente = htmlspecialchars($paciente['primer_nombre']) . ' ' . htmlspecialchars($paciente['a_paterno']);
    }

} catch (PDOException $e) {
}

?>

<div class="container mt-5 mb-5">
    
    <h1 class="display-5">Bienvenido, <?php echo $nombre_paciente; ?></h1>
    <p class="lead">Desde aquí puedes administrar tus citas y tu perfil.</p>
    <hr>

    <div class="row mt-4">

        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm text-center">
                <div class="card-body">
                    <h4 class="card-title">Agendar Nueva Cita</h4>
                    <p class="card-text">Busca un doctor por especialidad y elige un horario disponible.</p>
                    <a href="/HOSPITAL/pages/agendar_cita.php" class="btn btn-primary">Agendar Ahora</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm text-center">
                <div class="card-body">
                    <h4 class="card-title">Mis Citas</h4>
                    <p class="card-text">Revisa tus próximas citas programadas y tu historial de citas.</p>
                    <a href="/HOSPITAL/pages/mis_citas.php" class="btn btn-outline-secondary">Ver Mis Citas</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm text-center">
                <div class="card-body">
                    <h4 class="card-title">Mi Perfil</h4>
                    <p class="card-text">Actualiza tu información personal, datos de contacto y alergias.</p>
                    <a href="/HOSPITAL/pages/perfil.php" class="btn btn-outline-secondary">Editar Perfil</a>
                </div>
            </div>
        </div>

    </div> 
</div>

<?php 
include '../PLANTILLAS/footer.php'; 

$conn = null;
?>