<?php
// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit();
}

require_once __DIR__ . '/../controlador/ControladorPedidos.php';
$controlador = new ControladorPedidos($pdo);

// Filtros
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$filtros = [
    'search' => $search,
    'date_from' => $date_from,
    'date_to' => $date_to
];

$pedidos = $controlador->obtenerPedidos($filtros);
$estadisticas = $controlador->calcularEstadisticas($pedidos);
$total_pedidos = $estadisticas['total_pedidos'];
$total_ventas = $estadisticas['total_ventas'];
$promedio_pedido = $estadisticas['promedio_pedido'];
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 mb-3">
        <h1 class="h2"><i class="fas fa-shopping-cart text-primary me-2"></i>Gestión de Pedidos</h1>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">Total Pedidos</h6>
                            <h3 class="mb-0"><?php echo number_format($total_pedidos); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">Total Pedidos</h6>
                            <h3 class="mb-0">$<?php echo number_format($total_ventas, 0, ',', '.'); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">Promedio por Pedido</h6>
                            <h3 class="mb-0">$<?php echo number_format($promedio_pedido, 0, ',', '.'); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-bar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5></div>
        <div class="card-body">
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="pedidos">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Cliente, teléfono, correo, productos..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Desde</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Hasta</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="index.php?page=pedidos" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Pedidos -->
    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Pedidos (<?php echo number_format($total_pedidos); ?>)</h5></div>
        <div class="card-body">
            <?php if (empty($pedidos)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <h5>No se encontraron pedidos</h5>
                    <p>No hay pedidos que coincidan con los filtros aplicados.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Correo</th>
                                <th>Dirección</th>
                                <th>Comentarios</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#<?php echo $pedido['id']; ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                            <?php echo strtoupper(substr($pedido['nombre_cliente'], 0, 1)); ?>
                                        </div>
                                        <strong><?php echo htmlspecialchars($pedido['nombre_cliente']); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-phone text-muted me-1"></i>
                                    <?php echo htmlspecialchars($pedido['telefono']); ?>
                                </td>
                                <td style="max-width: 250px;">
                                    <div class="productos-preview">
                                        <?php 
                                        $productos = explode('•', $pedido['productos']);
                                        $productos = array_filter($productos);
                                        $productos = array_slice($productos, 0, 2);
                                        foreach ($productos as $producto) {
                                            echo '<div class="text-truncate">• ' . htmlspecialchars(trim($producto)) . '</div>';
                                        }
                                        if (count(explode('•', $pedido['productos'])) > 2) {
                                            echo '<small class="text-muted">... y más</small>';
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success fs-6">
                                        $<?php echo number_format($pedido['total'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($pedido['correo']): ?>
                                        <i class="fas fa-envelope text-muted me-1"></i>
                                        <small><?php echo htmlspecialchars($pedido['correo']); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($pedido['direccion']): ?>
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                        <small><?php echo htmlspecialchars(substr($pedido['direccion'], 0, 30)); ?>
                                        <?php if (strlen($pedido['direccion']) > 30): ?>...<?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($pedido['comentarios']): ?>
                                        <i class="fas fa-comment text-muted me-1"></i>
                                        <small><?php echo htmlspecialchars(substr($pedido['comentarios'], 0, 20)); ?>
                                        <?php if (strlen($pedido['comentarios']) > 20): ?>...<?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
    font-weight: bold;
}
.productos-preview {
    max-width:250px;
}
.productos-preview .text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style> 

<!-- JavaScript unificado -->
<script src="../js/app.js"></script> 