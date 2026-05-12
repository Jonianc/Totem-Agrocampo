# Changelog

## 0.1.0 - 2026-05-12
- Base técnica inicial del plugin.
- Bootstrap principal, activación y constantes.
- Creación de tablas `agp_totem_leads`, `agp_totem_products`, `agp_totem_categories` con `dbDelta`.
- Menú admin base y submenús iniciales.
- Pantallas base (resumen, placeholders y configuración funcional).
- Clase de settings con defaults y sanitización.

## 0.1.1 - 2026-05-12
- Endurecimiento técnico de Etapa 1 sin ampliar alcance funcional.
- Ajuste de guardado de configuración para usar `wp_unslash()` antes de sanitizar.
- Verificación explícita de permisos `manage_options` en todas las pantallas admin.
- Contadores del resumen con helper seguro y validación de existencia de tabla.
- Documentación en `uninstall.php` sobre conservación de tablas.
