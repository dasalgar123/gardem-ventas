<?php
require_once __DIR__ . '/../modelo/ModeloUsuario.php';

class ControladorAuth {
    private $pdo;
    private $modelo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->modelo = new ModeloUsuario($pdo);
    }
    
    public function autenticarUsuario($email, $password) {
        return $this->modelo->autenticar($email, $password);
    }
    
    public function iniciarSesion($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_email'] = $usuario['correo'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
    }
    
    public function cerrarSesion() {
        session_destroy();
        session_start();
    }
    
    public function verificarSesion() {
        return isset($_SESSION['usuario_id']);
    }
    
    public function obtenerUsuarioActual() {
        if ($this->verificarSesion()) {
            return [
                'id' => $_SESSION['usuario_id'],
                'nombre' => $_SESSION['usuario_nombre'],
                'email' => $_SESSION['usuario_email'],
                'rol' => $_SESSION['usuario_rol']
            ];
        }
        return null;
    }
}
?> 