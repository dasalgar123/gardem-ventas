<?php
// Menú Principal del Sistema de Vendedor

require_once __DIR__ . '/../controlador/ControladorMenuPrincipal.php';
$controlador = new ControladorMenuPrincipal($pdo);

// Obtener estadísticas básicas
$stats = $controlador->obtenerEstadisticas();
$productos_recientes = $controlador->obtenerProductosRecientes(5);
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4"><i class="fas fa-tachometer-alt me-2"></i>Menú Principal</h1>
    </div>
</div>

<!-- Tarjetas de estadísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Productos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $stats['total_productos']; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Clientes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $stats['total_clientes']; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Caballeros
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $stats['productos_caballero']; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-male fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Damas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $stats['productos_dama']; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-female fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Productos recientes -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-box me-2"></i>
                    Productos Recientes
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($productos_recientes)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No hay productos registrados</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Precio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos_recientes as $producto): ?>
                                    <tr>
                                        <td><?php echo $producto['id']; ?></td>
                                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo ucfirst($producto['tipo_producto']); ?></span>
                                        </td>
                                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                        <td>
                                            <a href="index.php?page=productos&action=ver&id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
</div>

<!-- Acciones rápidas -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>
                    Acciones Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="index.php?page=productos" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-box-open me-2"></i>
                            Ver Productos
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?page=clientes" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-users me-2"></i>
                            Ver Clientes
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?page=ventas" class="btn btn-info btn-lg w-100">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Nueva Venta
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?page=pedidos" class="btn btn-warning btn-lg w-100">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Ver Pedidos
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?page=inventario" class="btn btn-secondary btn-lg w-100">
                            <i class="fas fa-warehouse me-2"></i>
                            Ver Inventario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 