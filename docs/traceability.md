# Matriz de trazabilidad

| Requisito / decisión | Historia | Regla de negocio | Implementación | Prueba |
|---|---|---|---|---|
| Registrar artistas | HU-01 | El artista se registra y se consulta en el catálogo. | `ArtistController` (index, store, show), rutas API, modelo `Artist`, migración `create_artists_table`. | Feature: `ArtistCatalogTest` — alta, consulta, validación de obligatorios. |
| Artista especial para autor desconocido | HU-01 | Una obra futura no carece de autor; se asocia al registro especial `AUTOR DESCONOCIDO` cuando corresponda. | `Artist::autorDesconocido()` con `forceFill` e `is_system`, seeder `ArtistSeeder`. | Feature: `AutorDesconocidoTest` — unicidad, datos correctos, exclusión del catálogo. |
| Autoría confirmada/atribuida | Fuera de HU-01 | La autoría describe la relación obra-artista. | No implementar en HU-01. | No aplica hasta HU-02. |
| Sin componentes no justificados | HU-01 | No se anticipan obras, ventas, reservas, movimientos, exposiciones ni roles completos. | Revisión de alcance del cambio. | Revisión de PR y listado de archivos. |
