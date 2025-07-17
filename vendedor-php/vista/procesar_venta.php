<?php
session_start();
require_once '../config/database.php';
require_once '../controlador/ControladorVentas.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php?page=login');
    exit();
}

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=ventas');
    exit();
}

try {
    // Crear instancia del controlador
    $controladorVentas = new ControladorVentas($pdo);
    
    // Obtener datos del formulario
    $factura = sanitize($_POST['factura']);
    $cliente_id = (int)$_POST['cliente_id'];
    $productos = $_POST['productos'] ?? [];
    
    // Validaciones básicas
    if (empty($factura) || empty($cliente_id)) {
        throw new Exception('Factura y cliente son obligatorios');
    }
    
    if (empty($productos) || !is_array($productos)) {
        throw new Exception('Debe agregar al menos un producto');
    }
    
    // Validar cada producto
    $productos_validados = [];
    foreach ($productos as $producto) {
        if (empty($producto['producto_id']) || empty($producto['cantidad'])) {
            throw new Exception('Todos los campos de producto son obligatorios');
        }
        
        if ((int)$producto['cantidad'] <= 0) {
            throw new Exception('La cantidad debe ser mayor a 0');
        }
        
        $productos_validados[] = [
            'producto_id' => (int)$producto['producto_id'],
            'color_id' => !empty($producto['color_id']) ? (int)$producto['color_id'] : null,
            'talla_id' => !empty($producto['talla_id']) ? (int)$producto['talla_id'] : null,
            'cantidad' => (int)$producto['cantidad']
        ];
    }
    
    // Crear la venta usando el controlador
    $controladorVentas->crearVenta($factura, $cliente_id, $productos_validados);
    
    // Redireccionar con mensaje de éxito
    header('Location: index.php?page=ventas&success=1&factura=' . urlencode($factura));
    exit();
    
} catch (Exception $e) {
    // Redireccionar con mensaje de error
    header('Location: index.php?page=ventas&error=' . urlencode($e->getMessage()));
    exit();
}


?> 