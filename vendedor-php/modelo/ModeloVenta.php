<?php
class ModeloVenta {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function obtenerTodas($filtros = []) {
        $busqueda = isset($filtros['buscar']) ? $filtros['buscar'] : '';
        
        $sql = "SELECT v.*, c.nombre as nombre_cliente FROM productos_ventas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                WHERE 1=1";
        $params = [];
        
        if ($busqueda) {
            $sql .= " AND (c.nombre LIKE ? OR v.productos LIKE ?)";
            $like = "%$busqueda%";
            $params = [$like, $like];
        }
        
        $sql .= " ORDER BY v.fecha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos_ventas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function crear($datos) {
        $sql = "INSERT INTO productos_ventas (cliente_id, productos, total, fecha, usuario_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['cliente_id'],
            $datos['productos'],
            $datos['total'],
            $datos['fecha'] ?? date('Y-m-d H:i:s'),
            $datos['usuario_id']
        ]);
    }
    
    public function actualizar($id, $datos) {
        $sql = "UPDATE productos_ventas SET cliente_id = ?, productos = ?, total = ?, fecha = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['cliente_id'],
            $datos['productos'],
            $datos['total'],
            $datos['fecha'],
            $id
        ]);
    }
    
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM productos_ventas WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function calcularEstadisticas($ventas) {
        $total_ventas = count($ventas);
        $total_ingresos = array_sum(array_column($ventas, 'total'));
        $promedio_venta = $total_ventas ? $total_ingresos / $total_ventas : 0;
        return [
           'total_ventas' => $total_ventas,
           'total_ingresos' => $total_ingresos,
           'promedio_venta' => $promedio_venta
        ];
    }
}
?> 