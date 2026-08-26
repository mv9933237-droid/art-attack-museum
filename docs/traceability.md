# Matriz de trazabilidad

| Requisito / decisión | Historia | Regla de negocio | Implementación | Prueba |
|---|---|---|---|---|
| Registrar artistas | HU-01 | El artista se registra y se consulta en el catálogo. | Recurso persistente de artista y vistas/rutas mínimas. | Feature: alta y consulta. |
| Artista especial para autor desconocido | HU-01 | Una obra futura no carece de autor; se asocia al registro especial `AUTOR DESCONOCIDO` cuando corresponda. | Inicialización idempotente del registro especial. | Feature/integración: existe una única vez. |
| Autoría confirmada/atribuida | Fuera de HU-01 | La autoría describe la relación obra-artista. | No implementar en HU-01. | No aplica hasta HU-02. |
| Sin componentes no justificados | HU-01 | No se anticipan obras, ventas, reservas, movimientos, exposiciones ni roles completos. | Revisión de alcance del cambio. | Revisión de PR y listado de archivos. |
