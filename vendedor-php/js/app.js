/**
 * JavaScript unificado para el Sistema de Vendedor
 * Incluye todas las funcionalidades de ventas, pedidos y productos
 */

// Variables globales
let productoCounter = 1;

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Inicializa todas las funcionalidades de la aplicación
 */
function initializeApp() {
    // Inicializar funcionalidades comunes
    initializeForms();
    initializeTables();
    initializeNotifications();
    
    // Inicializar funcionalidades específicas según la página
    const currentPage = getCurrentPage();
    initializePageSpecific(currentPage);
    
    console.log('Sistema de Vendedor inicializado correctamente');
}

/**
 * Obtiene la página actual
 */
function getCurrentPage() {
    const path = window.location.pathname;
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    
    if (page) return page;
    return 'dashboard';
}

/**
 * Inicializa funcionalidades específicas por página
 */
function initializePageSpecific(page) {
    switch(page) {
        case 'ventas':
            initializeVentas();
            break;
        case 'pedidos':
            initializePedidos();
            break;
        case 'productos':
            initializeProductos();
            break;
        case 'clientes':
            initializeClientes();
            break;
    }
}

/**
 * Inicializa los formularios
 */
function initializeForms() {
    // Validación de formularios
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', validateForm);
    });
    
    // Limpiar formularios
    const resetButtons = document.querySelectorAll('.btn-reset');
    resetButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            if (form) {
                form.reset();
                clearFormErrors(form);
            }
        });
    });
}

/**
 * Valida un formulario
 */
function validateForm(e) {
    const form = e.target;
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    clearFormErrors(form);
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            showFieldError(field, 'Este campo es requerido');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        showNotification('Por favor, completa todos los campos requeridos', 'error');
    }
}

/**
 * Muestra error en un campo
 */
function showFieldError(field, message) {
    field.classList.add('is-invalid');
    
    let errorMsg = field.parentNode.querySelector('.invalid-feedback');
    if (!errorMsg) {
        errorMsg = document.createElement('div');
        errorMsg.className = 'invalid-feedback';
        field.parentNode.appendChild(errorMsg);
    }
    errorMsg.textContent = message;
}

/**
 * Limpia errores de un formulario
 */
function clearFormErrors(form) {
    const errorFields = form.querySelectorAll('.is-invalid');
    errorFields.forEach(field => {
        field.classList.remove('is-invalid');
    });
    
    const errorMessages = form.querySelectorAll('.invalid-feedback');
    errorMessages.forEach(msg => {
        msg.remove();
    });
}

/**
 * Inicializa las tablas
 */
function initializeTables() {
    // Búsqueda en tiempo real
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const table = this.closest('.card').querySelector('.table');
            
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });
}

/**
 * Inicializa las notificaciones
 */
function initializeNotifications() {
    // Auto-remover notificaciones después de 5 segundos
    setInterval(() => {
        const notifications = document.querySelectorAll('.alert');
        notifications.forEach(notification => {
            if (!notification.classList.contains('alert-persistent')) {
                notification.remove();
            }
        });
    }, 5000);
}

/**
 * Muestra una notificación
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insertar al inicio del contenido principal
    const mainContent = document.querySelector('.container-fluid');
    if (mainContent) {
        mainContent.insertBefore(notification, mainContent.firstChild);
    }
    
    // Auto-remove después de 5 segundos
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

/**
 * Confirma una acción
 */
function confirmAction(message = '¿Estás seguro de que quieres realizar esta acción?') {
    return confirm(message);
}

// ========================================
// FUNCIONALIDADES DE VENTAS
// ========================================

/**
 * Inicializa funcionalidades específicas de ventas
 */
