![](https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_bannerD.png#gh-dark-mode-only)
![](https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_bannerL.png#gh-light-mode-only)

<a href="/README.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_home.svg" height="30"></a>

# 🗺️ Roadmap (40h) – Marzo y Mayo 2025

## 🖥 Creación y configuración del servidor<a href="0.srv.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
*(Configurado e implementado en AWS y Ubuntu con Apache, PHP y MySQL)*

## 🛠️ Planificación y estructura básica del proyecto (4h)<a href="1.plan.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Crear estructura base con PHP (carpetas, conexión DB)

```mermaid
gantt
    title Roadmap (40h)
    dateFormat  DDD
    axisFormat %j

    section Planing<br>(4h)
    Definir modelos SQL         :done, 01-01, 2d
    Estructura base en PHP      :done, 01-03, 2d
```

## 🗄️ Diseño de la base de datos (5h)<a href="2.db.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Crear esquema SQL (ERD) actualizado  
- Crear tablas mediante migraciones/manual  
- Configurar relaciones (FKs, índices)

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section Base de Datos<br>(5h)
    Diseño de esquema SQL       :done, 01-06, 2d
    Crear tablas                :done, 01-08, 2d
    Configurar relaciones       :done, 01-10, 1d
```

## 🎯 Modelo de Actividades (4h)<a href="3.act.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Modelos actualizados para `Actividad`, `Objetivos`, `Categorias` y `Materiales`  
- Formularios implementados y testeados  
- Listado y filtrado operativos

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section Actividades<br>(4h)
    Crear modelos               :done, 01-11, 2d
    Formularios                 :done, 01-13, 1d
    Listado y filtros           :done, 01-14, 1d
```

## 📅 Modelo de Reuniones (4h)<a href="4.reu.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Modelo `Programacion` y `ActividadProgramada` implementado  
- Formularios y listados en funcionamiento

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j
   
    section Reuniones<br>(4h)
    Crear modelos               :active, 01-15, 2d
    Formularios                 : 01-17, 1d
    Listado y filtros           : 01-18, 1d
```

## 🖨️ Generador de PDF (5h)<a href="5.pdf.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Plantilla HTML para PDF  
- Integración de librería (`FPDF` o `Dompdf`)  
- Botón de descarga en vista de programación

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section PDF<br>(5h)
    Plantilla HTML              :done, 01-19, 2d
    Integrar FPDF               :done, 01-21, 2d
    Testeo                      :done, 01-23, 1d
```

## 📆 Sistema de calendario (5h)<a href="6.cld.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Tabla `reuniones` actualizada  
- API de eventos en PHP (formato JSON)  
- Integración de `FullCalendar` en el frontend

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j
   
    section Calendario<br>(5h)
    Unir con reuniones       :done, 01-24, 1d
    API de eventos (PHP)        :done, 01-25, 2d
    FullCalendar integración    :done, 01-27, 2d
```

## ✨ Mejoras (4h)<a href="7.imp.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Implementar filtros múltiples en consultas SQL  
- Refinar diseño (CSS limpio)  
- Validaciones adicionales en JS y PHP

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section Mejoras<br>(4h)
    Filtros avanzados           :done, 01-29, 1d
    Interfaz limpia             :done, 01-30, 1d
    Validaciones JS/PHP         :done, 01-31, 1d
```

## 🧪 Tests y ajustes finales (4h)<a href="8.test.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Pruebas funcionales realizadas  
- Correcciones menores aplicadas  
- Ajustes en interfaz y lógica finalizados

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section Testeo<br>(4h)
    Pruebas funcionales         :done, 02-01, 1d
    Corrección de errores       :done, 02-02, 1d
    Ajustes menores             :done, 02-03, 1d
```

## 📚 Documentación (5h)<a href="9.doc.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- README con instrucciones de instalación actualizadas  
- Documentación técnica y de uso completada

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j
    section Documentación<br>(5h)
    README completo             :done, 02-04, 3d
    Guía técnica/uso            :done, 02-07, 2d
```
