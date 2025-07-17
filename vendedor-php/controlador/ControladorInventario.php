<?php
// Controlador para la vista de inventario del vendedor (solo lectura)
class ControladorInventario {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function obtenerInventario() {
        try {
            // Consulta simple para obtener inventario con stock
            $sql = "SELECT 
                p.id AS producto_id,
                p.nombre AS producto_nombre,
                p.descripcion,
                p.precio,
                p.tipo_producto,
                COALESCE(SUM(ib.stock_actual), 0) AS stock_total
            FROM productos p
            LEFT JOIN inventario_bodega ib ON p.id = ib.producto_id
            GROUP BY p.id, p.nombre, p.descripcion, p.precio, p.tipo_producto
            ORDER BY p.nombre";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function obtenerEstadisticasInventario() {
        try {
            $inventario = $this->obtenerInventario();
            
            $stats = [
                'total_productos' => count($inventario),
                'productos_disponibles' => 0,
                'productos_agotados' => 0,
                'productos_stock_bajo' => 0,
                'valor_total' => 0
            ];
            
            foreach ($inventario as $item) {
                $stock = (int)$item['stock_total'];
                $precio = (float)$item['precio'];
                
                if ($stock > 10) {
                    $stats['productos_disponibles']++;
                } elseif ($stock > 0) {
                    $stats['productos_stock_bajo']++;
                } else {
                    $stats['productos_agotados']++;
                }
                
                $stats['valor_total'] += ($stock * $precio);
            }
            
            return $stats;
        } catch (Exception $e) {
            return [
                'total_productos' => 0,
                'productos_disponibles' => 0,
                'productos_agotados' => 0,
                'productos_stock_bajo' => 0,
                'valor_total' => 0
            ];
        }
    }
    
    public static function formatPrice($price) {
        return '$' . number_format($price, 2);
    }
    
    public static function getStockStatus($stock) {
        $stock = (int)$stock;
        if ($stock <= 0) {
            return ['status' => 'danger', 'text' => 'Agotado'];
        } elseif ($stock <= 10) {
            return ['status' => 'warning', 'text' => 'Stock Bajo'];
        } else {
            return ['status' => 'success', 'text' => 'Disponible'];
        }
    }
}
?> 