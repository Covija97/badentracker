![](https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_bannerD.png#gh-dark-mode-only)
![](https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_bannerL.png#gh-light-mode-only)

<a href="/README.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/9cf65965f880c39d5e634d73522a6d656c4ea501/_home.svg" height="30"></a>

# 🗺️ Roadmap (40h) – Marzo y Mayo 2025

## 🛠️ Planificación y estructura básica del proyecto (4h)<a href="1.planing.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Definir modelos en Django
- Crear proyecto y apps básicas

```mermaid
gantt
    title Roadmap (40h)
    dateFormat  DDD
    axisFormat %j

    section Planing<br>(5h)
    Definir modelos             :done, 01-01, 2d
    Proyecto y apps básicas     :done, 01-03, 3d
```

## 🗄️ Diseño de la base de datos (5h)<a href="2.database.md"><img src="https://raw.githubusercontent.com/jcorvid509/.resGen/dbf0397a38c3e0828d9bd164f719d77f3d977cda/_arrow.svg" height="30" align="right"></a>
- Definir esquemas de datos
- Crear modelos de datos en Django
- Configurar relaciones entre modelos

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section Database<br>(5h)
    Definir esquema             :done, 01-06, 2d
    Crear modelos               :done, 01-08, 2d
    Configurar relaciones       :active, 01-10, 1d
```

## 📝 CRUD de Actividades (4h)
- Crear modelos `Actividad` y `ObjetivoPedagogico`
- Formularios
- Listado y filtrado

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section Actividades<br>(5h)
    Crear modelos               : 01-11, 2d
    Formularios                 : 01-13, 1d
    Listado y filtrado          : 01-14, 1d
 ```

## 📅 CRUD de Reuniones (4h)
- Modelo `Programacion` + `ActividadProgramada`
- Formularios
- Listado y filtrado

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j
   
    section Reuniones<br>(5h)
    Crear modelos               : 01-15, 2d
    Formularios                 : 01-17, 1d
    Listado y filtrado          : 01-18, 1d
```

## 🖨️ Generador de PDF (5h)
- Plantilla HTML para PDF
- Integrar `WeasyPrint`
- Botón de descarga en vista de programación

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section PDF<br>(5h)
    Plantilla PDF               : 01-19, 2d
    WeasyPrint                  : 01-21, 2d
    Testeo                      : 01-23, 1d
 ```

## 📆 Sistema de calendario (5h)
- Modelo `Reunion`
- API para eventos
- Integrar `FullCalendar` en frontend

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j
   
    section Calendario<br>(5h)
    Crear modelos               : 01-24, 1d
    Eventos                     : 01-25, 2d
    FullCalendar                : 01-27, 2d
```

## ✨ Mejoras (4h)
- Filtros múltiples
- Diseño limpio de interfaces
- Validaciones

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section Mejoras<br>(3h)
    Filtros múltiples           : 01-29, 1d
    Diseño limpio               : 01-30, 1d
    Validaciones                : 01-31, 1d
```

## 🧪 Tests y ajustes finales (4h)
- Pruebas básicas
- Corrección de errores
- Pequeñas mejoras

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j

    section Testeo<br>(3h)
    Pruebas                     : 02-01, 1d
    Corrección                  : 02-02, 1d
    Pequeñas mejoras            : 02-03, 1d
```

## 📚 Documentación (5h)
- README completo
- Documentación

```mermaid
gantt
    dateFormat  DDD
    axisFormat %j
    section Documen.<br>(5h)
    Readmes                     : 02-04, 3d
    Documentación               : 02-07, 2d
```
