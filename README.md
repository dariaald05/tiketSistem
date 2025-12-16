# Sistema de Tickets

Un sistema simple de gestión de tickets con registro y login de usuarios.

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache recomendado)

## Instalación

1. **Instala XAMPP** (recomendado para Windows):
   - Descarga XAMPP desde https://www.apachefriends.org/
   - Instala XAMPP en tu sistema
   - Inicia Apache y MySQL desde el panel de control de XAMPP

2. **Configura la base de datos**:
   - Abre phpMyAdmin (http://localhost/phpmyadmin)
   - Crea una base de datos llamada `sistematickets`
   - O ejecuta el archivo `setup.php` en tu navegador (http://localhost/tiketSistem/setup.php) para crear automáticamente la base de datos y tablas.

3. **Configura la conexión**:
   - Edita `config.php` y ajusta las credenciales de MySQL si es necesario:
     ```php
     define('DB_USERNAME', 'root');
     define('DB_PASSWORD', ''); // Tu contraseña de MySQL
     ```

4. **Accede al sistema**:
   - Abre tu navegador y ve a http://localhost/tiketSistem/
   - Regístrate en http://localhost/tiketSistem/registro.php
   - Inicia sesión en http://localhost/tiketSistem/login.php

## Archivos

- `index.php`: Página de inicio
- `registro.php`: Formulario de registro
- `login.php`: Formulario de login
- `tickets.php`: Sistema de tickets (requiere login)
- `config.php`: Configuración de base de datos
- `style.css`: Estilos CSS
- `setup.php`: Script de configuración de base de datos

## Funcionalidades

- Registro de usuarios
- Login/logout
- Creación y gestión de tickets
- Interfaz con tema morado claro

## Notas

- Asegúrate de que las carpetas `tiketSistem` estén dentro del directorio `htdocs` de XAMPP (o equivalente).
- Si usas otro servidor, ajusta las rutas correspondientes.</content>
<parameter name="filePath">c:\Users\Dariana Ruiz\Desktop\tiketSistem\README.md