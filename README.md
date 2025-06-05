![](https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_bannerD.png#gh-dark-mode-only)
![](https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_bannerL.png#gh-light-mode-only)

---

Author: `Javier Coronel Vides`

Course: `2º ASIR`

Subject: `Trabajo fin de Grado Superior`

---

# 🧭 BadenTracker

**BadenTracker** es una plataforma web desarrollada para facilitar la planificación, organización y gestión de reuniones dentro de un grupo scout. Su objetivo principal es brindar una herramienta intuitiva y funcional para monitores, jefes de unidad o responsables de rama, ayudando a estructurar y visualizar el trabajo pedagógico a lo largo del ciclo scout.

![alt text](.md/img/1.png)

## 🗺️ Roadmap<a href="/.md/readme.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>

## 🚀 Características principales

- 📄 **Creación y descarga de programaciones en PDF**  
  Genera planificaciones de reuniones con un formato predefinido que incluye encabezado, actividades ordenadas, objetivos pedagógicos y tiempos estimados.
  
- 🗂️ **Base de datos de actividades**  
  Almacena, clasifica y filtra actividades según criterios como tipo (juego, dinámica, reflexión...) y objetivos pedagógicos (trabajo en equipo, liderazgo, etc.).

- 📆 **Calendario de reuniones**  
  Visualiza y organiza las reuniones en un calendario interactivo. Accede rápidamente a las programaciones asociadas a cada fecha.

- 👥 **Enfoque pedagógico personalizado**  
  Las actividades están orientadas a cumplir con los objetivos formativos del escultismo, adaptándose a distintas edades y ramas.

## 🛠️ Tecnologías utilizadas

- **Backend:** PHP 8.x  
- **Frontend:** HTML, CSS, JavaScript  
- **Base de datos:** MySQL  
- **Servidor web:** Apache / Xampp  
- **Generador de PDF:** Integrado con FPDF  
- **Integración de calendario:** FullCalendar integrado

## 📚 Estructura del proyecto

La estructura del proyecto se divide en 3 grandes módulos:

* Actividades: Gestiona las actividades, objetivos, categorías y materiales almacenados en la base de datos.
* Calendario: Muestra un calendario interactivo con las distintas programaciones diarias, pudiendo filtrar por rama para una gestión más cómoda.
* Reuniones: Permite crear reuniones, salidas, etc., pudiendo usar la base de datos de actividades o actividades personalizadas.

```
badentracker/
├── index.php               # Página de inicio
├── .res/                   # Recursos comunes
│   ├── css/                # Archivos CSS
│   ├── db/                 # Consultas y creación de tablas
│   ├── funct/              # Funciones PHP generales
│   ├── icon/               # Iconos SVG
│   ├── img/                # Imágenes
│   ├── js/                 # Archivos JavaScript
│   └── templates/          # Plantillas HTML
│
├── actividades/            # Página de actividades
│   │
│   ├── index.php
│   │
│   ├── new/                # Creación de actividades
│   │   └── index.php
│   │
│   ├── actividad/          # Edición de actividad por ID
│   │   ├── index.php
│   │   └── delete.php
│   │
│   ├── categorias/         # Página de categorías de actividades
│   │   ├── index.php
│   │   │── categoria/      # Edición de categoría por ID
│   │   │   ├── index.php
│   │   │   └── delete.php
│   │   └── new/            # Creación de categoría
│   │       └── index.php
│   │
│   ├── materiales/         # Página de materiales de actividades
│   │   ├── index.php
│   │   │── material/       # Edición de material por ID
│   │   │   ├── index.php
│   │   │   └── delete.php
│   │   └── new/            # Creación de material
│   │       └── index.php
│   │
│   └── objetivos/          # Página de objetivos
│       ├── index.php
│       │── objetivo/       # Edición de objetivo por ID
│       │   ├── index.php
│       │   └── delete.php
│       └── new/            # Creación de objetivo
│           └── index.php
│
├── calendario/             # Página de calendario
│
└── reuniones/              # Página de reuniones
    ├── index.php           # Listado de reuniones
    ├── new/               # Creación de reunión
    │     └── index.php
    ├── grupos/             # Gestión de grupos de reuniones
    │     ├── index.php     # Listado de grupos
    │     ├── new/          # Creación de grupo
    │     │     └── index.php
    │     └── grupo/        # Detalle y edición de grupo por ID
    │            ├── index.php
    │            └── delete.php
    └── reunion/            # Detalle, edición y exportación de reunión por ID
          ├── index.php
          ├── delete.php
          └── pdfExport.php   # Exportador a PDF
```

## 📌 Próximas mejoras

- Soporte y login por multiusuario por unidades o ramas.
- Seguimiento de progreso personal de cada educando.
- Exportación a Google Calendar.
- Búsqueda avanzada con múltiples filtros.
- Interfaz responsive para móviles.
- Actividades con duración variable.

## 🧑‍💻 Contribución

Este proyecto está en desarrollo. Si tienes ideas, sugerencias o quieres colaborar, ¡eres más que bienvenido! Puedes abrir un issue o hacer un fork para aportar.
