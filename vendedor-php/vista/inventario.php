<?php
// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit();
}

// Incluir el controlador
require_once '../controlador/ControladorInventario.php';

// Crear instancia del controlador
$controladorInventario = new ControladorInventario($pdo);

// Obtener datos usando el controlador
$inventario = $controladorInventario->obtenerInventario();
$stats = $controladorInventario->obtenerEstadisticasInventario();
$total_productos = count($inventario);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 mb-3">
        <h1 class="h2"><i class="fas fa-warehouse text-primary me-2"></i>Inventario</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-secondary" onclick="exportToExcel()">
                <i class="fas fa-file-excel me-2"></i>Excel
            </button>
            <button class="btn btn-secondary" onclick="exportToPDF()">
                <i class="fas fa-file-pdf me-2"></i>PDF
            </button>
            <button class="btn btn-secondary" onclick="printInventario()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Total Productos</h6>
                            <h4 class="mb-0"><?php echo $stats['total_productos']; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Disponible</h6>
                            <h4 class="mb-0"><?php echo $stats['productos_disponibles']; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Stock Bajo</h6>
                            <h4 class="mb-0"><?php echo $stats['productos_stock_bajo']; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0">Agotado</h6>
                            <h4 class="mb-0"><?php echo $stats['productos_agotados']; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de inventario -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list text-primary me-2"></i>Inventario Detallado
            </h5>
            <span class="badge bg-secondary"><?php echo $total_productos; ?> productos</span>
        </div>
        
        <div class="card-body p-0">
            <?php if (empty($inventario)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-warehouse fa-3x mb-3"></i>
                    <h5>No se encontraron productos</h5>
                    <p>El inventario está vacío o no hay productos disponibles.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="inventarioTable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">ID</th>
                                <th class="px-3">Producto</th>
                                <th class="px-3">Tipo</th>
                                <th class="px-3">Precio</th>
                                <th class="px-3">Stock</th>
                                <th class="px-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventario as $item): ?>
                                <?php $stockStatus = ControladorInventario::getStockStatus($item['stock_total']); ?>
                                <tr>
                                    <td class="px-3">
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($item['producto_id']); ?></span>
                                    </td>
                                    <td class="px-3">
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($item['producto_nombre']); ?></div>
                                            <?php if (!empty($item['descripcion'])): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['descripcion']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-3">
                                        <span class="badge bg-info"><?php echo ucfirst(htmlspecialchars($item['tipo_producto'])); ?></span>
                                    </td>
                                    <td class="px-3">
                                        <span class="fw-bold"><?php echo ControladorInventario::formatPrice($item['precio']); ?></span>
                                    </td>
                                    <td class="px-3">
                                        <span class="badge bg-<?php echo $stockStatus['status']; ?>">
                                            <?php echo htmlspecialchars($item['stock_total']); ?>
                                        </span>
                                    </td>
                                    <td class="px-3">
                                        <span class="badge bg-<?php echo $stockStatus['status']; ?>">
                                            <?php echo $stockStatus['text']; ?>
                                        </span>
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

<!-- Scripts para exportación -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/autotable.min.js"></script>

<script>
// Función para exportar a Excel
function exportToExcel() {
    const table = document.getElementById('inventarioTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Inventario"});
    XLSX.writeFile(wb, "inventario_vendedor.xlsx");
}

// Función para exportar a PDF
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    doc.autoTable({
        html: '#inventarioTable',
        startY: 20,
        headStyles: { fillColor: [66, 139, 202] },
        styles: { fontSize: 8 }
    });
    
    doc.text('Inventario - Sistema Vendedor', 14, 15);
    doc.save('inventario_vendedor.pdf');
}

// Función para imprimir
function printInventario() {
    window.print();
}
</script> 