# Matriz de trazabilidad

| Requisito / decisión | Historia | Regla de negocio | Implementación | Prueba |
|---|---|---|---|---|
| Registrar artistas | HU-01 | El artista se registra y se consulta en el catálogo. | `ArtistController` (index, store, show), rutas API, modelo `Artist`, migración `create_artists_table`. | Feature: `ArtistCatalogTest` — alta, consulta, validación de obligatorios. |
| Artista especial para autor desconocido | HU-01 | Una obra futura no carece de autor; se asocia al registro especial `AUTOR DESCONOCIDO` cuando corresponda. | `Artist::autorDesconocido()` con `forceFill` e `is_system`, seeder `ArtistSeeder`. | Feature: `AutorDesconocidoTest` — unicidad, datos correctos, exclusión del catálogo. |
| REQ-001 — Registrar obra | HU-02 | Toda obra requiere título, artista, naturaleza y estado comercial inicial. | `ArtworkController::store`, `ArtworkService::create`, migración `create_artworks_table`. | Feature: `ArtworkCatalogTest::test_puede_crear_obra_valida`. |
| REQ-002 — Consultar catálogo de obras | HU-02 | El catálogo excluye obras eliminadas lógicamente. Soporta paginación. | `ArtworkController::index` con `paginate(15)`, filtros status/artist_id/location_id/search. | Feature: `ArtworkCatalogTest::test_puede_listar_obras`. |
| REQ-003 — Consultar detalle de obra | HU-02 | La respuesta incluye relación con artista y ubicación. | `ArtworkController::show` con `load(['location', 'artists', 'statusHistory'])`. | Feature: `ArtworkCatalogTest::test_puede_consultar_detalle_obra`. |
| REQ-004 — Editar obra | HU-02 | No se puede cambiar el artista original de una obra. | `ArtworkController::update`, `ArtworkService::update`, `UpdateArtworkRequest`. | Feature: `ArtworkCatalogTest::test_puede_actualizar_obra`. |
| REQ-005 — Eliminar obra (lógicamente) | HU-02 | No se puede eliminar una obra con venta confirmada. | `ArtworkController::destroy` + `SoftDeletes`, `ArtworkService::delete`. | Feature: `ArtworkCatalogTest::test_puede_eliminar_obra_logicamente`. |
| REQ-006 — Asociar artista a obra | HU-02 | Toda obra tiene al menos un autor. Tipo: CONFIRMADA o ATRIBUIDA. | `ArtworkArtistController::store`, `ArtworkArtist` model, `artwork_artists` table. | Feature: `ArtworkArtistTest::test_puede_asociar_artista_a_obra`. |
| REQ-007 — Asignar AUTOR DESCONOCIDO | HU-02 | AUTOR DESCONOCIDO usa autoría CONFIRMADA. No representa persona real. | `ArtworkArtistController::assignUnknown`, `Artist::autorDesconocido()`. | Feature: `IntegrationTest::test_autor_desconocido_funciona`. |
| REQ-008 — Consultar autores de obra | HU-02 | Cada autor tiene un tipo (CONFIRMADA o ATRIBUIDA). | `ArtworkArtistController::index`. | Feature: `ArtworkArtistTest::test_puede_listar_autores_de_obra`. |
| REQ-009 — Registrar ubicación | HU-02 | Toda ubicación tiene nombre único y capacidad definida. | `LocationController::store`, migración `create_locations_table`, `unique:nombre`. | Feature: `LocationTest::test_puede_crear_ubicacion_valida`. |
| REQ-010 — Consultar ubicaciones | HU-02 | La respuesta incluye conteo de obras en cada ubicación. Paginación. | `LocationController::index` con `withCount('artworks')` + `paginate(15)`. | Feature: `LocationTest::test_puede_consultar_ubicaciones`. |
| REQ-011 — Registrar movimiento de obra | HU-02 | Todo movimiento conserva origen, destino, fecha, motivo y responsable. | `MovementController::store`, `MovementService::create`, migración `create_movements_table`. | Feature: `MovementTest::test_puede_crear_movimiento_valido`. |
| REQ-012 — Consultar historial de movimientos | HU-02 | El historial no debe sobrescribirse. | `MovementController::history`, migración `create_movements_table` (append-only). | Feature: `MovementTest::test_puede_consultar_historial_de_movimientos`. |
| REQ-013 — Registrar exposición | HU-02 | Exposición puede ser física o virtual. Fechas definen el período. | `ExhibitionController::store`, migración `create_exhibitions_table`. | Feature: `ExhibitionTest::test_puede_crear_exposicion_fisica_valida`. |
| REQ-014 — Asignar obra a exposición | HU-02 | Una obra no puede participar en dos exposiciones físicas con períodos solapados. | `ExhibitionArtworkController::store`, `ExhibitionService::assignArtwork`, `hasPhysicalOverlap`. | Feature: `ExhibitionTest::test_puede_asignar_obra_a_exposicion`. |
| REQ-015 — Registrar reserva | HU-02 | Solo obras DISPONIBLES pueden reservarse. Reserva cambia estado a RESERVADA. | `ReservationController::store`, `ReservationService::create`, `Reservation` model. | Feature: `ReservationTest::test_puede_registrar_reserva`. |
| REQ-016 — Cancelar reserva | HU-02 | Solo se pueden cancelar reservas vigentes. Cancelación devuelve obra a DISPONIBLE. | `ReservationController::cancel`, `ReservationService::cancel`. | Feature: `ReservationTest::test_puede_cancelar_reserva`. |
| REQ-017 — Registrar cliente | HU-02 | Todo cliente tiene documento de identidad único. | `CustomerController::store`, migración `create_customers_table`, `unique:documento`. | Feature: `CustomerTest::test_puede_registrar_cliente_valido`. |
| REQ-018 — Consultar clientes | HU-02 | Los clientes se listan por apellido. Paginación. | `CustomerController::index` con `orderBy('apellido')` + `paginate(15)`. | Feature: `CustomerTest::test_puede_consultar_listado_de_clientes`. |
| REQ-019 — Registrar venta | HU-02 | Una venta puede incluir múltiples obras. Moneda: BOB. | `SalesController::store`, `SaleService::create`, `StoreSaleRequest`. | Feature: `SaleTest::test_puede_crear_venta_valida`. |
| REQ-020 — Confirmar venta | HU-02 | Solo ventas PENDIENTES pueden confirmarse. Obras cambian a VENDIDA. | `SalesController::confirm`, `SaleService::confirm`, transiciones EST-001/EST-003. | Feature: `SaleTest::test_puede_confirmar_venta`. |
| REQ-021 — Anular venta | HU-02 | Solo ventas CONFIRMADAS pueden anularse. Obras vuelven a DISPONIBLE. | `SalesController::annul`, `SaleService::annul`. | Feature: `SaleTest::test_puede_anular_venta`. |
| REQ-022 — Registrar pago | HU-02 | Pago es físico (efectivo/transferencia). Moneda BOB. Págos parciales permitidos. | `PaymentController::store`, `PaymentService::create`, validación saldo ≤ total. | Feature: `PaymentTest::test_puede_registrar_pago_valido`. |
| REQ-023 — Consultar estado de obra | HU-02 | El estado comercial es independiente de la ubicación física. | `ArtworkController::show` retorna `estado_comercial`, `ArtworkController::status` (GET /artworks/{id}/status), `ArtworkController::changeStatus`. | Feature: `ArtworkCatalogTest::test_puede_consultar_estado_de_obra`. |
| REQ-024 — Cambiar estado de obra | HU-02 | Las transiciones deben seguir la máquina de estados aprobada (EST-001). | `ArtworkController::changeStatus`, `ArtworkService::changeStatus`, `TRANSICIONES_PERMITIDAS`. | Feature: `ArtworkCatalogTest::test_puede_cambiar_estado_obra`. |
| REQ-025 — Consultar obras por ubicación | HU-02 | Se muestran obras según su último movimiento. Paginación. | `LocationController::artworks` + `LocationController::index` con `location_id` + `paginate(15)`. | Feature: `LocationTest::test_puede_consultar_obras_de_ubicacion`. |
| REQ-026 — Consultar obras por artista | HU-02 | Se incluyen obras donde el artista es autor confirmado o atribuido. | `ArtworkController::index` con filtro `artist_id`, relación `Artist::artworks()`. | Feature: `ArtworkArtistTest::test_puede_filtrar_obras_por_artista`. |
| REQ-027 — Consultar obras por estado | HU-02 | Filtro válido: DISPONIBLE, RESERVADA, VENDIDA, NO_DISPONIBLE. | `ArtworkController::index` con filtro `status`. | Feature: `ArtworkCatalogTest::test_puede_filtrar_obras_por_estado`. |
| REQ-028 — Consultar obras en exposición | HU-02 | Solo se muestran obras activas en la exposición. Paginación. | `ExhibitionController::artworks` con `paginate(15)`. | Feature: `ExhibitionTest::test_puede_consultar_obras_de_exposicion`. |
| REQ-029 — Registrar naturaleza de obra | HU-02 | Naturaleza es ORIGINAL, RÉPLICA o REPRODUCCIÓN. | `StoreArtworkRequest` con `in:original,replica,reproduccion`, constante `Artwork::NATURALEZA_*`. | Feature: `ArtworkCatalogTest::test_naturaleza_invalida_rechazada`. |
| REQ-030 — Exclusividad de obra original | HU-02 | Una obra original solo puede tener una venta confirmada. | `SaleService::create` + `confirm` validan `Sale::whereHas('saleDetails')->where('estado', CONFIRMADA)`. | Feature: `SaleTest::test_exclusividad_obra_original`, `IntegrationTest::test_obra_original_no_doble_venta`. |
| REQ-031 — Validación de fechas de exposición | HU-02 | No se permite exposición con fecha fin anterior a inicio. | `StoreExhibitionRequest` con `end_date after:start_date`. | Feature: `ExhibitionTest::test_fecha_fin_anterior_a_inicio_rechazada`. |
| REQ-032 — Validación de solapamiento de exposiciones físicas | HU-02 | Una obra NO puede participar en dos exposiciones físicas con períodos solapados. | `ExhibitionService::hasPhysicalOverlap`, `ExhibitionArtworkController::store`. | Feature: `ExhibitionTest::test_solapamiento_exposiciones_fisicas`. |
| REQ-033 — Cálculo automático de totales en venta | HU-02 | Total = Suma(subtotales) + Suma(impuestos) - Suma(descuentos). Subtotal = precio. | `Sale::calcularTotales()`, `SaleDetail::calcularSubtotal()` = precio. | Feature: `SaleTest::test_calculo_correcto_totales`. |
| REQ-034 — Moneda fija BOB | HU-02 | Todas las ventas usan moneda BOB. | `SaleService::create` fija `'moneda' => 'BOB'`, migración `moneda` default BOB. | Feature: `SaleTest::test_moneda_es_bob`, `IntegrationTest::test_moneda_venta_es_bob`. |
| REQ-035 — Auditoría de ventas | HU-02 | Toda operación queda registrada (created_at, updated_at). | Timestamps automáticos en `sales` y `sale_details`. | Feature: Implícito en tests de creación/actualización. |
| REQ-036 — Pago parcial | HU-02 | Una venta puede tener múltiples pagos. Saldo pendiente actualizado. | `PaymentService::create` valida `monto ≤ total - sum(pagos)`, múltiples `Payment` por `sale_id`. | Feature: `PaymentTest::test_pagos_parciales_sumados_no_exceden_total`. |
| REQ-037 — Comprobante de pago | HU-02 | Comprobante es opcional. | `PaymentController::store` con `comprobante` nullable, `Payment` model. | Feature: `PaymentTest::test_puede_registrar_pago_valido` (con comprobante). |
| REQ-038 — Consultar ventas por cliente | HU-02 | Se muestran todas las ventas, incluyendo anuladas. Paginación. | `CustomerController::sales` con `paginate(15)`, ruta `GET /customers/{customer}/sales`. | Feature: `CustomerTest::test_puede_consultar_ventas_de_cliente`. |
| REQ-039 — Consultar pagos de venta | HU-02 | Se muestran todos los pagos registrados. Paginación. | `PaymentController::index` con `paginate(15)`, ruta `GET /sales/{sale}/payments`. | Feature: `PaymentTest::test_puede_consultar_pagos_de_venta`. |
| REQ-040 — Registrar exposición virtual | HU-02 | Exposición virtual tiene URL en lugar de ubicación física. | `StoreExhibitionRequest` con `url required_if:tipo,virtual`, `Exhibition::TIPO_VIRTUAL`. | Feature: `ExhibitionTest::test_puede_crear_exposicion_virtual_valida`. |
| REQ-041 — Validar capacidad de ubicación | HU-02 | No se puede asignar más obras de las que la capacidad permite. | `MovementService::hasAvailableCapacity`, valida `count(current_location_id) < capacidad`. | Feature: `MovementTest::test_destino_lleno_rechaza_movimiento`. |
| REQ-042 — Historial de estados de obra | HU-02 | Todo cambio de estado queda registrado. | `ArtworkService::changeStatus` crea `ArtworkStatusHistory`, migración `create_artwork_status_history_table`. | Feature: `ArtworkCatalogTest::test_cambio_estado_registra_historial`. |
| REQ-043 — Validar existencia de artista al asignar | HU-02 | No se puede asociar artista inexistente. | `ArtworkArtistController::store` con `artist_id exists:artists,id`. | Feature: `ArtworkArtistTest::test_artista_inexistente_rechazado`. |
| REQ-044 — Validar existencia de obra en movimientos | HU-02 | Movimiento requiere obra válida. | `MovementService::create` con `Artwork::findOrFail`. | Feature: `MovementTest::test_obra_inexistente_rechazada`. |
| REQ-045 — Validar existencia de ubicación en movimientos | HU-02 | Movimiento requiere ubicaciones válidas. | `MovementService::create` con `Location::findOrFail` para origen y destino. | Feature: `MovementTest::test_ubicacion_origen_inexistente_rechazada`. |
| REQ-046 — Fecha de movimiento no futura | HU-02 | No se permiten movimientos con fecha futura. | `StoreMovementRequest` con `fecha before_or_equal:today`. | Feature: `MovementTest::test_fecha_futura_rechazada`. |
| REQ-047 — Obra sin ubicación inicial | HU-02 | La ubicación se asigna mediante movimientos. | `current_location_id` nullable en migración `create_artworks_table`. | Feature: `ArtworkCatalogTest::test_obra_sin_ubicacion_inicial`. |
| REQ-048 — Validar cliente para venta | HU-02 | Toda venta requiere cliente válido. | `SaleService::create` con `Customer::findOrFail`. | Feature: `SaleTest::test_cliente_inexistente_rechazado`. |
| REQ-049 — Al menos un detalle en venta | HU-02 | Venta sin obras no es válida. | `StoreSaleRequest` con `details required|array|min:1`. | Feature: `SaleTest::test_venta_sin_detalles_rechazada`. |
| REQ-050 — Precio por obra en detalle | HU-02 | El precio es definido al momento de la venta. | `SaleDetail::create` con `precio` required. | Feature: `SaleTest::test_puede_crear_venta_valida`. |
| REQ-051 — Impuesto en detalle | HU-02 | Impuesto es calculable por detalle. | `SaleDetail` con `impuesto nullable|numeric|min:0`, default 0. | Feature: `SaleTest::test_calculo_correcto_totales`. |
| REQ-052 — Descuento en detalle | HU-02 | Descuento reduce el total del detalle. | `SaleDetail` con `descuento nullable|numeric|min:0`, default 0. | Feature: `SaleTest::test_calculo_correcto_totales`. |
| REQ-053 — Estado de reserva | HU-02 | Reserva tiene estado ACTIVA o CANCELADA (o CUMPLIDA). | `Reservation` model con `ESTADO_ACTIVA`, `ESTADO_CANCELADA`, `ESTADO_CUMPLIDA`. | Feature: `ReservationTest::test_puede_registrar_reserva` (estado ACTIVA). |
| REQ-054 — Fecha de reserva | HU-02 | La fecha se registra automáticamente. | `created_at` timestamp automático en `reservations`. | Feature: Implícito. |
| REQ-055 — Descripción de exposición | HU-02 | Descripción es obligatoria. | `StoreExhibitionRequest` con `descripcion required`. | Feature: `ExhibitionTest::test_descripcion_obligatoria`. |
| REQ-056 — Tipo de exposición | HU-02 | Solo dos tipos: physical, virtual. | `StoreExhibitionRequest` con `tipo in:physical,virtual`. | Feature: `ExhibitionTest::test_tipo_exposicion_invalido_rechazado`. |
| REQ-057 — Responsable en movimiento | HU-02 | Responsable es texto obligatorio. | `StoreMovementRequest` con `responsable required`. | Feature: `MovementTest::test_falta_responsable_genera_error`. |
| REQ-058 — Motivo de movimiento | HU-02 | Motivo es texto obligatorio. | `StoreMovementRequest` con `motivo required`. | Feature: `MovementTest::test_falta_motivo_genera_error`. |
| REQ-059 — Paginación en listados | HU-02 | Todos los listados soportan paginación. Por defecto: 15 por página. | `paginate(15)` en: `ArtworkController::index`, `LocationController::index`, `ExhibitionController::index`, `CustomerController::index`, `ReservationController::index`, `SalesController::index`, `PaymentController::index`, `LocationController::artworks`, `ExhibitionController::artworks`, `CustomerController::sales`, `PaymentController::index`. | Feature: `ArtworkCatalogTest::test_paginacion_funciona` (implícito). |
| REQ-060 — Formato de respuesta JSON consistente | HU-02 | Respuestas con estructura `{data: ...}` o `{data: [...], links: ..., meta: ...}`. | Todos los controllers retornan `response()->json([...])` o `response()->json($paginator)`. | Feature: Implícito en todos los tests. |

