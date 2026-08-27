# HU-02 — Diseño Integral: Catálogo de Obras

## 1. Alcance de HU-02

### IN SCOPE

- **Obras de arte**: registro, consulta, edición y catalogación de obras.
- **Relación artista ↔ obra**: autoría confirmada y atribuida.
- **AUTOR DESCONOCIDO**: reutilización del registro especial de HU-01.
- **Tipos de naturaleza**: original, réplica, reproducción.
- **Estado de obra**: DISPONIBLE, RESERVADA, VENDIDA, NO_DISPONIBLE.
- **Ubicación física**: registro y consulta de ubicaciones.
- **Movimientos**: registro de cambios de ubicación con trazabilidad.
- **Exposiciones físicas y virtuales**: registro y asignación de obras.
- **Reservas**: registro que bloquea ventas.
- **Clientes**: registro básico de clientes.
- **Ventas**: registro de ventas con detalles por obra.
- **Pagos físicos**: registro de pago en efectivo/transferencia.
- **Auditoría básica**: registro de operaciones críticas.

### OUT OF SCOPE (futuras HU)

- Autenticación completa y roles/permisos detallados (HU-03+)
- Reportes avanzados y estadísticas (HU-03+)
- Pagos virtuales/pasarelas (no aplica — solo pago físico)
- Comercio electrónico (no aplica)
- Python, Java, 3D, RA (fuera del MVP)
- Facturación electrónica (no aplica)
- Reservas online por clientes (no aplica en esta iteración)

## 2. Decisiones de dominio respetadas de HU-01

- Todo artista real tiene nombre, apellido, nacionalidad.
- Todo artista tiene estado activo/inactivo.
- AUTOR DESCONOCIDO es registro especial de sistema, no persona real.
- La entidad Artist NO se duplica; se reutiliza mediante relación.
- La autoría CONFIRMADA y ATRIBUIDA pertenece al contexto de las obras.