function initializeVentas() {
    console.log('Inicializando funcionalidades de ventas');
    
    // Verificar que estamos en la página de ventas
    const productosContainer = document.getElementById('productosContainer');
    if (!productosContainer) {
        console.log('No se encontró productosContainer - no estamos en la página de ventas');
        return;
    }
    
    console.log('Estamos en la página de ventas');
    
    // Event listener para agregar producto
    const btnAgregarProducto = document.getElementById('agregarProducto');
    console.log('Botón agregarProducto encontrado:', btnAgregarProducto);
    
    if (btnAgregarProducto) {
        btnAgregarProducto.addEventListener('click', agregarProductoVenta);
        console.log('Event listener agregado para el botón de agregar producto');
    } else {
        console.log('ERROR: No se encontró el botón agregarProducto');
    }
    
    // Event listener para remover productos (delegación de eventos)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remover-producto')) {
            removerProducto(e.target.closest('.remover-producto'));
        }
    });
    
    // Calcular totales
    const productoSelects = document.querySelectorAll('.producto-select');
    productoSelects.forEach(select => {
        select.addEventListener('change', calcularTotalesVentas);
    });
    
    const cantidadInputs = document.querySelectorAll('.cantidad-input');
    cantidadInputs.forEach(input => {
        input.addEventListener('input', calcularTotalesVentas);
    });
}

/**
 * Función para crear una nueva fila de producto en ventas
 */
