<?php
include '../PLANTILLAS/header.php';
include '../config/conexion.php';

if ($_SESSION['usuario_tipo'] != 'Farmaceutico') { header("Location: login.php"); exit; }

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$meds = $conn->query("SELECT * FROM MEDICAMENTO WHERE stock > 0 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$servs = $conn->query("SELECT * FROM SERVICIO ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

$total_venta = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_venta += $item['subtotal'];
}
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-5">
            <h4 class="text-primary mb-3"><i class="bi bi-plus-circle"></i> Agregar Productos</h4>

            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-capsule"></i> Medicamentos
                </div>
                <div class="card-body">
                    <form action="controlador_carrito.php" method="POST">
                        <input type="hidden" name="accion" value="agregar_medicina">
                        
                        <div class="mb-3">
                            <label class="form-label">Seleccione Medicamento:</label>
                            <select name="id_medicamento" class="form-select" required>
                                <option value="">-- Buscar --</option>
                                <?php foreach($meds as $m): ?>
                                    <option value="<?= $m['id_medicamento'] ?>">
                                        <?= htmlspecialchars($m['nombre']) ?> - $<?= $m['precio'] ?> (Stock: <?= $m['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Cantidad:</label>
                                <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Agregar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-heart-pulse"></i> Servicios (Inyecciones, Tomas, etc.)
                </div>
                <div class="card-body">
                    <form action="controlador_carrito.php" method="POST">
                        <input type="hidden" name="accion" value="agregar_servicio">
                        
                        <div class="mb-3">
                            <label class="form-label">Tipo de Servicio:</label>
                            <select name="id_servicio" class="form-select" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach($servs as $s): ?>
                                    <option value="<?= $s['id_servicio'] ?>">
                                        <?= htmlspecialchars($s['nombre']) ?> - $<?= $s['precio'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Agregar Servicio</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-cart4"></i> Ticket Actual</h4>
                    <a href="controlador_carrito.php?accion=vaciar" class="btn btn-outline-danger btn-sm text-white border-white">
                        <i class="bi bi-trash"></i> Vaciar Todo
                    </a>
                </div>
                
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($_SESSION['carrito'])): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-basket display-4"></i><br>
                                        El carrito está vacío.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($_SESSION['carrito'] as $indice => $item): ?>
                                    <tr>
                                        <td>
                                            <?php if($item['tipo'] == 'medicina'): ?>
                                                <span class="badge bg-primary">MED</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">SERV</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                                        <td class="text-center"><?= $item['cantidad'] ?></td>
                                        <td class="text-end">$<?= number_format($item['precio'], 2) ?></td>
                                        <td class="text-end fw-bold">$<?= number_format($item['subtotal'], 2) ?></td>
                                        <td class="text-center">
                                            <a href="controlador_carrito.php?accion=eliminar&indice=<?= $indice ?>" class="text-danger" title="Quitar">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white p-4">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h3 class="text-muted">Total a Pagar:</h3>
                        </div>
                        <div class="col-6 text-end">
                            <h2 class="text-success fw-bold">$<?= number_format($total_venta, 2) ?></h2>
                        </div>
                    </div>
                    <hr>
                    
                    <form action="procesar_venta_final.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Cliente (Opcional):</label>
                            <input type="text" name="nombre_cliente" class="form-control" placeholder="Público en General">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg" <?= empty($_SESSION['carrito']) ? 'disabled' : '' ?>>
                                <i class="bi bi-cash-coin"></i> COBRAR E IMPRIMIR TICKET
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../PLANTILLAS/footer.php'; ?>