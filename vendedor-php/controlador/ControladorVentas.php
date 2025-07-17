<?php
class ControladorVentas {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Obtener el siguiente número de factura
    public function obtenerSiguienteFactura() {
        try {
            $stmt = $this->pdo->query("SELECT MAX(CAST(SUBSTRING(factura, 2) AS UNSIGNED)) as ultimo_numero FROM productos_ventas WHERE factura LIKE 'F%'");
            $result = $stmt->fetch();
            return 'F' . str_pad(($result['ultimo_numero'] ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            return 'F001';
        }
    }
    
    // Obtener clientes activos
    public function obtenerClientes() {
        try {
            $stmt = $this->pdo->query("SELECT id, nombre FROM cliente ORDER BY nombre");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Obtener productos disponibles
    public function obtenerProductos() {
        try {
            $stmt = $this->pdo->query("SELECT id, nombre, precio, descripcion, tipo_producto FROM productos ORDER BY nombre");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Buscar producto por código de barras o nombre
    public function buscarProductoPorCodigo($codigo) {
        try {
            $codigo = trim($codigo);
            
            // Buscar por ID exacto primero
            $stmt = $this->pdo->prepare("SELECT id, nombre, precio, descripcion, tipo_producto FROM productos WHERE id = ?");
            $stmt->execute([$codigo]);
            $producto = $stmt->fetch();
            
            if ($producto) {
                return $producto;
            }
            
            // Si no se encuentra por ID, buscar por nombre (búsqueda parcial)
            $stmt = $this->pdo->prepare("SELECT id, nombre, precio, descripcion, tipo_producto FROM productos WHERE nombre LIKE ? ORDER BY nombre LIMIT 1");
            $stmt->execute(['%' . $codigo . '%']);
            $producto = $stmt->fetch();
            
            if ($producto) {
                return $producto;
            }
            
            // Si no se encuentra por nombre, buscar por descripción
            $stmt = $this->pdo->prepare("SELECT id, nombre, precio, descripcion, tipo_producto FROM productos WHERE descripcion LIKE ? ORDER BY nombre LIMIT 1");
            $stmt->execute(['%' . $codigo . '%']);
            $producto = $stmt->fetch();
            
            return $producto;
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Obtener historial de ventas
    public function obtenerVentas() {
        try {
            $sql = "SELECT pv.*, c.nombre as cliente_nombre 
                    FROM productos_ventas pv 
                    LEFT JOIN cliente c ON pv.cliente_id = c.id 
                    ORDER BY pv.fecha DESC LIMIT 20";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Crear nueva venta con múltiples productos
    public function crearVenta($factura, $cliente_id, $productos) {
        try {
            $this->pdo->beginTransaction();
            
            // Verificar cliente
            $stmt = $this->pdo->prepare("SELECT id FROM cliente WHERE id = ?");
            $stmt->execute([$cliente_id]);
            if (!$stmt->fetch()) {
                throw new Exception('Cliente no válido');
            }
            
            $productos_texto = [];
            $total_venta = 0;
            
            // Procesar cada producto
            foreach ($productos as $producto) {
                // Verificar producto
                $stmt = $this->pdo->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
                $stmt->execute([$producto['producto_id']]);
                $producto_info = $stmt->fetch();
                
                if (!$producto_info) {
                    throw new Exception('Producto no encontrado: ' . $producto['producto_id']);
                }
                
                // Obtener información de color y talla
                $color_nombre = '';
                $talla_nombre = '';
                
                if (!empty($producto['color_id'])) {
                    $stmt = $this->pdo->prepare("SELECT nombre FROM colores WHERE id = ?");
                    $stmt->execute([$producto['color_id']]);
                    $color = $stmt->fetch();
                    $color_nombre = $color ? $color['nombre'] : '';
                }
                
                if (!empty($producto['talla_id'])) {
                    $stmt = $this->pdo->prepare("SELECT nombre FROM tallas WHERE id = ?");
                    $stmt->execute([$producto['talla_id']]);
                    $talla = $stmt->fetch();
                    $talla_nombre = $talla ? $talla['nombre'] : '';
                }
                
                // Construir descripción del producto
                $descripcion = $producto_info['nombre'];
                if ($color_nombre) $descripcion .= " - Color: " . $color_nombre;
                if ($talla_nombre) $descripcion .= " - Talla: " . $talla_nombre;
                $descripcion .= " (x" . $producto['cantidad'] . ")";
                
                $productos_texto[] = $descripcion;
                $total_venta += $producto_info['precio'] * $producto['cantidad'];
            }
            
            // Insertar venta con todos los productos
            $stmt = $this->pdo->prepare("INSERT INTO productos_ventas (cliente_id, factura, productos, total, fecha, usuario_id) VALUES (?, ?, ?, ?, NOW(), ?)");
            $productos_final = implode(" | ", $productos_texto);
            $usuario_id = $_SESSION['usuario_id'] ?? 1;
            $stmt->execute([$cliente_id, $factura, $productos_final, $total_venta, $usuario_id]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    // Obtener colores disponibles
    public function obtenerColores() {
        try {
            $stmt = $this->pdo->query("SELECT id, nombre FROM colores ORDER BY nombre");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Obtener tallas disponibles
    public function obtenerTallas() {
        try {
            $stmt = $this->pdo->query("SELECT id, nombre, categoria FROM tallas ORDER BY categoria, nombre");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Obtener tallas por categoría
    public function obtenerTallasPorCategoria($categoria) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nombre FROM tallas WHERE categoria = ? ORDER BY nombre");
            $stmt->execute([$categoria]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Obtener información del producto incluyendo categoría
    public function obtenerProductoConDetalles($producto_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, t.categoria as categoria_talla FROM productos p 
                                        LEFT JOIN tallas t ON p.tallas_id = t.id 
                                        WHERE p.id = ?");
            $stmt->execute([$producto_id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
}
?> 