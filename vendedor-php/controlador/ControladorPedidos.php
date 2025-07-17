<?php
class ControladorPedidos {
    private $pdo;
    private $modelo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->modelo = new ModeloPedido($pdo);
    }
    
    public function obtenerPedidos($filtros = []) {
        return $this->modelo->obtenerTodos($filtros);
    }
    
    public function calcularEstadisticas($pedidos) {
        return $this->modelo->calcularEstadisticas($pedidos);
    }
    
    public function obtenerPedidoPorId($id) {
        return $this->modelo->obtenerPorId($id);
    }
    
    public function crearPedido($datos) {
        return $this->modelo->crear($datos);
    }
    
    public function actualizarPedido($id, $datos) {
        return $this->modelo->actualizar($id, $datos);
    }
    
    public function eliminarPedido($id) {
        return $this->modelo->eliminar($id);
    }
}
?> 