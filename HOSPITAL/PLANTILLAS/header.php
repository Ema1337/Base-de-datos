<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOSPITAL SAN ANGEL INN | Salud y Confianza</title>
    
    <link href="/HOSPITAL/css/style.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
      <div class="container">
        
        <a class="navbar-brand" href="/HOSPITAL/index.php">
          <strong>HOSPITAL SAN ANGEL INN</strong>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link" href="/HOSPITAL/index.php">Inicio</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/HOSPITAL/pages/servicios.php">Servicios</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/HOSPITAL/pages/doctores.php">Doctores</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/HOSPITAL/pages/agendar_cita.php">Citas</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/HOSPITAL/pages/contacto.php">Contacto</a>
            </li>
          </ul>

          <div class="d-flex">
            <?php if (isset($_SESSION['usuario_correo'])): ?>
                
                <?php
                $dashboard_link = "/HOSPITAL/index.php"; 
                $tipo = $_SESSION['usuario_tipo']; 
                if ($tipo == 'Paciente') {
                    $dashboard_link = "/HOSPITAL/pages/dashboard_paciente.php";
                } 
                else if ($tipo == 'Doctor' || $tipo == 'Medico') {
                    $dashboard_link = "/HOSPITAL/pages/dashboard_doctor.php";
                } 
                else if ($tipo == 'Recepcionista' || $tipo == 'Empleado' || $tipo == 'Enfermera') {
                    
                    $dashboard_link = "/HOSPITAL/pages/mi_cuenta.php";
                }
                ?>
                
                <a href="<?php echo $dashboard_link; ?>" class="btn btn-outline-primary me-2">Mi Cuenta</a>
                <a href="/HOSPITAL/pages/logout.php" class="btn btn-danger">Cerrar Sesión</a>

            <?php else: ?>
                
                <a href="/HOSPITAL/pages/login.php" class="btn btn-outline-primary me-2">Iniciar Sesión</a>
                <a href="/HOSPITAL/pages/registro.php" class="btn btn-primary">Registrarse</a>
                
            <?php endif; ?>
          </div>

        </div>
      </div>
    </nav>
</header>

<main>