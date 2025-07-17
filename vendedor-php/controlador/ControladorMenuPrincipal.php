<?php
class ControladorMenuPrincipal {
    private $pdo;
    private $modeloProducto;
    private $modeloCliente;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->modeloProducto = new ModeloProducto($pdo);
        $this->modeloCliente = new ModeloCliente($pdo);
    }
    
    public function obtenerEstadisticas() {
        $stats = [];
        
        // Total de productos
        $productos = $this->modeloProducto->obtenerTodos();
        $stats['total_productos'] = count($productos);
        
        // Total de productos por categoría
        $categorias = ['caballero', 'dama', 'niño', 'niña'];
        foreach ($categorias as $cat) {
            $filtros = ['tipo' => $cat];
            $productos_cat = $this->modeloProducto->obtenerTodos($filtros);
            $stats['productos_' . $cat] = count($productos_cat);
        }
        
        // Total de clientes
        $clientes = $this->modeloCliente->obtenerTodos();
        $stats['total_clientes'] = count($clientes);
        
        return $stats;
    }
    
    public function obtenerProductosRecientes($limite = 5) {
        return $this->modeloProducto->obtenerRecientes($limite);
    }
}
?> 