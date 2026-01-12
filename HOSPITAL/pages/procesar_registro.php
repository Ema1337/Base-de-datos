<?php
session_start();
include '../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        if (!isset($_POST['tipo_registro'])) throw new Exception("Error en el envío del formulario.");
        $tipo_registro = $_POST['tipo_registro']; 

        $correo = $_POST['correo'];
        $contrasenia = $_POST['contrasenia'];
        
        $primer_nombre = $_POST['primer_nombre'];
        $segundo_nombre = !empty($_POST['segundo_nombre']) ? $_POST['segundo_nombre'] : NULL;
        $a_paterno = $_POST['a_paterno'];
        $a_materno = !empty($_POST['a_materno']) ? $_POST['a_materno'] : NULL;
        $telefono = $_POST['telefono'];

        if (empty($_POST['sexo'])) {
            throw new Exception("Por favor, selecciona tu Sexo/Género.");
        }
        $sexo = $_POST['sexo']; 
        $checkUser = $conn->prepare("SELECT correo FROM USUARIO WHERE correo = ?");
        $checkUser->execute([$correo]);
        if ($checkUser->rowCount() > 0) {
            throw new Exception("El correo electrónico '$correo' ya está registrado en el sistema.");
        }

        if ($tipo_registro == 'paciente') {
            
            $nacimiento = $_POST['nacimiento'];
            $tipo_sangre = $_POST['tipo_sangre'];
            $donante = $_POST['donante'];
            $alergias = !empty($_POST['alergias']) ? $_POST['alergias'] : NULL;

            $checkTel = $conn->prepare("SELECT telefono FROM PACIENTE WHERE telefono = ?");
            $checkTel->execute([$telefono]);
            if ($checkTel->rowCount() > 0) {
                throw new Exception("El número de teléfono ya está registrado por otro paciente.");
            }

            $sql_validar = "SELECT dbo.FN_ValidarFechaNacimiento(?) AS es_valida";
            $stmt_validar = $conn->prepare($sql_validar);
            $stmt_validar->execute([$nacimiento]);
            $resultado_validez = $stmt_validar->fetch(PDO::FETCH_ASSOC);

            if ($resultado_validez['es_valida'] == 0) {
                throw new Exception("Error: La fecha de nacimiento no es válida o es futura.");
            }

            $conn->beginTransaction();

            $sql_usuario = "INSERT INTO USUARIO (correo, contrasenia, tipo) VALUES (?, ?, 'Paciente')";
            $sentencia_usuario = $conn->prepare($sql_usuario);
            $sentencia_usuario->execute([$correo, $contrasenia]);

            $sql_paciente = "INSERT INTO PACIENTE (nacimiento, primer_nombre, segundo_nombre, a_paterno, a_materno, tipo_sangre, donante, sexo, telefono, alergias, correo)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sentencia_paciente = $conn->prepare($sql_paciente);
            $sentencia_paciente->execute([
                $nacimiento, $primer_nombre, $segundo_nombre, $a_paterno, $a_materno,
                $tipo_sangre, $donante, $sexo, $telefono, $alergias, $correo
            ]);

            $conn->commit();

            $_SESSION['exito_registro'] = "¡Cuenta de Paciente creada exitosamente! Por favor, inicia sesión.";
            header("Location: /HOSPITAL/pages/login.php");
            exit;

        } elseif ($tipo_registro == 'empleado') {
            
            $curp = $_POST['curp'];
            $rol_solicitado = $_POST['rol_solicitado']; 
            $genero = $sexo;

            $checkCurp = $conn->prepare("SELECT curp FROM SOLICITUD_EMPLEADO WHERE curp = ?");
            $checkCurp->execute([$curp]);
            if ($checkCurp->rowCount() > 0) {
                throw new Exception("La CURP ingresada ya tiene una solicitud registrada.");
            }

            $checkSol = $conn->prepare("SELECT correo FROM SOLICITUD_EMPLEADO WHERE correo = ? AND id_estado = 1"); 
            $checkSol->execute([$correo]);
            if ($checkSol->rowCount() > 0) {
                throw new Exception("Ya existe una solicitud pendiente de revisión para este correo.");
            }

            $sql_solicitud = "INSERT INTO SOLICITUD_EMPLEADO 
                (correo, contrasenia, rol_solicitado, curp, primer_nombre, segundo_nombre, a_paterno, a_materno, genero, telefono) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmtSol = $conn->prepare($sql_solicitud);
            $stmtSol->execute([
                $correo, $contrasenia, $rol_solicitado, $curp, 
                $primer_nombre, $segundo_nombre, $a_paterno, $a_materno, 
                $genero, $telefono
            ]);

            $_SESSION['exito_registro'] = "Solicitud enviada. Tu cuenta debe ser aprobada por administración antes de poder entrar.";
            header("Location: /HOSPITAL/pages/registro.php");
            exit;
        }

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        if ($e instanceof PDOException && $e->getCode() == '23000') {
             $_SESSION['error_registro'] = "Error: Datos duplicados. Verifique que el Correo, Teléfono o CURP no estén ya registrados.";
        } else {
             $_SESSION['error_registro'] = $e->getMessage();
        }
        
        header("Location: /HOSPITAL/pages/registro.php");
        exit;
    }

} else {
    header("Location: /HOSPITAL/pages/registro.php");
    exit;
}
?>