<?php
include '../config/conexion.php';
header('Content-Type: application/json');

if (!isset($_GET['id_doctor']) || !isset($_GET['fecha'])) {
    echo json_encode(['error' => 'Faltan parámetros']);
    exit;
}

$id_doctor = $_GET['id_doctor'];
$fecha_seleccionada = $_GET['fecha'];
$horarios_disponibles = [];

try {
    $sql_doc_info = "SELECT 
                        d.id_empleado, 
                        e.tiempo_min_cita 
                     FROM DOCTOR d
                     JOIN ESPECIALIDAD e ON d.id_especialidad = e.id_especialidad
                     WHERE d.id_doctor = ?";
    $stmt_doc_info = $conn->prepare($sql_doc_info);
    $stmt_doc_info->execute([$id_doctor]);
    $doc_info = $stmt_doc_info->fetch(PDO::FETCH_ASSOC);

    if (!$doc_info) throw new Exception("Doctor no encontrado");

    $id_empleado_doctor = $doc_info['id_empleado'];
    $tiempo_min_cita = (int)$doc_info['tiempo_min_cita'];

    $fecha_obj = new DateTime($fecha_seleccionada);
    $dias_es = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $dia_semana_es = $dias_es[(int)$fecha_obj->format('w')];

    $sql_horario = "SELECT hora_ini, hora_fin FROM HORARIO WHERE id_empleado = ? AND dia = ?";
    $stmt_horario = $conn->prepare($sql_horario);
    $stmt_horario->execute([$id_empleado_doctor, $dia_semana_es]);
    $horario_doc = $stmt_horario->fetch(PDO::FETCH_ASSOC);

    if (!$horario_doc) throw new Exception("El doctor no trabaja ese día");

    $sql_citas = "SELECT fecha_cita, fecha_fin_cita FROM CITA 
                  WHERE id_doctor = ? AND CONVERT(date, fecha_cita) = ?";
    $stmt_citas = $conn->prepare($sql_citas);
    $stmt_citas->execute([$id_doctor, $fecha_seleccionada]);
    $citas_ocupadas = $stmt_citas->fetchAll(PDO::FETCH_ASSOC);

    $hora_inicio_trabajo = new DateTime($horario_doc['hora_ini']);
    $hora_fin_trabajo = new DateTime($horario_doc['hora_fin']);
    $slot_actual = clone $hora_inicio_trabajo;

    while ($slot_actual < $hora_fin_trabajo) {
        $slot_inicio = clone $slot_actual;
        $slot_fin = clone $slot_actual;
        $slot_fin->modify("+$tiempo_min_cita minutes");

        if ($slot_fin > $hora_fin_trabajo) break;

        $esta_ocupado = false;
        foreach ($citas_ocupadas as $cita) {
            $cita_inicio_obj = new DateTime($cita['fecha_cita']);
            $cita_fin_obj = new DateTime($cita['fecha_fin_cita']);
            if ($slot_inicio < $cita_fin_obj && $slot_fin > $cita_inicio_obj) {
                $esta_ocupado = true;
                break;
            }
        }

        if (!$esta_ocupado) {
            $horarios_disponibles[] = $slot_inicio->format('H:i');
        }
        $slot_actual->modify("+$tiempo_min_cita minutes");
    }
    echo json_encode($horarios_disponibles);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
$conn = null;
?>