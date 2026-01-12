<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-login { max-width: 400px; width: 100%; border: none; border-radius: 12px; }
        .brand-logo { font-size: 3rem; color: #0d6efd; }
    </style>
</head>
<body>

<div class="card shadow-lg card-login p-4">
    <div class="text-center mb-4">
        <i class="bi bi-hospital-fill brand-logo"></i>
        <h3 class="fw-bold mt-2">Bienvenido</h3>
        <p class="text-muted">Ingresa a tu cuenta</p>
    </div>

    <div class="card-body p-0">
        
        <?php
        if (isset($_SESSION['exito_registro'])) {
            echo '<div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>' . htmlspecialchars($_SESSION['exito_registro']) . '</div>
                  </div>';
            unset($_SESSION['exito_registro']);
        }

        if (isset($_SESSION['error_login'])) {
            echo '<div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>' . htmlspecialchars($_SESSION['error_login']) . '</div>
                  </div>';
            unset($_SESSION['error_login']);
        }
        ?>

        <form action="/HOSPITAL/pages/procesar_login.php" method="POST">
            
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@correo.com" required autofocus>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="contrasenia" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                    <input type="password" class="form-control" id="contrasenia" name="contrasenia" placeholder="•••••••" required>
                </div>
            </div>
            
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm">Entrar</button>
            </div>

            <div class="text-center">
                <p class="mb-0 text-muted">¿No tienes cuenta?</p>
                <a href="/HOSPITAL/pages/registro.php" class="text-decoration-none fw-bold">Regístrate aquí</a>
            </div>
            
        </form>
    </div>
</div>

</body>
</html>