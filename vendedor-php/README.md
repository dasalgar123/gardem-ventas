# Sistema de Vendedor PHP

Un sistema completo de interfaz de vendedor desarrollado en PHP con funcionalidades de ventas, pedidos y gestión de clientes.

## 🚀 Características

- **Login seguro** con autenticación de vendedores
- **Dashboard personalizado** con estadísticas de ventas
- **Gestión de ventas** con productos dinámicos
- **Sistema de pedidos** con seguimiento de estados
- **Gestión de clientes** y productos
- **Interfaz moderna** con Bootstrap53*Responsive design** para móviles y tablets

## 📁 Estructura del Proyecto

```
vendedor-php/
├── config/
│   ├── app.php              # Configuración de la aplicación
│   └── database.php         # Configuración de base de datos
├── vista/
│   ├── login.php            # Página de login
│   ├── dashboard.php        # Panel principal
│   ├── ventas.php          # Gestión de ventas
│   ├── pedidos.php         # Gestión de pedidos
│   ├── productos.php       # Catálogo de productos
│   ├── clientes.php        # Gestión de clientes
│   └── perfil.php          # Perfil del vendedor
├── css/
│   └── style.css           # Estilos personalizados
├── js/
│   └── app.js              # JavaScript principal
├── sql/
│   └── database.sql        # Estructura de base de datos
├── uploads/                # Archivos subidos
├── logs/                   # Archivos de log
└── index.php               # Punto de entrada
```

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP74**Frontend**: HTML5, CSS3, JavaScript ES6+
- **Framework CSS**: Bootstrap 50.3- **Iconos**: Font Awesome 60
- **Base de Datos**: MySQL/MariaDB
- **Arquitectura**: MVC simplificado

## 📋 Módulos Disponibles

### 🔐 Autenticación
- Login seguro con validación
- Control de sesiones
- Logout automático

### 📊 Dashboard
- Estadísticas de ventas del día
- Pedidos pendientes
- Clientes activos
- Últimas ventas

### 🛒 Gestión de Ventas
- Crear nuevas ventas
- Selección de productos dinámicos
- Cálculo automático de totales
- Historial de ventas

### 📋 Gestión de Pedidos
- Crear nuevos pedidos
- Estados de pedidos (Pendiente, En Proceso, Completado)
- Fecha de entrega
- Observaciones

### 👥 Gestión de Clientes
- Registro de clientes
- Información de contacto
- Historial de compras

### 📦 Catálogo de Productos
- Lista de productos disponibles
- Precios y stock
- Categorías

## 🚀 Instalación

1**Clonar o descargar el proyecto**
   ```bash
   # Copiar a tu servidor web
   cp -r vendedor-php/ /ruta/de/tu/servidor/
   ```

2Configurar la base de datos**
   ```bash
   # Importar la estructura de la base de datos
   mysql -u root -p < vendedor-php/sql/database.sql
   ```

3onfigurar la conexión**
   ```php
   // Editar config/database.php
   $host =localhost; $dbname = 'vendedor_system';
   $username = tu_usuario';
   $password =tu_password';
   ```

4. **Configurar permisos**
   ```bash
   chmod 755 -R vendedor-php/
   chmod 777ndedor-php/uploads/
   chmod 777R vendedor-php/logs/
   ```

5. **Acceder al sistema**
   ```
   http://localhost/vendedor-php/
   ```

## 🔑 Credenciales de Prueba

**Vendedor:**
- Email: juan@vendedor.com
- Contraseña: password123# ⚙️ Configuración

### Base de Datos
El sistema incluye datos de prueba:
- 1 vendedor
- 3 clientes
- 5productos
- Ventas y pedidos de ejemplo

### Personalización
- Editar `config/app.php` para cambiar configuraciones
- Modificar `css/style.css` para personalizar estilos
- Ajustar `js/app.js` para funcionalidades específicas

## 📱 Funcionalidades Principales

### Login y Autenticación
- Formulario de login seguro
- Validación de credenciales
- Control de sesiones
- Redirección automática

### Dashboard
- Estadísticas en tiempo real
- Tarjetas informativas
- Últimas ventas
- Acciones rápidas

### Ventas
- Formulario dinámico de productos
- Cálculo automático de totales
- Selección de clientes
- Historial completo

### Pedidos
- Creación de pedidos
- Estados de seguimiento
- Fechas de entrega
- Observaciones

## 🔒 Seguridad

- **Autenticación**: Verificación de credenciales
- **Sanitización**: Limpieza de datos de entrada
- **Sesiones**: Control de sesiones seguras
- **Validación**: Validación de formularios
- **Headers**: Headers de seguridad configurados

## 📊 Características Técnicas

### Base de Datos
- **Tablas principales**: vendedores, clientes, productos, ventas, pedidos
- **Relaciones**: Claves foráneas configuradas
- **Índices**: Optimización de consultas
- **Datos de prueba**: Incluidos automáticamente

### Frontend
- **Responsive**: Adaptable a todos los dispositivos
- **Moderno**: Diseño actual con Bootstrap5- **Interactivo**: JavaScript para funcionalidades dinámicas
- **Accesible**: Navegación intuitiva

### Backend
- **PHP**: Código limpio y organizado
- **PDO**: Conexiones seguras a base de datos
- **Sesiones**: Manejo seguro de sesiones
- **Validación**: Validación de datos completa

## 🚀 Uso del Sistema

### 1 Login
- Acceder a `http://localhost/vendedor-php/`
- Usar credenciales: juan@vendedor.com / password123

### 2hboard
- Ver estadísticas de ventas
- Acceder a módulos principales
- Ver últimas actividades

### 3. Nueva Venta
- Ir aVentas → "Nueva Venta"
- Seleccionar cliente
- Agregar productos
- Confirmar venta

###4 Nuevo Pedido
- Ir a Pedidos" → "Nuevo Pedido"
- Seleccionar cliente y fecha de entrega
- Agregar productos
- Guardar pedido

## 🔧 Mantenimiento

### Logs
- Los logs se guardan en `logs/`
- Revisar logs para debugging

### Backups
- Hacer backup regular de la base de datos
- Backup de archivos de configuración

### Actualizaciones
- Mantener PHP actualizado
- Revisar seguridad regularmente

## 📞 Soporte

Para soporte técnico:
- Revisar logs en `logs/`
- Verificar configuración de base de datos
- Comprobar permisos de archivos

## 📄 Licencia

Este proyecto está bajo la Licencia MIT.

---

**Desarrollado para gestión eficiente de ventas y pedidos** 