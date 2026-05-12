# Changelog

## 0.3.2 - 2026-05-12
- Validación backend de sucursal contra listado permitido en settings.
- Validación mínima de WhatsApp chileno y normalización de guardado a formato `+569XXXXXXXX`.
- Generación de ticket ajustada a fecha local de WordPress (`current_time('Ymd')`).

## 0.3.1 - 2026-05-12
- Corrección del flujo de confirmación para impedir navegación visual a éxito sin envío real.
- Pantalla final solo visible con ticket real generado desde guardado exitoso.
- Autopoblado de tipo de consulta desde selección inicial, manteniendo edición manual.
- Campo sucursal cambiado a selector basado en sucursales configuradas en settings.

## 0.3.0 - 2026-05-12
- Implementación de Etapa 3: formulario frontend funcional con envío real de atenciones.
- Validación backend con nonce, sanitización de campos y validaciones obligatorias.
- Persistencia en `agp_totem_leads` mediante repositorio centralizado.
- Generación y almacenamiento de ticket único legible (`AGP-YYYYMMDD-XXXX`).
- Pantalla final conectada a ticket real y manejo seguro de errores de validación/guardado.
- Normalización de distribución: usar carpeta raíz estable `totem-agrocampo` y resguardo defensivo para evitar redeclare de `agp_totem_init()`.

## 0.2.1 - 2026-05-12
- Ajuste menor de Etapa 2 en frontend standalone.
- Limpieza explícita del estado temporal al usar Inicio/Reiniciar atención.
- Timeout: ocultar aviso de inactividad inmediatamente al detectar actividad y reiniciar contador.
- Mensaje admin al cambiar ruta frontend manteniendo flush controlado.

## 0.2.0 - 2026-05-12
- Implementación de Etapa 2: frontend standalone inicial tipo kiosk.
- Router con rewrite rule para ruta configurable y render de layout propio del plugin.
- Carga de assets kiosk solo en la ruta del tótem.
- Flujo visual por pasos (inicio, categoría, destacados, formulario placeholder, confirmación placeholder, final placeholder).
- Navegación con botones Inicio/Volver, indicador de progreso y timeout de inactividad con reinicio automático.
- Sin persistencia de datos ni envío de correos en esta etapa.

## 0.1.1 - 2026-05-12
- Endurecimiento técnico de Etapa 1 sin ampliar alcance funcional.
- Ajuste de guardado de configuración para usar `wp_unslash()` antes de sanitizar.
- Verificación explícita de permisos `manage_options` en todas las pantallas admin.
- Contadores del resumen con helper seguro y validación de existencia de tabla.
- Documentación en `uninstall.php` sobre conservación de tablas.

## 0.1.0 - 2026-05-12
- Base técnica inicial del plugin.
- Bootstrap principal, activación y constantes.
- Creación de tablas `agp_totem_leads`, `agp_totem_products`, `agp_totem_categories` con `dbDelta`.
- Menú admin base y submenús iniciales.
- Pantallas base (resumen, placeholders y configuración funcional).
- Clase de settings con defaults y sanitización.
