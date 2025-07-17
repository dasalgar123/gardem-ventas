<?php
// Página de Clientes

// Obtener filtros
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$rol_filtro = isset($_GET['rol']) ? $_GET['rol'] : '';

// Construir consulta
$where_conditions = [];
$params = [];

if ($busqueda) {
    $where_conditions[] = "(nombre LIKE ? OR correo LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

if ($rol_filtro) {
    $where_conditions[] = "rol = ?";
    $params[] = $rol_filtro;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = " WHERE " . implode(' AND ', $where_conditions);
}

// Obtener clientes
$sql = "SELECT * FROM cliente $where_clause ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clientes = $stmt->fetchAll();

// Obtener estadísticas por rol
$stmt = $pdo->prepare("SELECT rol, COUNT(*) as total FROM cliente GROUP BY rol");
$stmt->execute();
$stats_por_rol = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-users me-2"></i>
                Clientes
            </h1>
            <a href="index.php?page=clientes&action=nuevo" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>
                Nuevo Cliente
            </a>
        </div>
    </div>
</div>

<!-- Filtros y búsqueda -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="page" value="clientes">
                    
                    <div class="col-md-4">
                        <label for="rol" class="form-label">Filtrar por rol</label>
                        <select name="rol" id="rol" class="form-select">
                            <option value="">Todos los roles</option>
                            <option value="cliente" <?php echo $rol_filtro == 'cliente' ? 'selected' : ''; ?>>Cliente</option>
                            <option value="vendedor" <?php echo $rol_filtro == 'vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                            <option value="administrador" <?php echo $rol_filtro == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="buscar" class="form-label">Buscar cliente</label>
                        <input type="text" name="buscar" id="buscar" class="form-control" 
                               value="<?php echo htmlspecialchars($busqueda); ?>" 
                               placeholder="Buscar por nombre o correo...">
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

<!-- Estadísticas por rol -->
<div class="row mb-4">
    <?php foreach ($stats_por_rol as $stat): ?>
        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <?php echo ucfirst($stat['rol']); ?>
                            </div>
                            <div class="h50t-weight-bold text-gray-800">
                                <?php echo $stat['total']; ?> clientes
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Lista de clientes -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0t-weight-bold text-primary">
                    <i class="fas fa-list me-2"></i>
                    Lista de Clientes (<?php echo count($clientes); ?>)
                </h6>
            </div>
            <div class="card-body">                <?php if (empty($clientes)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5>No se encontraron clientes</h5>
                        <p>Intenta cambiar los filtros de búsqueda</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td><?php echo $cliente['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($cliente['correo']); ?>">
                                                <?php echo htmlspecialchars($cliente['correo']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php
                                            $rol_class = '';
                                            switch($cliente['rol']) {
                                                case 'administrador':
                                                    $rol_class = 'bg-danger';
                                                    break;
                                                case 'vendedor':
                                                    $rol_class = 'bg-warning';
                                                    break;
                                                case 'cliente':
                                                    $rol_class = 'bg-info';
                                                    break;
                                                default:
                                                    $rol_class = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $rol_class; ?>">
                                                <?php echo ucfirst($cliente['rol']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="index.php?page=clientes&action=ver&id=<?php echo $cliente['id']; ?>" 
                                                   class="btn btn-sm btn-primary" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="index.php?page=clientes&action=editar&id=<?php echo $cliente['id']; ?>" 
                                                   class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="confirmarEliminar(<?php echo $cliente['id']; ?>)" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

<script>
function confirmarEliminar(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este cliente?')) {
        window.location.href = 'index.php?page=clientes&action=eliminar&id=' + id;
    }
}
</script> 