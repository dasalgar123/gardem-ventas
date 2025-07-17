<?php
/**
 * Configuración de la aplicación para el sistema de vendedor
 */

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Vendedor');
define('APP_VERSION', 1.0);
define('APP_URL', 'http://localhost/vendedor-php');
define('APP_PATH', __DIR__ . '/../');

// Configuración de sesión
define('SESSION_NAME', 'vendedor_session');
define('SESSION_LIFETIME', 3601); // 1 hora

// Configuración de seguridad
define('HASH_COST', 12);
define('CSRF_TOKEN_NAME', 'rf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 90); // 15 minutos

// Configuración de archivos
define('UPLOAD_PATH', APP_PATH . 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); //5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Configuración de paginación
define('ITEMS_PER_PAGE', 20);

// Configuración de logs
define('LOG_PATH', APP_PATH . 'logs/');
define('LOG_LEVEL', 'INFO');

// Configuración de desarrollo
define('DEBUG_MODE', true);
define('SHOW_ERRORS', true);

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de headers de seguridad
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1 mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Configuración de errores
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

// Función para obtener configuración
function getConfig($key, $default = null) {
    $config = [
        'app_name' => APP_NAME,
        'app_version' => APP_VERSION,
        'app_url' => APP_URL,
        'debug_mode' => DEBUG_MODE,
        'items_per_page' => ITEMS_PER_PAGE,
        'upload_path' => UPLOAD_PATH,
        'log_path' => LOG_PATH,
    ];
    
    return $config[$key] ?? $default;
}

// Función para validar configuración
function validateConfig() {
    $errors = [];
    
    // Verificar directorios necesarios
    $directories = [
        UPLOAD_PATH => 'Directorio de uploads',
        LOG_PATH => 'Directorio de logs'
    ];
    
    foreach ($directories as $path => $name) {
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true)) {
                $errors[] = "No se puede crear el directorio: $name ($path)";
            }
        } elseif (!is_writable($path)) {
            $errors[] = "El directorio no es escribible: $name ($path)";
        }
    }
    
    return $errors;
}

// Función para inicializar la aplicación
function initializeApp() {
    // Validar configuración
    $errors = validateConfig();
    if (!empty($errors)) {
        if (DEBUG_MODE) {
            echo '<h2>Errores de configuración:</h2>';
            echo '<ul>';
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul>';
        } else {
            error_log('Errores de configuración: ' . implode(', ', $errors));
        }
        return false;
    }
    
    return true;
}

// Función para sanitizar entrada
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Función para generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Función para validar token CSRF
function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Función para log
function logMessage($level, $message, $context = []) {
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    
    $logFile = LOG_PATH . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? json_encode($context) : '';
    $logEntry = "[$timestamp] [$level] $message$contextStr\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Función para formatear precio
function formatPrice($price) {
    return '$' . number_format($price, 2, ',', '.');
}

// Función para formatear fecha
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

// Función para obtener el estado de un pedido
function getOrderStatus($status) {
    $statuses = [
        'pendiente' => ['label' => 'Pendiente', 'class' => 'warning'],
        'en_proceso' => ['label' => 'En Proceso', 'class' => 'info'],
        'completado' => ['label' => 'Completado', 'class' => 'success'],
        'cancelado' => ['label' => 'Cancelado', 'class' => 'danger'],
    ];
    
    return $statuses[$status] ?? ['label' => 'Desconocido', 'class' => 'secondary'];
}

// Inicializar aplicación
if (!initializeApp()) {
    die('Error al inicializar la aplicación');
}
?> 