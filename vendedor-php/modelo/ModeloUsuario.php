<?php
class ModeloUsuario {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function autenticar($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE correo = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && $password === $usuario['contraseña']) {
            return $usuario;
        }
        
        return false;
    }
    
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function obtenerPorEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE correo = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function crear($datos) {
        $sql = "INSERT INTO usuario (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?);";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['correo'],
            $datos['contraseña'],
            $datos['rol'] ?? 'vendedor'
        ]);
    }
    
    public function actualizar($id, $datos) {
        $sql = "UPDATE usuario SET nombre = ?, correo = ?, rol = ? WHERE id = ?;";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['correo'],
            $datos['rol'],
            $id
        ]);
    }
    
    public function actualizarContraseña($id, $nuevaContraseña) {
        $sql = "UPDATE usuario SET contraseña = ? WHERE id = ?;";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nuevaContraseña, $id]);
    }
    
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM usuario WHERE id = ?;");
        return $stmt->execute([$id]);
    }
    
    public function obtenerTodos() {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?> 