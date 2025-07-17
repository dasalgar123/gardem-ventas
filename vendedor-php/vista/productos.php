<?php
// Página de Productos - Usando Controlador

// Instanciar controlador
$controlador = new ControladorProductos($pdo);

// Obtener filtros
$tipo_filtro = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Obtener datos usando el controlador
$filtros = [
    'tipo' => $tipo_filtro,
    'buscar' => $busqueda
];

$productos = $controlador->obtenerProductos($filtros);
$stats_por_tipo = $controlador->obtenerEstadisticasPorTipo();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-box me-2"></i>
                Productos
            </h1>
            <!-- El vendedor no puede agregar productos nuevos -->
        </div>
    </div>
</div>

<!-- Filtros y búsqueda -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="page" value="productos">
                    
                    <div class="col-md-4">
                        <label for="tipo" class="form-label">Filtrar por tipo</label>
                        <select name="tipo" id="tipo" class="form-select">
                            <option value="">Todos los tipos</option>
                            <?php foreach ($controlador->obtenerTiposProducto() as $tipo): ?>
                                <option value="<?php echo $tipo; ?>" <?php echo $tipo_filtro == $tipo ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($tipo); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="buscar" class="form-label">Buscar producto</label>
                        <input type="text" name="buscar" id="buscar" class="form-control" 
                               value="<?php echo htmlspecialchars($busqueda); ?>" 
                               placeholder="Buscar por nombre o descripción...">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas por tipo -->
<div class="row mb-4">
    <?php foreach ($stats_por_tipo as $stat): ?>
        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <?php echo ucfirst($stat['tipo_producto']); ?>
                            </div>
                            <div class="h50t-weight-bold text-gray-800">
                                <?php echo $stat['total']; ?> productos
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tshirt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Lista de productos -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0t-weight-bold text-primary">
                    <i class="fas fa-list me-2"></i>
                    Lista de Productos (<?php echo count($productos); ?>)
                </h6>
            </div>
            <div class="card-body">                <?php if (empty($productos)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <h5>No se encontraron productos</h5>
                        <p>Intenta cambiar los filtros de búsqueda</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Precio</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td><?php echo $producto['id']; ?></td>
                                        <td>
                                            <?php if ($producto['imagen']): ?>
                                                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo ucfirst($producto['tipo_producto']); ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">$<?php echo number_format($producto['precio'], 2); ?></span>                   </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 50)); ?>
                                                <?php if (strlen($producto['descripcion']) > 50): ?>...<?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="index.php?page=productos&action=ver&id=<?php echo $producto['id']; ?>" 
                                                   class="btn btn-sm btn-primary" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <!-- El vendedor solo puede ver productos, no editarlos ni eliminarlos -->
                                            </div>
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

<!-- JavaScript unificado -->
<script src="../js/app.js"></script> 