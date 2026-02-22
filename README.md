# Sistema CRUD en PHP + MySQL

## 📋 Descripción
Sistema web completo para la gestión de usuarios con operaciones CRUD (Create, Read, Update, Delete), desarrollado en PHP puro con MySQL, utilizando buenas prácticas de seguridad y diseño responsive.

## 🚀 Características
- ✅ Crear nuevos usuarios
- ✅ Listar usuarios en tabla dinámica
- ✅ Editar información existente
- ✅ Eliminar registros
- ✅ Validación de formularios
- ✅ Diseño responsive con Bootstrap 5
- ✅ Sentencias preparadas para seguridad

## 🛠️ Tecnologías
- **PHP 7.4+**
- **MySQL 5.7+**
- **Bootstrap 5 (Tema Superhero)**
- **PDO para conexión a BD**
- **Control de versiones:** Git

## 📁 Estructura del Proyecto
```bash
Proyecto_php/
│
├── 📂 app/
│   ├── 📂 controllers/
│   │   └── UsuarioController.php    # Lógica de operaciones CRUD
│   ├── 📂 models/
│   │   └── Usuario.php              # Modelo de base de datos
│   └── 📂 views/
│       ├── index.php                # Formulario (agregar/editar)
│       └── listar.php               # Listado de usuarios
│
├── 📂 config/
│   └── database.php                 # Configuración de conexión
│
├── 📂 public/
│   ├── 📂 css/
│   │   └── style.css                # Estilos personalizados
│   └── index.php                    # Punto de entrada principal
│
├── 📂 sql/
│   └── database.sql                 # Script SQL
│
├── .gitignore                       # Archivos a ignorar en Git
└── README.md                        # Esta documentación
```

### 🏗️ Arquitectura MVC
- **Models**: Capa de datos (`Usuario.php`)
- **Controllers**: Lógica de negocio (`UsuarioController.php`)
- **Views**: Interfaz de usuario (`index.php`, `listar.php`)
## ⚙️ Instalación y Configuración

### Requisitos Previos
- Servidor web local (XAMPP, WAMP, MAMP o similar)
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Navegador web moderno
