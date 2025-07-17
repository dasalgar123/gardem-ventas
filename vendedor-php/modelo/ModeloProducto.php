<?php
class ModeloProducto {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function obtenerTodos($filtros = []) {
        $tipo_filtro = isset($filtros['tipo']) ? $filtros['tipo'] : '';
        $busqueda = isset($filtros['buscar']) ? $filtros['buscar'] : '';
        
        $where_conditions = [];
        $params = [];
        
        if ($tipo_filtro) {
            $where_conditions[] = "tipo_producto = ?";
            $params[] = $tipo_filtro;
        }
        
        if ($busqueda) {
            $where_conditions[] = "(nombre LIKE ? OR descripcion LIKE ?)";
            $params[] = "%$busqueda%";
            $params[] = "%$busqueda%";
        }
        
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = " WHERE " . implode(' AND ', $where_conditions);
        }
        
        $sql = "SELECT * FROM productos $where_clause ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function crear($datos) {
        $sql = "INSERT INTO productos (nombre, tipo_producto, precio, descripcion, imagen) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['tipo_producto'],
            $datos['precio'],
            $datos['descripcion'],
            $datos['imagen'] ?? null
        ]);
    }
    
    public function actualizar($id, $datos) {
        $sql = "UPDATE productos SET nombre = ?, tipo_producto = ?, precio = ?, descripcion = ?, imagen = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['tipo_producto'],
            $datos['precio'],
            $datos['descripcion'],
            $datos['imagen'] ?? null,
            $id
        ]);
    }
    
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function obtenerEstadisticasPorTipo() {
        $stmt = $this->pdo->prepare("SELECT tipo_producto, COUNT(*) as total FROM productos GROUP BY tipo_producto");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function obtenerRecientes($limite = 5) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function obtenerTipos() {
        return ['caballero', 'dama', 'niño', 'niña'];
    }
}
?> 