function crearFilaProducto(numero) {
    const nuevaFila = document.createElement('div');
    nuevaFila.className = 'producto-row border rounded p-3 mb-3';
    nuevaFila.setAttribute('data-producto-id', numero);
    
    nuevaFila.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Producto #${numero}</h6>
            <button type="button" class="btn btn-danger btn-sm remover-producto">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Producto</label>
                <select class="form-select producto-select" name="productos[${numero}][producto_id]" required>
                    <option value="">Seleccionar producto...</option>
                    ${obtenerOpcionesProductos()}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Color</label>
                <select class="form-select" name="productos[${numero}][color_id]" required>
                    <option value="">Color...</option>
                    ${obtenerOpcionesColores()}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Talla</label>
                <select class="form-select talla-select" name="productos[${numero}][talla_id]" required>
                    <option value="">Talla...</option>
                    ${obtenerOpcionesTallas()}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number" class="form-control cantidad-input" name="productos[${numero}][cantidad]" placeholder="Cantidad" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Precio</label>
                <input type="number" class="form-control precio-input" name="productos[${numero}][precio]" placeholder="Precio" step="0.01" readonly>
            </div>
            <div class="col-md-1">
                <label class="form-label">Subtotal</label>
                <input type="number" class="form-control subtotal-input" name="productos[${numero}][subtotal]" placeholder="Subtotal" readonly>
            </div>
        </div>
    `;
    
    return nuevaFila;
}

/**
 * Función para obtener opciones de productos
 */
function obtenerOpcionesProductos() {
    const primerSelect = document.querySelector('.producto-select');
    if (primerSelect) {
        return primerSelect.innerHTML.replace(/name="productos\[1]\[producto_id\]"/g, '');
    }
    return '';
}

/**
 * Función para obtener opciones de colores
 */
function obtenerOpcionesColores() {
    const primerSelect = document.querySelector('select[name="productos[1][color_id]"]');
    if (primerSelect) {
        return primerSelect.innerHTML.replace(/name="productos\[1\]\[color_id\]"/g, '');
    }
    return '';
}

/**
 * Función para obtener opciones de tallas
 */
function obtenerOpcionesTallas() {
    const primerSelect = document.querySelector('.talla-select');
    if (primerSelect) {
        return primerSelect.innerHTML.replace(/name="productos\[1\]\[talla_id\]"/g, '');
    }
    return '';
}

/**
 * Función para agregar un nuevo producto en ventas
 */
function agregarProductoVenta() {
    console.log('Función agregarProductoVenta ejecutada');
    alert('Función ejecutada - agregando producto...');
    
    try {
        productoCounter++;
        console.log('Nuevo contador:', productoCounter);
        
        const nuevaFila = crearFilaProducto(productoCounter);
        console.log('Nueva fila creada:', nuevaFila);
        
        const container = document.getElementById('productosContainer');
        console.log('Container encontrado:', container);
        
        if (container && nuevaFila) {
            container.appendChild(nuevaFila);
            console.log('Fila agregada al container');
            
            // Mostrar botón de remover en la primera fila si hay más de una
            if (productoCounter > 1) {
                const primerRemoverBtn = document.querySelector('.remover-producto');
                if (primerRemoverBtn) {
                    primerRemoverBtn.style.display = 'block';
                }
            }
            
            // Mostrar notificación
            showNotification('Nuevo producto agregado. Selecciona el producto de la lista.', 'info');
            
            console.log('Nuevo producto agregado exitosamente:', productoCounter);
        } else {
            console.error('Error: Container o nueva fila no encontrados');
        }
    } catch (error) {
        console.error('Error en agregarProductoVenta:', error);
    }
}

/**
 * Función para remover un producto en ventas
 */
function removerProducto(elemento) {
    const fila = elemento.closest('.producto-row');
    if (productoCounter > 1) {
        fila.remove();
        productoCounter--;
        
        // Ocultar botón de remover en la primera fila si solo queda una
        if (productoCounter === 1) {
            document.querySelector('.remover-producto').style.display = 'none';
        }
        
        console.log('Producto removido');
    }
}

/**
 * Calcula los totales en ventas
 */
function calcularTotalesVentas() {
    let subtotal = 0;
    const productoRows = document.querySelectorAll('.producto-row');
    
    productoRows.forEach(row => {
        const cantidad = row.querySelector('.cantidad-input');
        const precio = row.querySelector('.precio-input');
        const subtotalInput = row.querySelector('.subtotal-input');
        
        if (cantidad && precio && subtotalInput) {
            const cantidadVal = parseFloat(cantidad.value) || 0;
            const precioVal = parseFloat(precio.value) || 0;
            const itemSubtotal = cantidadVal * precioVal;
            
            subtotalInput.value = itemSubtotal.toFixed(2);
            subtotal += itemSubtotal;
        }
    });
    
    const iva = subtotal * 0.16;
    const total = subtotal + iva;
    
    const subtotalElement = document.getElementById('subtotal');
    const ivaElement = document.getElementById('iva');
    const totalElement = document.getElementById('total');
    
    if (subtotalElement) subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    if (ivaElement) ivaElement.textContent = `$${iva.toFixed(2)}`;
    if (totalElement) totalElement.textContent = `$${total.toFixed(2)}`;
}

// ========================================
// FUNCIONALIDADES DE PEDIDOS
// ========================================

/**
 * Inicializa funcionalidades específicas de pedidos
 */
function initializePedidos() {
    console.log('Inicializando funcionalidades de pedidos');
    
    // Event listener para copiar correo
    document.querySelectorAll('.copy-email').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const email = this.getAttribute('data-email');
            if (email) {
                navigator.clipboard.writeText(email);
                showNotification('Correo copiado al portapapeles', 'info');
            }
        });
    });
}

// ========================================
// FUNCIONALIDADES DE PRODUCTOS
// ========================================

/**
 * Inicializa funcionalidades específicas de productos
 */
function initializeProductos() {
    console.log('Inicializando funcionalidades de productos');
    
    // Auto-submit del formulario de filtros cuando cambien los valores
    const filtroTipo = document.getElementById('tipo');
    const filtroBusqueda = document.getElementById('buscar');
    
    if (filtroTipo) {
        filtroTipo.addEventListener('change', function() {
            document.querySelector('form[method="GET"]').submit();
        });
    }
    
    if (filtroBusqueda) {
        let timeout;
        filtroBusqueda.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                document.querySelector('form[method="GET"]').submit();
            }, 500); // Esperar 500 después de que el usuario deje de escribir
        });
    }
    
    // Event listeners para formularios de producto
    const formularioProducto = document.getElementById('formulario-producto');
    if (formularioProducto) {
        formularioProducto.addEventListener('submit', function(e) {
            if (!validarFormularioProducto()) {
                e.preventDefault();
            }
        });
    }
    
    // Event listener para previsualización de imagen
    const inputImagen = document.getElementById('imagen');
    if (inputImagen) {
        inputImagen.addEventListener('change', function() {
            previsualizarImagen(this);
        });
    }
    
    // Event listeners para cálculo de precio
    const inputPrecio = document.getElementById('precio');
    const inputDescuento = document.getElementById('descuento');
    
    if (inputPrecio) {
        inputPrecio.addEventListener('input', calcularPrecioConDescuento);
    }
    
    if (inputDescuento) {
        inputDescuento.addEventListener('input', calcularPrecioConDescuento);
    }
}

/**
 * Función para confirmar eliminación de productos
 */
function confirmarEliminar(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        window.location.href = 'index.php?page=productos&action=eliminar&id=' + id;
    }
}

/**
 * Función para filtrar productos dinámicamente
 */
function filtrarProductos() {
    const tipo = document.getElementById('tipo').value;
    const busqueda = document.getElementById('buscar').value;
    
    // Construir URL con filtros
    let url = 'index.php?page=productos';
    if (tipo) url += '&tipo=' + encodeURIComponent(tipo);
    if (busqueda) url += '&buscar=' + encodeURIComponent(busqueda);
    
    window.location.href = url;
}

/**
 * Función para limpiar filtros
 */
function limpiarFiltros() {
    window.location.href = 'index.php?page=productos';
}

/**
 * Función para exportar productos
 */
function exportarProductos() {
    const tipo = document.getElementById('tipo').value;
    const busqueda = document.getElementById('buscar').value;
    
    let url = 'exportar_productos.php?';
    if (tipo) url += 'tipo=' + encodeURIComponent(tipo);
    if (busqueda) url += '&buscar=' + encodeURIComponent(busqueda);
    
    window.open(url, '_blank');
}

/**
 * Función para mostrar detalles del producto en modal
 */
function mostrarDetallesProducto(id) {
    // Aquí se podría hacer una petición AJAX para obtener los detalles
    // Por ahora redirigimos a la página de detalles
    window.location.href = 'index.php?page=productos&action=ver&id=' + id;
}

/**
 * Función para editar producto
 */
function editarProducto(id) {
    window.location.href = 'index.php?page=productos&action=editar&id=' + id;
}

/**
 * Función para validar formulario de producto
 */
function validarFormularioProducto() {
    const nombre = document.getElementById('nombre').value.trim();
    const tipo = document.getElementById('tipo_producto').value;
    const precio = document.getElementById('precio').value;
    const descripcion = document.getElementById('descripcion').value.trim();
    
    if (!nombre) {
        alert('El nombre del producto es obligatorio');
        return false;
    }
    
    if (!tipo) {
        alert('Debe seleccionar un tipo de producto');
        return false;
    }
    
    if (!precio || isNaN(precio) || parseFloat(precio) <= 0) {
        alert('El precio debe ser un número válido mayor a 0');
        return false;
    }
    
    if (!descripcion) {
        alert('La descripción es obligatoria');
        return false;
    }
    
    return true;
}

/**
 * Función para previsualizar imagen
 */
function previsualizarImagen(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-imagen').src = e.target.result;
            document.getElementById('preview-imagen').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Función para calcular precio con descuento
 */
function calcularPrecioConDescuento() {
    const precio = parseFloat(document.getElementById('precio').value) || 0;
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    const precioFinal = precio - (precio * descuento / 100);
    
    document.getElementById('precio_final').value = precioFinal.toFixed(2);
    document.getElementById('precio_final_display').textContent = '$' + precioFinal.toFixed(2);
}

// ========================================
// FUNCIONALIDADES DE CLIENTES
// ========================================

/**
 * Inicializa funcionalidades específicas de clientes
 */
function initializeClientes() {
    console.log('Inicializando funcionalidades de clientes');
}

// ========================================
// FUNCIONES COMPATIBILIDAD (mantener para compatibilidad)
// ========================================

/**
 * Agrega un nuevo producto al formulario (compatibilidad)
 */
function agregarProducto() {
    const container = document.getElementById('productosContainer');
    const productoItems = container.querySelectorAll('.producto-item');
    const newIndex = productoItems.length;
    
    const newProductoItem = document.createElement('div');
    newProductoItem.className = 'row producto-item mb-2';
    newProductoItem.innerHTML = `
        <div class="col-md-4">
            <select class="form-select producto-select" name="productos[${newIndex}][id]" required>
                <option value="">Seleccionar producto</option>
                ${getProductosOptions()}
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" class="form-control cantidad-input" 
                   name="productos[${newIndex}][cantidad]" placeholder="Cantidad" 
                   min="1" required>
        </div>
        <div class="col-md-2">
            <input type="number" class="form-control precio-input" 
                   name="productos[${newIndex}][precio]" placeholder="Precio" 
                   step="0.01" readonly>
        </div>
        <div class="col-md-2">
            <span class="form-control subtotal-display">$00</span>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm remove-producto">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newProductoItem);
    
    // Agregar event listeners al nuevo elemento
    const newSelect = newProductoItem.querySelector('.producto-select');
    const newCantidad = newProductoItem.querySelector('.cantidad-input');
    const newRemoveBtn = newProductoItem.querySelector('.remove-producto');
    
    newSelect.addEventListener('change', calcularTotales);
    newCantidad.addEventListener('input', calcularTotales);
    newRemoveBtn.addEventListener('click', removeProducto);
}

