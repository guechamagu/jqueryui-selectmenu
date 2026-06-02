# Mi Addon para Elementor Free

Un addon gratuito, portable y legal para extender las funcionalidades de Elementor Free sin infringir derechos de autor ni restricciones.

## Características

- ✅ **100% Legal**: No copia código propietario de Elementor Pro
- ✅ **Portable**: Funciona en cualquier instalación de WordPress con Elementor
- ✅ **Compatible**: Se actualiza junto con Elementor sin romperse
- ✅ **Extensible**: Fácil de añadir nuevos widgets personalizados
- ✅ **Ligero**: Solo carga lo necesario
- ✅ **Sin dependencias externas**: Solo requiere Elementor Free

## Requisitos

- WordPress 5.0 o superior
- PHP 7.4 o superior
- Elementor (versión gratuita) instalado y activado

## Instalación

1. Descarga el plugin
2. Sube la carpeta `mi-elementor-addon` a `/wp-content/plugins/`
3. Activa el plugin desde el menú "Plugins" en WordPress
4. ¡Listo! Tus nuevos widgets estarán disponibles en Elementor

## Widgets Incluidos

### Tarjeta Avanzada (Advanced Card)

Un widget flexible para crear tarjetas de contenido con:
- Imagen destacada
- Título personalizable
- Descripción
- Botón de llamada a la acción
- Icono opcional
- Múltiples opciones de estilo
- Animaciones hover
- Totalmente responsive

## Uso

1. Abre una página con Elementor
2. Busca "Tarjeta Avanzada" en el panel de widgets
3. Arrastra el widget a tu página
4. Personaliza el contenido y estilo desde el panel izquierdo

## Estructura del Plugin

```
mi-elementor-addon/
├── mi-elementor-addon.php    # Archivo principal del plugin
├── widgets/                   # Widgets personalizados
│   └── advanced-card.php     # Widget de tarjeta avanzada
└── assets/                    # Recursos estáticos
    ├── css/
    │   └── style.css         # Estilos frontend
    └── js/
        └── script.js         # Scripts frontend
```

## Añadir Nuevos Widgets

Para agregar más widgets:

1. Crea un nuevo archivo PHP en la carpeta `widgets/`
2. Extiende la clase `Elementor\Widget_Base`
3. Registra el widget en el archivo principal

Ejemplo:
```php
// En mi-elementor-addon.php
function mea_register_widgets( $widgets_manager ) {
	require_once( MEA_PATH . 'widgets/advanced-card.php' );
	require_once( MEA_PATH . 'widgets/nuevo-widget.php' ); // Nuevo widget
	
	$widgets_manager->register( new \Mi_Elementor_Addon\Widgets\Advanced_Card_Widget() );
	$widgets_manager->register( new \Mi_Elementor_Addon\Widgets\Nuevo_Widget() );
}
```

## Compatibilidad

Este plugin está diseñado para:
- ✅ Ser compatible con futuras versiones de Elementor
- ✅ No usar código privado o protegido de Elementor Pro
- ✅ Usar solo APIs públicas y documentadas de Elementor
- ✅ Seguir las mejores prácticas de desarrollo de WordPress

## Licencia

GPL v2 o posterior - Este plugin es software libre y puede ser modificado y distribuido libremente.

## Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Haz un fork del repositorio
2. Crea una rama para tu característica
3. Envía un pull request

## Soporte

Si encuentras algún problema o tienes sugerencias:
- Abre un issue en el repositorio
- Revisa la documentación de Elementor para desarrolladores

## Notas Importantes

⚠️ **Este plugin NO es una versión gratuita de Elementor Pro**

Este addon:
- No duplica funcionalidades protegidas por derechos de autor
- No infringe la licencia de Elementor Pro
- Es completamente legal y ético
- Usa únicamente APIs públicas de Elementor

La filosofía es crear componentes originales que complementen Elementor Free, no reemplazar Elementor Pro.

## Changelog

### Versión 1.0.0
- Lanzamiento inicial
- Widget de Tarjeta Avanzada
- Estilos base y scripts
- Soporte para animaciones hover
- Compatible con Elementor 3.x+
