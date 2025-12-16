# Backend – Sistema Abarrotes El caballero

El backend del Sistema Abarrotes el caballero es el encargado de gestionar
la lógica de negocio, la seguridad, el acceso por roles, el control de
productos, las ventas y la actualización del inventario.

Este módulo fue desarrollado en PHP utilizando PDO para el acceso seguro
a la base de datos.

## Requisitos del sistema
- PHP 8.0 o superior
- MySQL o MariaDB
- Servidor web Apache
- Extensión PDO habilitada en PHP
- Navegador web actualizado

## Configuración inicial
1. Colocar el proyecto dentro de la carpeta raíz del servidor web.
2. Importar la base de datos en MySQL/MariaDB.
3. Configurar las credenciales de conexión en:
   `backend/controllers/config.php`
4. Verificar permisos de escritura en la ruta:
   `frontend/public/imagenes/productos/`

## Autenticación y control de acceso
El sistema utiliza sesiones en PHP para manejar la autenticación.
Se manejan dos roles:
- Administrador: acceso completo a productos, ventas y restauración.
- Cajero: registro de ventas y alta de productos.

El control de acceso se valida desde el archivo `auth.php`.

## Gestión de productos
- Los productos no se eliminan físicamente.
- Se utiliza borrado lógico mediante el campo `estado`.
- Cuando el stock llega a cero, el producto se desactiva automáticamente.
- Los productos eliminados pueden ser restaurados por el administrador.

## Gestión de ventas
- Las ventas se procesan mediante transacciones para asegurar integridad.
- Métodos de pago disponibles: efectivo, tarjeta y PayPal.
- El ticket se genera utilizando variables de sesión, evitando el uso
  de parámetros visibles en la URL.

## Seguridad y buenas prácticas
- Uso de consultas preparadas para evitar inyección SQL.
- Validación de roles en cada controlador.
- Manejo de errores controlado y consistente.

## Estado del backend
El backend se encuentra completamente funcional 

##  Autores 
**Fuentes Martinez Ivan Dario** 
**Hernandez Peñaloza Jose Jesus** 
**Monjaraz Cruz Lesly Gudalupe**
 Ingeniería en Informática 
 Tecnológico de Estudios Superiores de Ixtapaluca
