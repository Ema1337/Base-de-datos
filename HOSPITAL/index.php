<?php 
include 'PLANTILLAS/header.php'; 
?>

<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">

    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>

    <div class="carousel-inner">
        
        <div class="carousel-item active" data-bs-interval="5000">
            <img src="img/slider1.jpg" class="d-block w-100" alt="Fachada del Hospital">
            <div class="carousel-caption d-none d-md-block text-shadow">
                <h1 class="display-4">Cuidamos de ti y tu familia</h1>
                <p class="lead">Expertos en salud dedicados a tu bienestar.</p>
                <a href="/HOSPITAL/pages/agendar_cita.php" class="btn btn-light btn-lg">Agenda tu Cita</a>
            </div>
        </div>

        <div class="carousel-item" data-bs-interval="5000">
            <img src="img/slider2.jpg" class="d-block w-100" alt="Equipo Médico">
            <div class="carousel-caption d-none d-md-block text-shadow">
                <h2>Tecnología de Punta</h2>
                <p>Equipos de última generación para diagnósticos precisos.</p>
                <a href="/HOSPITAL/pages/servicios.php" class="btn btn-primary btn-lg">Ver Servicios</a>
            </div>
        </div>

        <div class="carousel-item" data-bs-interval="5000">
            <img src="img/slider3.jpg" class="d-block w-100" alt="Atención al Paciente">
            <div class="carousel-caption d-none d-md-block text-shadow">
                <h2>Atención Humana y Cálida</h2>
                <p>Tu bienestar es nuestra prioridad en todo momento.</p>
                <a href="/HOSPITAL/pages/contacto.php" class="btn btn-primary btn-lg">Contáctanos</a>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide-to="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide-to="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>

<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4">Nuestros Servicios</h2>
    
    <div class="row">
        
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Cardiología</h5>
                    <p class="card-text">Cuidado integral de tu corazón con la última tecnología.</p>
                    <a href="/HOSPITAL/pages/servicios.php#cardiologia" class="btn btn-outline-primary">Ver más</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Pediatría</h5>
                    <p class="card-text">Atención especializada para los más pequeños de la familia.</p>
                    <a href="/HOSPITAL/pages/servicios.php#pediatria" class="btn btn-outline-primary">Ver más</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Consulta General</h5>
                    <p class="card-text">Agenda una cita de revisión o consulta con nuestros médicos.</p>
                    <a href="/HOSPITAL/pages/agendar_cita.php" class="btn btn-outline-primary">Agendar Cita</a>
                </div>
            </div>
        </div>
        
    </div>
</div>

<div class="container-fluid bg-light p-5">
    <div class="container text-center">
        <h2>¿Por qué elegirnos?</h2>
        <p class="lead">Contamos con más de 20 años de experiencia, un equipo de especialistas certificados y un compromiso inquebrantable con la salud de nuestros pacientes. Tu confianza es nuestra prioridad.</p>
    </div>
</div>

<?php 
include 'PLANTILLAS/footer.php'; 
?>
