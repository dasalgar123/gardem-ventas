<?php
class ModeloCliente {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function obtenerTodos($filtros = []) {
        $busqueda = isset($filtros['buscar']) ? $filtros['buscar'] : '';
        
        $sql = "SELECT * FROM cliente WHERE 1=1";
        $params = [];
        
        if ($busqueda) {
            $sql .= " AND (nombre LIKE ? OR correo LIKE ?)";
            $like = "%$busqueda%";
            $params = [$like, $like];
        }
        
        $sql .= " ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM cliente WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function crear($datos) {
        $sql = "INSERT INTO cliente (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?);";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['correo'],
            $datos['contraseña'],
            $datos['rol'] ?? 'cliente'
        ]);
    }
    
    public function actualizar($id, $datos) {
        $sql = "UPDATE cliente SET nombre = ?, correo = ? WHERE id = ?;";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['correo'],
            $id
        ]);
    }
    
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM cliente WHERE id = ?);");
        return $stmt->execute([$id]);
    }
    
    public function obtenerTotal() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM cliente");
        $stmt->execute();
        return $stmt->fetch()['total'];
    }
}
?> 