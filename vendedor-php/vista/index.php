<?php
session_start();
require_once '../config/database.php';
require_once '../controlador/ControladorIndex.php';
require_once '../controlador/ControladorProductos.php';
require_once '../controlador/ControladorPedidos.php';
require_once '../controlador/ControladorMenuPrincipal.php';
require_once '../controlador/ControladorAuth.php';
require_once '../modelo/ModeloProducto.php';
require_once '../modelo/ModeloCliente.php';
require_once '../modelo/ModeloPedido.php';
require_once '../modelo/ModeloVenta.php';
require_once '../modelo/ModeloUsuario.php';
$controlador = new ControladorIndex($pdo);

// Verificar si el usuario está logueado
if (!$controlador->verificarSesion()) {
    header('Location: login.php');
    exit();
}

// Obtener la página solicitada
$page = $controlador->obtenerPaginaSolicitada();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Vendedor - <?php echo ucfirst($page); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-store me-2"></i>
                Sistema de Vendedor
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'menu_principal' ? 'active' : ''; ?>" href="index.php?page=menu_principal">
                            <i class="fas fa-tachometer-alt me-1"></i>Menú Principal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'ventas' ? 'active' : ''; ?>" href="index.php?page=ventas">
                            <i class="fas fa-shopping-cart me-1"></i>Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'pedidos' ? 'active' : ''; ?>" href="index.php?page=pedidos">
                            <i class="fas fa-clipboard-list me-1"></i>Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'clientes' ? 'active' : ''; ?>" href="index.php?page=clientes">
                            <i class="fas fa-users me-1"></i>Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'productos' ? 'active' : ''; ?>" href="index.php?page=productos">
                            <i class="fas fa-box me-1"></i>Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'inventario' ? 'active' : ''; ?>" href="index.php?page=inventario">
                            <i class="fas fa-warehouse me-1"></i>Inventario
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php?page=perfil">
                                <i class="fas fa-user-cog me-2"></i>Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="index.php?page=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container-fluid mt-4">
        <?php $controlador->incluirPagina($page); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/app.js"></script>
</body>
</html> 