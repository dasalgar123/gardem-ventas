<?php
// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit();
}

// Incluir el controlador
require_once '../controlador/ControladorVentas.php';

// Crear instancia del controlador
$controladorVentas = new ControladorVentas($pdo);

// Obtener datos usando el controlador
$siguiente_factura = $controladorVentas->obtenerSiguienteFactura();
$clientes = $controladorVentas->obtenerClientes();
$productos = $controladorVentas->obtenerProductos();
$colores = $controladorVentas->obtenerColores();
$tallas = $controladorVentas->obtenerTallas();
$ventas = $controladorVentas->obtenerVentas();
$total_ventas = count($ventas);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 mb-3">
        <h1 class="h2"><i class="fas fa-chart-line text-primary me-2"></i>Ventas</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
            <i class="fas fa-plus me-2"></i>Nueva Venta
        </button>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success">
            <strong>¡Éxito!</strong> La venta con factura <?php echo htmlspecialchars($_GET['factura'] ?? ''); ?> ha sido registrada correctamente.
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Historial de Ventas (<?php echo $total_ventas; ?> registros)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($ventas)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <h5>No se encontraron ventas</h5>
                    <p>La tabla productos_ventas está vacía o no existe.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Factura</th>
                                <th>Productos</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td><?php echo $venta['id']; ?></td>
                                <td><?php echo htmlspecialchars($venta['cliente_nombre'] ?? 'Cliente #' . $venta['cliente_id']); ?></td>
                                <td><?php echo $venta['fecha']; ?></td>
                                <td><?php echo htmlspecialchars($venta['factura']); ?></td>
                                <td><?php echo htmlspecialchars($venta['productos']); ?></td>
                                <td>$<?php echo number_format($venta['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Nueva Venta -->
<div class="modal fade" id="nuevaVentaModal" tabindex="-1" aria-labelledby="nuevaVentaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevaVentaModalLabel">
                    <i class="fas fa-file-invoice me-2"></i>FACTURA DE VENTA
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevaVenta" method="POST" action="procesar_venta.php">
                <div class="modal-body">
                    <!-- Encabezado de Factura -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>DATOS DE LA EMPRESA</h6>
                                </div>
                                <div class="card-body">
                                    <h5 class="text-primary mb-1">GARDEL CATÁLOGO</h5>
                                    <p class="mb-1"><strong>Dirección:</strong> Calle Principal #123</p>
                                    <p class="mb-1"><strong>Teléfono:</strong> (123) 456-7890</p>
                                    <p class="mb-0"><strong>Email:</strong> info@gardel.com</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>DATOS DEL CLIENTE</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="cliente_id" class="form-label fw-bold">Cliente</label>
                                        <select class="form-select" id="cliente_id" name="cliente_id" required>
                                            <option value="">Seleccionar cliente...</option>
                                            <?php foreach ($clientes as $cliente): ?>
                                                <option value="<?php echo $cliente['id']; ?>"><?php echo htmlspecialchars($cliente['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Factura #</label>
                                            <input type="text" class="form-control" id="factura" name="factura" value="<?php echo $siguiente_factura; ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Fecha</label>
                                            <input type="text" class="form-control" value="<?php echo date('d/m/Y'); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de Productos -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-list me-2"></i>DETALLE DE PRODUCTOS
                            </h5>
                            <button type="button" class="btn btn-success btn-lg" id="agregarProducto">
                                <i class="fas fa-plus me-2"></i>Agregar Producto
                            </button>
                        </div>
                        
                        <div id="productosContainer">
                            <!-- Producto 1 -->
                            <div class="producto-row border rounded p-3 mb-3" data-producto-id="1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">1</h6>
                                    <button type="button" class="btn btn-danger btn-sm remover-producto" style="display: none; margin-top: 0;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Producto</label>
                                        <select class="form-select producto-select" name="productos[1][producto_id]" required>
                                            <option value="">Seleccionar producto...</option>
                                            <?php foreach ($productos as $producto): ?>
                                                <option value="<?php echo $producto['id']; ?>" data-tipo="<?php echo htmlspecialchars($producto['tipo_producto']); ?>" data-precio="<?php echo $producto['precio']; ?>">
                                                    <?php echo htmlspecialchars($producto['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Color</label>
                                        <select class="form-select" name="productos[1][color_id]" required>
                                            <option value="">Color...</option>
                                            <?php foreach ($colores as $color): ?>
                                                <option value="<?php echo $color['id']; ?>">
                                                    <?php echo htmlspecialchars($color['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Talla</label>
                                        <select class="form-select talla-select" name="productos[1][talla_id]" required>
                                            <option value="">Talla...</option>
                                            <?php foreach ($tallas as $talla): ?>
                                                <option value="<?php echo $talla['id']; ?>" data-categoria="<?php echo $talla['categoria']; ?>">
                                                    <?php echo htmlspecialchars($talla['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Cantidad</label>
                                        <input type="number" class="form-control cantidad-input" name="productos[1][cantidad]" placeholder="Cant." min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Precio</label>
                                        <input type="number" class="form-control precio-input" name="productos[1][precio]" placeholder="Precio" step="0.01" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Subtotal</label>
                                        <input type="number" class="form-control subtotal-input" name="productos[1][subtotal]" placeholder="Subtotal" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Totales de Factura -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>RESUMEN DE TOTALES</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Subtotal</label>
                                            <input type="text" class="form-control" id="subtotal" name="subtotal" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">IVA (16%)</label>
                                            <input type="text" class="form-control" id="iva" name="iva" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label fw-bold fs-4 text-success">TOTAL A PAGAR</label>
                                            <input type="text" class="form-control form-control-lg fw-bold text-success" id="total_venta" name="total_venta" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script simple y directo para ventas -->
<script>
// Script directo sin conflictos
document.addEventListener('DOMContentLoaded', function() {
    console.log('VENTAS SCRIPT CARGADO');
    
    // Buscar botón y agregar evento
    const boton = document.getElementById('agregarProducto');
    if (boton) {
        boton.addEventListener('click', function() {
            console.log('BOTÓN CLICKEADO');
            
            // Crear nueva fila completa
            const container = document.getElementById('productosContainer');
            const nuevaFila = document.createElement('div');
            nuevaFila.className = 'producto-row border rounded p-3 mb-3';
            
            // Obtener el número de productos existentes
            const productosExistentes = container.querySelectorAll('.producto-row');
            const numeroProducto = productosExistentes.length + 1;
            
            nuevaFila.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">${numeroProducto}</h6>
                    <button type="button" class="btn btn-danger btn-sm remover-producto" style="margin-top: 0;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Producto</label>
                        <select class="form-select producto-select" name="productos[${numeroProducto}][producto_id]" required>
                            <option value="">Seleccionar producto...</option>
                            ${obtenerOpcionesProductos()}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Color</label>
                        <select class="form-select" name="productos[${numeroProducto}][color_id]" required>
                            <option value="">Color...</option>
                            <option value="1">Rojo</option>
                            <option value="2">Azul</option>
                            <option value="3">Verde</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Talla</label>
                        <select class="form-select" name="productos[${numeroProducto}][talla_id]" required>
                            <option value="">Talla...</option>
                            <option value="1">S</option>
                            <option value="2">M</option>
                            <option value="3">L</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Cantidad</label>
                        <input type="number" class="form-control cantidad-input" name="productos[${numeroProducto}][cantidad]" placeholder="Cant." min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Precio</label>
                        <input type="number" class="form-control precio-input" name="productos[${numeroProducto}][precio]" placeholder="Precio" step="0.01" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Subtotal</label>
                        <input type="number" class="form-control subtotal-input" name="productos[${numeroProducto}][subtotal]" placeholder="Subtotal" readonly>
                    </div>
                </div>
            `;
            
            container.appendChild(nuevaFila);
            
            // Agregar event listeners a la nueva fila
            const nuevoSelect = nuevaFila.querySelector('.producto-select');
            const nuevaCantidad = nuevaFila.querySelector('.cantidad-input');
            
            nuevoSelect.addEventListener('change', calcularPrecioYSubtotal);
            nuevaCantidad.addEventListener('input', calcularPrecioYSubtotal);
        });
    }
    
    // Event listener para remover productos
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remover-producto')) {
            const fila = e.target.closest('.producto-row');
            fila.remove();
            calcularTotal();
        }
    });
    
    // Event listeners para productos existentes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select') || e.target.classList.contains('cantidad-input')) {
            calcularPrecioYSubtotal.call(e.target);
        }
    });
    
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad-input')) {
            calcularPrecioYSubtotal.call(e.target);
        }
    });
    
    // Función para obtener opciones de productos del primer select
    function obtenerOpcionesProductos() {
        const primerSelect = document.querySelector('.producto-select');
        if (primerSelect) {
            return primerSelect.innerHTML.replace(/name="productos\[1\]\[producto_id\]"/g, '');
        }
        return '';
    }
    
    // Función para calcular precio y subtotal
    function calcularPrecioYSubtotal() {
        const fila = this.closest('.producto-row');
        const select = fila.querySelector('.producto-select');
        const cantidad = fila.querySelector('.cantidad-input');
        const precio = fila.querySelector('.precio-input');
        const subtotal = fila.querySelector('.subtotal-input');
        
        if (select.value) {
            const precioSeleccionado = select.options[select.selectedIndex].getAttribute('data-precio');
            precio.value = precioSeleccionado;
            
            if (cantidad.value) {
                const subtotalCalculado = parseFloat(precioSeleccionado) * parseFloat(cantidad.value);
                subtotal.value = subtotalCalculado.toFixed(2);
            }
        }
        
        calcularTotal();
    }
    
    // Función para calcular total general
    function calcularTotal() {
        let total = 0;
        const subtotales = document.querySelectorAll('.subtotal-input');
        
        subtotales.forEach(subtotal => {
            if (subtotal.value) {
                total += parseFloat(subtotal.value);
            }
        });
        
        const totalElement = document.getElementById('total_venta');
        if (totalElement) {
            totalElement.value = '$' + total.toFixed(2);
        }
    }
});
</script>

