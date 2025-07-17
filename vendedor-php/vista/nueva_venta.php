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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta - Sistema de Vendedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-store me-2"></i>
                Sistema de Vendedor
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=menu_principal">
                            <i class="fas fa-tachometer-alt me-1"></i>Menú Principal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?page=ventas">
                            <i class="fas fa-shopping-cart me-1"></i>Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=pedidos">
                            <i class="fas fa-clipboard-list me-1"></i>Pedidos
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php?page=perfil">
                                <i class="fas fa-user-cog me-2"></i>Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="index.php?page=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2">
                        <i class="fas fa-plus-circle text-primary me-2"></i>Nueva Venta
                    </h1>
                    <a href="index.php?page=ventas" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Ventas
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <form id="formNuevaVenta" method="POST" action="procesar_venta.php">
                            <!-- Información básica -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="factura" class="form-label fw-bold">Número de Factura</label>
                                        <input type="text" class="form-control form-control-lg" id="factura" name="factura" 
                                               value="<?php echo $siguiente_factura; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_id" class="form-label fw-bold">Cliente</label>
                                        <select class="form-select form-select-lg" id="cliente_id" name="cliente_id" required>
                                            <option value="">Seleccionar cliente...</option>
                                            <?php foreach ($clientes as $cliente): ?>
                                                <option value="<?php echo $cliente['id']; ?>">
                                                    <?php echo htmlspecialchars($cliente['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Productos -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0">
                                        <i class="fas fa-boxes text-primary me-2"></i>Productos
                                    </h4>
                                    <button type="button" class="btn btn-success btn-lg" id="agregarProducto">
                                        <i class="fas fa-plus me-2"></i>Agregar Producto
                                    </button>
                                </div>
                                
                                <div id="productosContainer">
                                    <!-- Producto 1 -->
                                    <div class="producto-row border rounded p-4 mb-3 bg-white" data-producto-id="1">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0 text-primary">Producto #1</h5>
                                            <button type="button" class="btn btn-danger btn-sm remover-producto" style="display: none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Producto</label>
                                                <select class="form-select producto-select" name="productos[1][producto_id]" required>
                                                    <option value="">Seleccionar producto...</option>
                                                    <?php foreach ($productos as $producto): ?>
                                                        <option value="<?php echo $producto['id']; ?>" 
                                                                data-tipo="<?php echo htmlspecialchars($producto['tipo_producto']); ?>" 
                                                                data-precio="<?php echo $producto['precio']; ?>">
                                                            <?php echo htmlspecialchars($producto['nombre']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold">Color</label>
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
                                                <label class="form-label fw-bold">Talla</label>
                                                <select class="form-select talla-select" name="productos[1][talla_id]" required>
                                                    <option value="">Talla...</option>
                                                    <?php foreach ($tallas as $talla): ?>
                                                        <option value="<?php echo $talla['id']; ?>" 
                                                                data-categoria="<?php echo $talla['categoria']; ?>">
                                                            <?php echo htmlspecialchars($talla['nombre']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold">Cantidad</label>
                                                <input type="number" class="form-control cantidad-input" 
                                                       name="productos[1][cantidad]" placeholder="Cantidad" min="1" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold">Precio</label>
                                                <input type="number" class="form-control precio-input" 
                                                       name="productos[1][precio]" placeholder="Precio" step="0.01" readonly>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label fw-bold">Subtotal</label>
                                                <input type="number" class="form-control subtotal-input" 
                                                       name="productos[1][subtotal]" placeholder="Subtotal" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total de la Venta -->
                            <div class="row">
                                <div class="col-md-6 offset-md-6">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="mb-0">Total de la Venta</h4>
                                                <h3 class="mb-0" id="total_venta_display">$0.00</h3>
                                            </div>
                                            <input type="hidden" id="total_venta" name="total_venta" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-secondary btn-lg me-3" onclick="history.back()">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Guardar Venta
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para nueva venta -->
    <script>
    // Variables globales
    let productoCounter = 1;

    // Función para mostrar notificación
    function mostrarNotificacion(mensaje, tipo = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Función para crear nueva fila de producto
    function crearFilaProducto(numero) {
        const nuevaFila = document.createElement('div');
        nuevaFila.className = 'producto-row border rounded p-4 mb-3 bg-white';
        nuevaFila.setAttribute('data-producto-id', numero);
        
        nuevaFila.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 text-primary">Producto #${numero}</h5>
                <button type="button" class="btn btn-danger btn-sm remover-producto">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Producto</label>
                    <select class="form-select producto-select" name="productos[${numero}][producto_id]" required>
                        <option value="">Seleccionar producto...</option>
                        ${obtenerOpcionesProductos()}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Color</label>
                    <select class="form-select" name="productos[${numero}][color_id]" required>
                        <option value="">Color...</option>
                        ${obtenerOpcionesColores()}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Talla</label>
                    <select class="form-select talla-select" name="productos[${numero}][talla_id]" required>
                        <option value="">Talla...</option>
                        ${obtenerOpcionesTallas()}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Cantidad</label>
                    <input type="number" class="form-control cantidad-input" 
                           name="productos[${numero}][cantidad]" placeholder="Cantidad" min="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Precio</label>
                    <input type="number" class="form-control precio-input" 
                           name="productos[${numero}][precio]" placeholder="Precio" step="0.01" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label fw-bold">Subtotal</label>
                    <input type="number" class="form-control subtotal-input" 
                           name="productos[${numero}][subtotal]" placeholder="Subtotal" readonly>
                </div>
            </div>
        `;
        
        return nuevaFila;
    }

    // Función para obtener opciones de productos
    function obtenerOpcionesProductos() {
        const primerSelect = document.querySelector('.producto-select');
        if (primerSelect) {
            return primerSelect.innerHTML.replace(/name="productos\[1]\[producto_id\]"/g, '');
        }
        return '';
    }

    // Función para obtener opciones de colores
    function obtenerOpcionesColores() {
        const primerSelect = document.querySelector('select[name="productos[1][color_id]"]');
        if (primerSelect) {
            return primerSelect.innerHTML.replace(/name="productos\[1\]\[color_id\]"/g, '');
        }
        return '';
    }

    // Función para obtener opciones de tallas
    function obtenerOpcionesTallas() {
        const primerSelect = document.querySelector('.talla-select');
        if (primerSelect) {
            return primerSelect.innerHTML.replace(/name="productos\[1\]\[talla_id\]"/g, '');
        }
        return '';
    }

    // Función para cargar precio del producto
    function cargarPrecioProducto(productoSelect, precioInput) {
        const selectedOption = productoSelect.options[productoSelect.selectedIndex];
        const precio = parseFloat(selectedOption.getAttribute('data-precio')) || 0;
        precioInput.value = precio.toFixed(2);
        calcularSubtotalProducto(productoSelect);
    }

    // Función para calcular subtotal de un producto
    function calcularSubtotalProducto(productoSelect) {
        const fila = productoSelect.closest('.producto-row');
        const cantidadInput = fila.querySelector('.cantidad-input');
        const precioInput = fila.querySelector('.precio-input');
        const subtotalInput = fila.querySelector('.subtotal-input');
        
        const cantidad = parseFloat(cantidadInput.value) || 0;
        const precio = parseFloat(precioInput.value) || 0;
        const subtotal = cantidad * precio;
        
        subtotalInput.value = subtotal.toFixed(2);
        calcularTotal();
    }

    // Función para agregar producto
    function agregarProducto() {
        console.log('Agregando nuevo producto');
        productoCounter++;
        const nuevaFila = crearFilaProducto(productoCounter);
        const container = document.getElementById('productosContainer');
        container.appendChild(nuevaFila);
        
        // Configurar event listeners para la nueva fila
        configurarEventListeners(nuevaFila);
        
        // Mostrar botón de remover en la primera fila
        if (productoCounter > 1) {
            document.querySelector('.remover-producto').style.display = 'block';
        }
        
        mostrarNotificacion('Nuevo producto agregado', 'success');
        console.log('Producto agregado exitosamente');
    }

    // Función para configurar event listeners en una fila
    function configurarEventListeners(fila) {
        const productoSelect = fila.querySelector('.producto-select');
        const cantidadInput = fila.querySelector('.cantidad-input');
        const precioInput = fila.querySelector('.precio-input');
        
        // Event listener para cambio de producto
        if (productoSelect) {
            productoSelect.addEventListener('change', function() {
                cargarPrecioProducto(this, precioInput);
            });
        }
        
        // Event listener para cambio de cantidad
        if (cantidadInput) {
            cantidadInput.addEventListener('input', function() {
                calcularSubtotalProducto(productoSelect);
            });
        }
    }

    // Función para remover producto
    function removerProducto(elemento) {
        const fila = elemento.closest('.producto-row');
        if (productoCounter > 1) {
            fila.remove();
            productoCounter--;
            
            if (productoCounter === 1) {
                document.querySelector('.remover-producto').style.display = 'none';
            }
        }
    }

    // Función para calcular total
    function calcularTotal() {
        const subtotales = document.querySelectorAll('.subtotal-input');
        let total = 0;
        subtotales.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        document.getElementById('total_venta').value = total.toFixed(2);
        document.getElementById('total_venta_display').textContent = '$' + total.toFixed(2);
    }

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Inicializando nueva venta');
        
        // Configurar la primera fila de producto
        const primeraFila = document.querySelector('.producto-row');
        if (primeraFila) {
            configurarEventListeners(primeraFila);
        }
        
        // Event listener para agregar producto
        const btnAgregar = document.getElementById('agregarProducto');
        if (btnAgregar) {
            btnAgregar.addEventListener('click', agregarProducto);
            console.log('Event listener agregado al botón agregar producto');
        }
        
        // Event listener para remover productos
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remover-producto')) {
                removerProducto(e.target.closest('.remover-producto'));
            }
        });
    });
    </script>
</body>
</html> 