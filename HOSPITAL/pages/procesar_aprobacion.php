<?php
session_start();
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo']) || 
    !in_array($_SESSION['usuario_tipo'], ['Recepcionista', 'Empleado', 'Administrador'])) {
    header("Location: /HOSPITAL/pages/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!isset($_POST['id_solicitud']) || !isset($_POST['accion'])) {
        $_SESSION['error_solicitud'] = "Datos incompletos.";
        header("Location: /HOSPITAL/pages/ver_solicitudes.php");
        exit;
    }

    $id_solicitud = $_POST['id_solicitud'];
    $accion = $_POST['accion']; 
    $rol = isset($_POST['rol_detectado']) ? $_POST['rol_detectado'] : '';

    try {
        $conn->beginTransaction();

        
        if ($accion == 'rechazar') {
            $sqlRechazo = "UPDATE SOLICITUD_EMPLEADO SET id_estado = 3 WHERE id_solicitud = ?";
            $stmt = $conn->prepare($sqlRechazo);
            $stmt->execute([$id_solicitud]);
            
            $mensaje = "Solicitud rechazada correctamente.";
        } 
        
        elseif ($accion == 'aprobar') {

            $sqlApprove = "UPDATE SOLICITUD_EMPLEADO SET id_estado = 2 WHERE id_solicitud = ?";
            $stmtApprove = $conn->prepare($sqlApprove);
            $stmtApprove->execute([$id_solicitud]);

            $sqlGetId = "SELECT e.id_empleado 
                         FROM EMPLEADO e
                         JOIN SOLICITUD_EMPLEADO s ON e.correo = s.correo
                         WHERE s.id_solicitud = ?";
            $stmtGetId = $conn->prepare($sqlGetId);
            $stmtGetId->execute([$id_solicitud]);
            $empleadoData = $stmtGetId->fetch(PDO::FETCH_ASSOC);

            if (!$empleadoData) {
                throw new Exception("Error crítico: El empleado no se creó. Verifica el Trigger de base de datos.");
            }
            $nuevo_id_empleado = $empleadoData['id_empleado'];

            if (stripos($rol, 'Medico') !== false || stripos($rol, 'Médico') !== false) {
                
                if (empty($_POST['cedula']) || empty($_POST['no_consultorio']) || empty($_POST['id_especialidad'])) {
                    throw new Exception("Faltan datos obligatorios para el Médico (Cédula, Consultorio o Especialidad).");
                }

                $cedula = $_POST['cedula'];
                $consultorio = $_POST['no_consultorio'];
                $especialidad = $_POST['id_especialidad'];

                $sqlDoc = "INSERT INTO DOCTOR (cedula_profesional, id_empleado, no_consultorio, id_especialidad) 
                           VALUES (?, ?, ?, ?)";
                $stmtDoc = $conn->prepare($sqlDoc);
                $stmtDoc->execute([$cedula, $nuevo_id_empleado, $consultorio, $especialidad]);
            } 
            
            elseif (stripos($rol, 'Farmace') !== false) {
                $sqlFar = "INSERT INTO FARMACEUTICO (id_empleado) VALUES (?)";
                $stmtFar = $conn->prepare($sqlFar);
                $stmtFar->execute([$nuevo_id_empleado]);
            }

            $mensaje = "Empleado contratado y activado exitosamente.";
        }

        $conn->commit();
        $_SESSION['exito_solicitud'] = $mensaje;
        header("Location: /HOSPITAL/pages/ver_solicitudes.php");
        exit;

    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        $_SESSION['error_solicitud'] = "Error: " . $e->getMessage();
        header("Location: /HOSPITAL/pages/ver_solicitudes.php");
        exit;
    }
}
?>