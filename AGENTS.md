# AGENTS.md — Convenciones del proyecto Art Attack Museum

## Contexto del proyecto

Sistema web para gestión de galería de arte y museo: artistas, obras, ubicaciones, exposiciones, clientes, ventas y auditoría.

## Stack aprobado

- Laravel 13
- PHP 8.4
- MySQL
- Monolito modular

## Rama de trabajo

`iteration-1/hu-01-artists`

## Reglas generales

1. NO reiniciar el proyecto ni cambiar decisiones funcionales ya aprobadas.
2. NO implementar funcionalidades fuera de la historia actual autorizada.
3. NO inventar reglas de negocio no documentadas.
4. NO borrar documentación existente.
5. Antes de modificar código o configuración, inspeccionar el repositorio y confirmar el estado actual.
6. Si una decisión está marcada como pendiente, no convertirla en regla por cuenta propia.
7. Mantener trazabilidad: cada entidad, tabla, endpoint o servicio debe justificarse mediante Requisito → Historia → Regla de negocio → Implementación → Prueba.
8. Actualizar `docs/traceability.md` cuando corresponda.

## Historias autorizadas

| Historia | Estado |
|---|---|
| HU-01 — Registrar y consultar artistas | Autorizada (actual) |
| HU-02+ | NO implementar hasta autorización explícita |

## Restricciones de seguridad

- Nunca desactivar TLS ni validación SSL.
- Nunca descargar ejecutables de fuentes desconocidas.
- Nunca introducir credenciales en el repositorio.
- Nunca crear `.env` con secretos reales.
- Nunca subir contraseñas.
- Nunca borrar historial Git.
- Nunca hacer force push sin autorización explícita.

## Convenciones de código

- Seguir la estructura estándar de Laravel.
- Usar naming conventions de Laravel (PascalCase para clases, snake_case para columnas).
- No agregar comentarios innecesarios.
- No agregar dependencias no justificadas.

## Documentación del proyecto

| Archivo | Propósito |
|---|---|
| `README.md` | Descripción general |
| `docs/iterations/iteration-1-hu-01-artists.md` | Definición completa de HU-01 |
| `docs/traceability.md` | Matriz de trazabilidad |
| `AGENTS.md` | Este archivo — convenciones del agente |

## Antes de cada cambio significativo

1. Explicar qué se encontró.
2. Identificar dependencias.
3. Indicar qué se va a modificar.
4. Realizar el cambio.
5. Verificar el resultado.
6. Informar exactamente qué cambió.