/**
 * Remueve un producto del formulario (compatibilidad)
 */
function removeProducto() {
    this.closest('.producto-item').remove();
    calcularTotales();
}

/**
 * Calcula los totales del formulario (compatibilidad)
 */
function calcularTotales() {
    let subtotal = 0;
    const productoItems = document.querySelectorAll('.producto-item');
    productoItems.forEach(item => {
        const select = item.querySelector('.producto-select');
        const cantidad = item.querySelector('.cantidad-input');
        const precio = item.querySelector('.precio-input');
        const subtotalDisplay = item.querySelector('.subtotal-display');
        
        if (select.value && cantidad.value && precio.value) {
            const cantidadVal = parseFloat(cantidad.value);
            const precioVal = parseFloat(precio.value);
            const itemSubtotal = cantidadVal * precioVal;
            
            subtotalDisplay.textContent = `$${itemSubtotal.toFixed(2)}`;
            subtotal += itemSubtotal;
        }
    });
    
    const iva = subtotal * 0.16;
    const total = subtotal + iva;
    
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('iva').textContent = `$${iva.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}

/**
 * Obtiene las opciones de productos para el select (compatibilidad)
 */
function getProductosOptions() {
    // Esta función debería obtener las opciones de productos desde el servidor
    // Por ahora retornamos opciones de ejemplo
    return `
        <option value="1">Producto 1</option>
        <option value="2">Producto 2</option>
        <option value="3">Producto 3</option>
    `;
}

// Exponer funciones globalmente
window.showNotification = showNotification;
window.confirmAction = confirmAction; 
window.agregarProductoVenta = agregarProductoVenta;
window.removerProducto = removerProducto;
window.confirmarEliminar = confirmarEliminar;
window.filtrarProductos = filtrarProductos;
window.limpiarFiltros = limpiarFiltros;
window.exportarProductos = exportarProductos;
window.mostrarDetallesProducto = mostrarDetallesProducto;
window.editarProducto = editarProducto;
window.validarFormularioProducto = validarFormularioProducto;
window.previsualizarImagen = previsualizarImagen;
window.calcularPrecioConDescuento = calcularPrecioConDescuento; 