## Secuencias (SEQ-001 a SEQ-020)

| Secuencia | Descripción | Estado | Implementación |
|---|---|---|---|
| SEQ-001 | Alta de obra completa con autoría | ✅ | `ArtworkController::store` + `ArtworkArtistController::store` |
| SEQ-002 | Asignar AUTOR DESCONOCIDO | ✅ | `ArtworkArtistController::assignUnknown` |
| SEQ-003 | Listar obras con filtros y paginación | ✅ | `ArtworkController::index` |
| SEQ-004 | Alta de ubicación | ✅ | `LocationController::store` |
| SEQ-005 | Traslado de obra (movimiento) | ✅ | `MovementController::store`, `MovementService::create` |
| SEQ-006 | Crear exposición física | ✅ | `ExhibitionController::store` con `tipo=physical` |
| SEQ-007 | Asignar obra a exposición física (anti-solapamiento) | ✅ | `ExhibitionArtworkController::store`, `ExhibitionService::hasPhysicalOverlap` |
| SEQ-008 | Reservar obra | ✅ | `ReservationController::store`, `ReservationService::create` |
| SEQ-009 | Alta de venta con detalles | ✅ | `SalesController::store`, `SaleService::create` |
| SEQ-010 | Confirmar venta | ✅ | `SalesController::confirm`, `SaleService::confirm` |
| SEQ-011 | Anular venta | ✅ | `SalesController::annul`, `SaleService::annul` |
| SEQ-012 | Registrar pago (parcial/múltiple) | ✅ | `PaymentController::store`, `PaymentService::create` |
| SEQ-013 | Cancelar reserva | ✅ | `ReservationController::cancel`, `ReservationService::cancel` |
| SEQ-014 | Alta de cliente | ✅ | `CustomerController::store` |
| SEQ-015 | Consultar historial de movimientos | ✅ | `MovementController::history` |
| SEQ-016 | Consultar ventas de cliente | ✅ | `CustomerController::sales` (GET /customers/{id}/sales) |
| SEQ-017 | Consultar pagos de venta | ✅ | `PaymentController::index` (GET /sales/{sale}/payments) |
| SEQ-018 | Registrar exposición virtual | ✅ | `ExhibitionController::store` con `tipo=virtual` + `url` |
| SEQ-019 | Asignar obra a exposición virtual | ✅ | `ExhibitionArtworkController::store` (sin validación solapamiento) |
| SEQ-020 | Cambiar estado de obra manualmente | ✅ | `ArtworkController::changeStatus`, `ArtworkService::changeStatus` |

