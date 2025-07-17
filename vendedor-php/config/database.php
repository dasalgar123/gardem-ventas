<?php
/**
 * Configuración de Base de Datos
 * Sistema de Vendedor
 */

// Configuración de la base de datos
const DB_HOST = 'localhost';
const DB_NAME = 'gardelcatalogo';
const DB_USER = 'root';
const DB_PASS = '';

// Zona horaria
date_default_timezone_set('America/Bogota');

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}

function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Función para verificar si el usuario está logueado
 */
function isLoggedIn() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Función para verificar si el usuario es vendedor
 */
function isVendedor() {
    return isLoggedIn() && $_SESSION['usuario_rol'] === 'vendedor';
}

/**
 * Función para redirigir si no está logueado
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Función para redirigir si no es vendedor
 */
function requireVendedor() {
    if (!isVendedor()) {
        header('Location: login.php');
        exit();
    }
}
?> 