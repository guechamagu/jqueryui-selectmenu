# YPFB Ductos Map - Plugin de WordPress

Plugin para visualizar ductos en un mapa interactivo usando Leaflet y servicios web REST de YPFB Transporte S.A.

## Descripción

Este plugin permite a los usuarios seleccionar un punto en un mapa interactivo y consultar si existen ductos cercanos mediante la integración con tres servicios web REST proporcionados por YPFB Transporte S.A.

## Características

- **Mapa Interactivo**: Utiliza la librería Leaflet.js para mostrar un mapa donde los usuarios pueden hacer clic para seleccionar un punto.
- **Consulta de Ductos**: Se conecta a servicios web REST para obtener información de ductos cercanos al punto seleccionado.
- **Visualización de Ductos**: Muestra los ductos encontrados en el mapa con polilíneas que representan su trayecto.
- **Mensajes Configurables**: Dos mensajes personalizables para cuando se encuentran ductos o no.
- **Panel de Administración**: Interfaz para configurar URLs de servicios web, credenciales, parámetros y mensajes.
- **Shortcode**: Fácil inserción en cualquier página o entrada de WordPress.

## Requerimientos

- WordPress 5.0 o superior (recomendado última versión)
- PHP 7.4 o superior
- Conexión HTTPS a los servicios web de YPFB Transporte S.A.
- Librería jQuery (incluida en WordPress)

## Instalación

1. Descargue el plugin y extraiga el contenido en la carpeta `/wp-content/plugins/ypfb-ductos-map/`
2. Active el plugin desde el menú "Plugins" en el administrador de WordPress
3. Vaya a "YPFB Ductos Map" en el menú de configuración
4. Configure las URLs de los servicios web y las credenciales
5. Use el shortcode `[ypfb_ductos_map]` en cualquier página o entrada

## Configuración

### Servicios Web

Configure las siguientes URLs proporcionadas por YPFB Transporte S.A.:

1. **URL Servicio Autenticación**: Endpoint para obtener el token de autenticación
2. **URL Servicio Geolocalización**: Endpoint para consultar ductos por coordenadas
3. **URL Servicio Vértices**: Endpoint para obtener los vértices de un ducto específico

### Credenciales

- **Usuario**: Nombre de usuario para autenticación en los servicios web
- **Contraseña**: Contraseña para autenticación en los servicios web

### Parámetros de Consulta

- **Radio de Búsqueda**: Distancia en metros para buscar ductos cercanos (default: 5000m)
- **Referencia Espacial**: Sistema de coordenadas usado por los servicios (ej: EPSG:3857)

### Mensajes y Contacto

- **Email de Contacto**: Dirección de correo para que los usuarios se contacten
- **Mensaje con Ductos**: Plantilla del mensaje cuando se encuentran ductos
  - Use `{DUCTOS}` para insertar la lista de ductos
  - Use `{EMAIL}` para insertar el email de contacto
- **Mensaje sin Ductos**: Plantilla del mensaje cuando no se encuentran ductos
  - Use `{EMAIL}` para insertar el email de contacto

## Uso

### Shortcode

Inserte el siguiente shortcode en cualquier página o entrada de WordPress:

```
[ypfb_ductos_map]
```

### Funcionamiento

1. El usuario ve el mapa interactivo
2. Hace clic en el punto deseado
3. El sistema consulta los servicios web para encontrar ductos cercanos
4. Se muestra un mensaje con la información de los ductos encontrados (o no)
5. Los ductos se dibujan en el mapa como polilíneas rojas
6. El usuario puede hacer clic en cada ducto de la lista para verlo en detalle

## Estructura de Archivos

```
ypfb-ductos-map/
├── ypfb-ductos-map.php      # Archivo principal del plugin
├── assets/
│   ├── css/
│   │   └── style.css        # Estilos personalizados
│   └── js/
│       └── map.js           # Lógica del mapa e interacciones
└── readme.txt               # Este archivo
```

## Servicios Web

El plugin se integra con tres servicios web REST:

### 1. Servicio de Autenticación

**Método**: POST  
**Parámetros**: username, password  
**Respuesta**: JSON con token de acceso

### 2. Servicio de Geolocalización

**Método**: GET  
**Parámetros**: 
- x: Coordenada X (proyectada)
- y: Coordenada Y (proyectada)
- spatialRef: Referencia espacial
- token: Token de autenticación
- distance: Radio de búsqueda en metros

**Respuesta Ejemplo**:
```json
{
  "features": [
    {
      "attributes": {
        "OBJECTID": 4176,
        "StationSeriesName": "DOCH",
        "LineDescription": "Derivada Oleoducto Refineria Cbba - Huayñacota",
        "OperationalStatus": "Active"
      }
    }
  ]
}
```

### 3. Servicio de Vértices

**Método**: GET  
**Parámetros**:
- codigoDucto: Código o ID del ducto
- token: Token de autenticación

**Respuesta Ejemplo**:
```json
{
  "feature": {
    "attributes": {
      "OBJECTID": 4579,
      "StationSeriesName": "DGEK",
      "LineDescription": "Derivada Gasoducto GSP - ENDE Karachipampa"
    },
    "geometry": {
      "paths": [
        [
          [213763.54, 7835990.22],
          [213777.53, 7835970.37],
          ...
        ]
      ]
    }
  }
}
```

## Notas Importantes

1. **Sistema de Coordenadas**: El plugin incluye funciones de conversión de coordenadas genéricas (Web Mercator). Debe ajustar las funciones `convertToProjectedCoords()` y `convertFromProjectedCoords()` en `assets/js/map.js` según el sistema de coordenadas específico que use YPFB Transporte S.A.

2. **Seguridad**: Todas las llamadas a los servicios web se realizan desde el servidor (PHP) para proteger las credenciales.

3. **HTTPS**: Los servicios web deben estar configurados con HTTPS válido.

4. **Token de Autenticación**: El token se obtiene en cada consulta. Para mejor rendimiento, considere implementar caché del token.

## Personalización

### Modificar el Sistema de Coordenadas

Edite el archivo `assets/js/map.js` y ajuste las funciones:

```javascript
function convertToProjectedCoords(lat, lng) {
    // Implementar conversión según el sistema de coordenadas requerido
}

function convertFromProjectedCoords(x, y) {
    // Implementar conversión inversa según el sistema de coordenadas requerido
}
```

### Cambiar Colores del Mapa

Edite el archivo `assets/css/style.css` para modificar estilos visuales.

### Modificar Mensajes por Defecto

Los mensajes pueden editarse desde el panel de administración del plugin.

## Soporte

Para problemas o consultas, contacte a:
- Email: soporte@ypfbtransporte.com
- Documentación adicional: https://ypfbtransporte.com/docs

## Licencia

GPL v2 or later

## Autor

YPFB Transporte S.A.

## Changelog

### 1.0.0
- Versión inicial del plugin
- Integración con Leaflet.js
- Conexión a servicios web REST
- Panel de administración configurable
- Shortcode para inserción en páginas
