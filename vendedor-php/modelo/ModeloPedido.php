<?php
class ModeloPedido {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function obtenerTodos($filtros = []) {
        $search = isset($filtros['search']) ? trim($filtros['search']) : '';
        $date_from = isset($filtros['date_from']) ? $filtros['date_from'] : '';
        $date_to = isset($filtros['date_to']) ? $filtros['date_to'] : '';

        $sql = "SELECT id, nombre_cliente, telefono, productos, total, fecha, correo, direccion, comentarios FROM pedidos WHERE 1=1";
        $params = [];
        if ($search) {
            $sql .= " AND (nombre_cliente LIKE ? OR telefono LIKE ? OR correo LIKE ? OR productos LIKE ?)";
            $like = "%$search%";
            $params = array_merge($params, [$like, $like, $like, $like]);
        }
        if ($date_from) {
            $sql .= " AND fecha >= ?";
            $params[] = $date_from . " 00:00:00";
        }
        if ($date_to) {
            $sql .= " AND fecha <= ?";
            $params[] = $date_to . " 23:59:59";
        }
        $sql .= " ORDER BY fecha DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function crear($datos) {
        $sql = "INSERT INTO pedidos (nombre_cliente, telefono, productos, total, fecha, correo, direccion, comentarios) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre_cliente'],
            $datos['telefono'],
            $datos['productos'],
            $datos['total'],
            $datos['fecha'] ?? date('Y-m-d H:i:s'),
            $datos['correo'] ?? null,
            $datos['direccion'] ?? null,
            $datos['comentarios'] ?? null
        ]);
    }
    
    public function actualizar($id, $datos) {
        $sql = "UPDATE pedidos SET nombre_cliente = ?, telefono = ?, productos = ?, total = ?, fecha = ?, correo = ?, direccion = ?, comentarios = ? WHERE id = ?;";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre_cliente'],
            $datos['telefono'],
            $datos['productos'],
            $datos['total'],
            $datos['fecha'],
            $datos['correo'] ?? null,
            $datos['direccion'] ?? null,
            $datos['comentarios'] ?? null,
            $id
        ]);
    }
    
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id = ?;");
        return $stmt->execute([$id]);
    }
    
    public function calcularEstadisticas($pedidos) {
        $total_pedidos = count($pedidos);
        $total_ventas = array_sum(array_column($pedidos, 'total'));
        $promedio_pedido = $total_pedidos ? $total_ventas / $total_pedidos : 0;
        return [
            'total_pedidos' => $total_pedidos,
            'total_ventas' => $total_ventas,
            'promedio_pedido' => $promedio_pedido
        ];
    }
}
?> 