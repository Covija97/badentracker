![](https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_bannerD.png#gh-dark-mode-only)
![](https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_bannerL.png#gh-light-mode-only)

---

Author: `Javier Coronel Vides`

Course: `2º ASIR`

Subject: `Trabajo fin de Grado Superior`

---

# 🧭 BadenTracker

**BadenTracker** es una plataforma web desarrollada para facilitar la planificación, organización y gestión de reuniones dentro de un grupo scout. Su objetivo principal es brindar una herramienta intuitiva y funcional para monitores, jefes de unidad o responsables de rama, ayudando a estructurar y visualizar el trabajo pedagógico a lo largo del ciclo scout.

## 🗺️ Roadmap<a href="/.info/roadmap.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>

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

- **Backend:** Django (Python)
- **Frontend:** HTML, CSS, JavaScript (FullCalendar para el calendario)
- **Base de datos:** SQLite
- **PDF Generator:** WeasyPrint / xhtml2pdf ???

## 📚 Estructura del proyecto

```
baden_tracker/
├── _activities/    # Gestión de actividades pedagógicas
├── _meetings/      # Planificaciones y generación de PDFs
├── _calendar/      # Reuniones y calendario visual
├── templates/      # Plantillas HTML para views y PDFs
├── static/         # Archivos estáticos (CSS, JS)
├── db.sqlite3      # Base de datos SQLite
└── baden_tracker/  # Configuración general de Django
```

## 📌 Próximas mejoras

- Soporte multiusuario por unidades o ramas.
- Exportación a Google Calendar.
- Búsqueda avanzada con múltiples filtros.
- Modo offline (PWA).
- Interfaz responsive para móviles.

## 🧑‍💻 Contribución

Este proyecto está en desarrollo. Si tienes ideas, sugerencias o quieres colaborar, ¡eres más que bienvenido! Puedes abrir un issue o hacer un fork para aportar.
