<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if (!isset($_SESSION['usuario_correo'])) {
    header("Location: login.php");
    exit;
}

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filtro_rol = isset($_GET['rol']) ? $_GET['rol'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : ''; 

$sql = "SELECT 
            e.id_empleado, 
            e.primer_nom_emp, 
            e.a_pat_emp, 
            e.a_mat_emp, 
            e.telefono, 
            e.activo, 
            e.correo,
            u.tipo as nombre_cargo  
        FROM EMPLEADO e
        JOIN USUARIO u ON e.correo = u.correo 
        WHERE 1=1";

$params = [];

if (!empty($busqueda)) {
    $sql .= " AND (e.primer_nom_emp LIKE ? OR e.a_pat_emp LIKE ? OR e.a_mat_emp LIKE ?)";
    $termino = "%" . $busqueda . "%";
    $params[] = $termino;
    $params[] = $termino;
    $params[] = $termino;
}

if (!empty($filtro_rol)) {
    $sql .= " AND u.tipo = ?";
    $params[] = $filtro_rol;
}

if ($filtro_estado !== '') { 
    $sql .= " AND e.activo = ?";
    $params[] = $filtro_estado;
}

$sql .= " ORDER BY e.activo DESC, e.a_pat_emp ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtCargos = $conn->query("SELECT DISTINCT tipo FROM USUARIO WHERE tipo != 'Paciente'");
$cargos = $stmtCargos->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><i class="bi bi-people-fill"></i> Directorio de Empleados</h2>
        <a href="mi_cuenta.php" class="btn btn-outline-secondary">Volver al Panel</a>
    </div>

    <div class="card bg-light shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-4">
                    <label class="form-label fw-bold">Buscar por nombre:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="busqueda" class="form-control" placeholder="Ej: Juan..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Cargo:</label>
                    <select name="rol" class="form-select">
                        <option value="">-- Todos --</option>
                        <?php foreach($cargos as $cargo): ?>
                            <option value="<?php echo $cargo['tipo']; ?>" <?php echo ($filtro_rol == $cargo['tipo']) ? 'selected' : ''; ?>>
                                <?php echo $cargo['tipo']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Estado:</label>
                    <select name="estado" class="form-select">
                        <option value="">-- Todos --</option>
                        <option value="1" <?php echo ($filtro_estado === '1') ? 'selected' : ''; ?>>Activos</option>
                        <option value="0" <?php echo ($filtro_estado === '0') ? 'selected' : ''; ?>>Bajas (Inactivos)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filtrar</button>
                    <a href="gestion_empleados.php" class="btn btn-outline-secondary" title="Limpiar">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo isset($_GET['tipo']) ? $_GET['tipo'] : 'info'; ?> alert-dismissible fade show">
            <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle shadow-sm bg-white">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nombre Completo</th>
                    <th>Cargo</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($empleados)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-emoji-frown fs-4"></i><br>No se encontraron empleados con esos filtros.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($empleados as $emp): ?>
                        <tr class="<?php echo $emp['activo'] == 0 ? 'table-secondary text-muted' : ''; ?>">
                            
                            <td>
                                <div class="fw-bold text-uppercase">
                                    <?php echo $emp['primer_nom_emp'] . " " . $emp['a_pat_emp'] . " " . $emp['a_mat_emp']; ?>
                                </div>
                                <small class="text-muted fst-italic"><?php echo $emp['correo']; ?></small>
                            </td>

                            <td class="text-center">
                                <?php 
                                    $colorBadge = 'bg-info text-dark';
                                    if($emp['nombre_cargo'] == 'Doctor') $colorBadge = 'bg-primary';
                                    if($emp['nombre_cargo'] == 'Recepcionista') $colorBadge = 'bg-warning text-dark';
                                    if($emp['nombre_cargo'] == 'Farmaceutico') $colorBadge = 'bg-success';
                                ?>
                                <span class="badge <?php echo $colorBadge; ?> rounded-pill px-3">
                                    <?php echo $emp['nombre_cargo']; ?>
                                </span>
                            </td>

                            <td>
                                <i class="bi bi-telephone-fill text-muted"></i> <?php echo $emp['telefono']; ?>
                            </td>
                            
                            <td class="text-center">
                                <?php if ($emp['activo'] == 1): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-dash-circle"></i> Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <?php if ($emp['activo'] == 1): ?>
                                    <a href="procesar_baja_empleado.php?id=<?php echo $emp['id_empleado']; ?>&accion=baja" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('¿Estás seguro de dar de BAJA a <?php echo $emp['primer_nom_emp']; ?>?\n\nSi es Doctor y tiene citas pendientes, el sistema BLOQUEARÁ esta acción.');"
                                       title="Dar de Baja">
                                        <i class="bi bi-person-x-fill"></i> Baja
                                    </a>
                                <?php else: ?>
                                    <a href="procesar_baja_empleado.php?id=<?php echo $emp['id_empleado']; ?>&accion=alta" 
                                       class="btn btn-sm btn-outline-success" 
                                       onclick="return confirm('¿Reactivar a este empleado?');"
                                       title="Reactivar">
                                        <i class="bi bi-person-check-fill"></i> Reactivar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>