# Iteración 1 — HU-01: catálogo de artistas

## Alcance

Implementar únicamente el registro y la consulta de artistas para el catálogo. No incluye obras, tipos de autoría aplicados a obras, ubicaciones, movimientos, exposiciones, reservas, ventas ni roles completos.

## Decisiones de dominio utilizadas

- Todo artista real/identificable requiere `nombre`, `apellido` y `nacionalidad`.
- El `estado` es obligatorio y permite activar o inactivar el registro sin eliminarlo físicamente.
- `fecha_nacimiento`, `fecha_fallecimiento` y `biografia` son opcionales.
- El registro especial `AUTOR DESCONOCIDO` representa una autoría históricamente no identificada; no representa a una persona real.
- La autoría `CONFIRMADA` o `ATRIBUIDA` pertenece a una obra y queda fuera de HU-01.
- No se agregan campos adicionales ni se implementa lógica de obras.

## Criterios técnicos mínimos

1. Inicializar Laravel 13 sobre PHP 8.4, compatible con MySQL, sin componentes ajenos al MVP.
2. Incorporar una única representación persistente de artista, justificada por HU-01.
3. Permitir registrar un artista con nombre, apellido, nacionalidad y estado; admitir las tres propiedades opcionales aprobadas.
4. Permitir consultar el catálogo de artistas.
5. Garantizar la disponibilidad única del registro especial `AUTOR DESCONOCIDO` como registro de sistema, sin hacerlo pasar por persona identificada.
6. Mantener pruebas automatizadas de las operaciones de HU-01.
7. No incorporar lógica de obras ni el atributo de autoría a la entidad de artista.

## Pruebas requeridas

| Nivel | Caso |
|---|---|
| Feature | Registrar un artista válido y consultarlo en el catálogo. |
| Feature | Rechazar la ausencia de nombre, apellido, nacionalidad o estado. |
| Feature | Aceptar fecha de nacimiento, fecha de fallecimiento y biografía como opcionales. |
| Feature/integración | Crear o asegurar el registro especial `AUTOR DESCONOCIDO` y evitar duplicados. |
| Unitario | Regla de identificación del artista especial, solo si queda aislada fuera de la capa HTTP. |

## Trazabilidad

| Requisito | Historia | Regla | Implementación prevista | Prueba |
|---|---|---|---|---|
| Gestión de artistas: registrar y consultar | HU-01 | Artista real requiere nombre, apellido, nacionalidad y estado | Recurso persistente de artista, interfaz de registro/consulta | Feature: alta y consulta; validación de obligatorios |
| Estado activo/inactivo | HU-01 | No hay eliminación física para activar/inactivar | Campo de estado del artista | Feature: persistencia y consulta del estado |
| Autor históricamente desconocido | HU-01 | `AUTOR DESCONOCIDO` es registro especial de sistema y no una persona | Inicialización idempotente del registro especial | Feature/integración: existe una única vez |
| Catálogo mantenible | HU-01 | No crear lógica de obras antes de HU-02 | Solo componentes de artista y pruebas asociadas | Revisión de alcance + suite |

## Verificación de entorno

La versión seleccionada es Laravel 13 con PHP 8.4; Laravel 13 admite PHP 8.3 a 8.5. El 26 de agosto de 2026 se intentó instalar PHP 8.4 y Composer mediante los canales oficiales autorizados, pero el entorno no pudo establecer TLS con los servidores de descarga y no dispone de Winget, Chocolatey ni Scoop. Por ello, Laravel, Composer y las pruebas aún no se han podido inicializar ni ejecutar.
