<?php
session_start();
include '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contrasenia = $_POST['contrasenia'];

    try {
        $sql = "SELECT * FROM USUARIO WHERE correo = ? AND contrasenia = ?";
        $sentencia = $conn->prepare($sql);
        $sentencia->execute([$correo, $contrasenia]);
        $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION['usuario_correo'] = $usuario['correo'];
            $_SESSION['usuario_tipo'] = $usuario['tipo']; 

            switch ($usuario['tipo']) {
                case 'Paciente':
                    header("Location: /HOSPITAL/pages/mis_citas.php");
                    break;
                case 'Doctor':
                    header("Location: /HOSPITAL/pages/dashboard_doctor.php");
                    break;
                case 'Farmaceutico':
                    header("Location: /HOSPITAL/pages/dashboard_farmacia.php");
                    break;
                case 'Recepcionista':
                case 'Empleado': 
                    header("Location: /HOSPITAL/pages/dashboard_recepcion.php");
                    break;
                default:
                    header("Location: /HOSPITAL/index.php");
                    break;
            }
            exit;
        } else {
            $_SESSION['error_login'] = "Credenciales incorrectas.";
            header("Location: /HOSPITAL/pages/login.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error_login'] = "Error BD: " . $e->getMessage();
        header("Location: /HOSPITAL/pages/login.php");
        exit;
    }
}
?>