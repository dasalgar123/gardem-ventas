<?php
class ControladorProductos {
    private $pdo;
    private $modelo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->modelo = new ModeloProducto($pdo);
    }
    
    public function obtenerProductos($filtros = []) {
        return $this->modelo->obtenerTodos($filtros);
    }
    
    public function obtenerEstadisticasPorTipo() {
        return $this->modelo->obtenerEstadisticasPorTipo();
    }
    
    public function obtenerProductoPorId($id) {
        return $this->modelo->obtenerPorId($id);
    }
    
    public function crearProducto($datos) {
        return $this->modelo->crear($datos);
    }
    
    public function actualizarProducto($id, $datos) {
        return $this->modelo->actualizar($id, $datos);
    }
    
    public function eliminarProducto($id) {
        return $this->modelo->eliminar($id);
    }
    
    public function obtenerTiposProducto() {
        return $this->modelo->obtenerTipos();
    }
}
?> 