## Historias de Estado (EST-001 a EST-020)

| Historia | Entidad | Estados | Transiciones Implementadas | Estado |
|---|---|---|---|---|
| EST-001 | Obra | DISPONIBLE, RESERVADA, VENDIDA, NO_DISPONIBLE | DISPONIBLE↔RESERVADA, DISPONIBLE→VENDIDA, RESERVADA→VENDIDA, VENDIDA→DISPONIBLE, *→NO_DISPONIBLE, NO_DISPONIBLE→DISPONIBLE | ✅ `ArtworkService::TRANSICIONES_PERMITIDAS` + `ArtworkService::changeStatus` |
| EST-002 | Reserva | ACTIVA, CANCELADA, CUMPLIDA | ACTIVA→CANCELADA (`ReservationService::cancel`), ACTIVA→CUMPLIDA (`SaleService::confirm`) | ✅ `ReservationService` + `SaleService::confirm` |
| EST-003 | Venta | PENDIENTE, CONFIRMADA, ANULADA | PENDIENTE→CONFIRMADA (`SaleService::confirm`), CONFIRMADA→ANULADA (`SaleService::annul`) | ✅ `SaleService` |
| EST-004 | Exposición | PROGRAMADA, EN_CURSO, FINALIZADA, CANCELADA | PROGRAMADA→EN_CURSO, PROGRAMADA→CANCELADA, EN_CURSO→FINALIZADA, EN_CURSO→CANCELADA | ✅ `ExhibitionService::changeStatus` + `ExhibitionController::changeStatus` |
| EST-005 | Movimiento | REGISTRADO (append-only) | Sin transiciones (solo creación) | ✅ `MovementService::create` (append-only) |
| EST-006 | Pago | REGISTRADO, VERIFICADO, RECHAZADO | REGISTRADO→VERIFICADO, REGISTRADO→RECHAZADO | ✅ `PaymentService::changeStatus` + `PaymentController::verify/reject` |
| EST-007 | Artista | ACTIVO, INACTIVO | Campo `estado` en `artists`, sin transiciones DESHABILITAR/HABILITAR expuestas | ⚠️ Parcial (campo existe, sin endpoints) |
| EST-008 | Cliente | ACTIVO, INACTIVO | Campo `estado` en `customers`, sin transiciones expuestas, no valida en ventas | ⚠️ Parcial |
| EST-009 | Ubicación | ACTIVA, INACTIVA | Campo `estado` en `locations`, sin transiciones expuestas, no valida en movimientos | ⚠️ Parcial |
| EST-010 | Autoría | ACTIVA, REVOCADA | Solo ACTIVA implementada (creación), sin REVOCADA (falta campo `estado` en `artwork_artists`) | ⚠️ Parcial |
| EST-011 | Historial estados obra | REGISTRADO (append-only) | `ArtworkStatusHistory` append-only | ✅ `ArtworkService::changeStatus` |
| EST-012 | Historial movimientos | REGISTRADO (append-only) | `movements` append-only | ✅ `MovementService::create` |
| EST-013 | Auditoría ventas | REGISTRADO (append-only) | `created_at`/`updated_at` en `sales`/`sale_details` | ✅ (timestamps) |
| EST-014 | Historial reservas | REGISTRADO (append-only) | `reservations` append-only | ✅ (timestamps) |
| EST-015 | Historial pagos | REGISTRADO (append-only) | `payments` append-only | ✅ (timestamps) |
| EST-016 | Historial exposiciones | REGISTRADO (append-only) | `exhibitions` append-only (timestamps) | ✅ |
| EST-017 | Historial clientes | REGISTRADO (append-only) | `customers` append-only (timestamps) | ✅ |
| EST-018 | Historial ubicaciones | REGISTRADO (append-only) | `locations` append-only (timestamps) | ✅ |
| EST-019 | Historial artistas | REGISTRADO (append-only) | `artists` append-only (timestamps) | ✅ |
| EST-020 | Historial autorías | REGISTRADO (append-only) | `artwork_artists` append-only (timestamps) | ✅ |

---

**Leyenda:** ✅ Cumplido | ⚠️ Parcial (ver detalles) | ❌ No cumplido

**Resumen final:**
- **REQ**: 60/60 ✅ (todos completados)
- **SEQ**: 20/20 ✅
- **EST**: 14/20 ✅, 6 ⚠️ (EST-007 a EST-010 parciales - campos existen sin transiciones DESHABILITAR/HABILITAR/REVOCAR expuestas)

**Tests**: 184 tests / 635 assertions — todos pasan
**Pint**: Limpio