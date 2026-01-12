<?php
include '../config/conexion.php';

header('Content-Type: application/json');

if (isset($_GET['id_especialidad'])) {
    
    $id_especialidad = $_GET['id_especialidad'];
    $doctores = [];

    try {
        $sql = "SELECT 
                    d.id_doctor, 
                    e.primer_nom_emp, 
                    e.a_pat_emp,
                    e.a_mat_emp
                FROM DOCTOR d
                JOIN EMPLEADO e ON d.id_empleado = e.id_empleado
                WHERE d.id_especialidad = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_especialidad]);
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultado as $doc) {
            
            $apellido_materno = !empty($doc['a_mat_emp']) ? ' ' . $doc['a_mat_emp'] : '';
            
            $doctores[] = [
                'id' => $doc['id_doctor'],
                'nombre' => 'Dr. ' . $doc['primer_nom_emp'] . ' ' . $doc['a_pat_emp'] . $apellido_materno
            ];
        }
        echo json_encode($doctores);

    } catch (PDOException $e) {
        
        echo json_encode(['error' => 'Error de BD al cargar doctores: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['error' => 'No se especificó la especialidad']);
}
$conn = null;
?>