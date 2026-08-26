# HU-02 — Matriz de Trazabilidad

## Matriz REQ → SEQ → EST → Tablas → Clases

| REQ | Descripción | SEQ | EST | Tablas | Clases |
|-----|-------------|-----|-----|--------|--------|
| REQ-001 | Registrar obra con título, naturaleza, descripción, dimensiones | SEQ-001 | EST-001 | artwork | Artwork, ArtworkController, StoreArtworkRequest |
| REQ-002 | Listar obras con filtros y paginación | SEQ-003 | - | artwork | ArtworkController, ArtworkService |
| REQ-003 | Consultar detalle de obra | SEQ-003 | - | artwork | ArtworkController |
| REQ-004 | Editar obra | SEQ-003 | EST-001 | artwork | ArtworkController, ArtworkService, UpdateArtworkRequest |
| REQ-005 | Eliminar obra (soft delete) | - | EST-001 | artwork | ArtworkController, ArtworkService |
| REQ-006 | Asociar artista existente a obra (tipo_autoria) | SEQ-001 | EST-010 | artwork_artists | ArtworkArtist, ArtworkArtistController, ArtworkService |
| REQ-007 | Asignar AUTOR DESCONOCIDO a obra | SEQ-002 | EST-010 | artwork_artists, artists | Artist::autorDesconocido(), ArtworkArtistController |
| REQ-008 | Listar artistas de una obra | SEQ-003 | - | artwork_artists | ArtworkArtistController |
| REQ-009 | Registrar ubicación con nombre único, capacidad | SEQ-004 | EST-009 | locations | Location, LocationController |
| REQ-010 | Listar ubicaciones | SEQ-004 | - | locations | LocationController |
| REQ-011 | Registrar movimiento con origen, destino, fecha, motivo, responsable | SEQ-005 | EST-005, EST-012 | movements | Movement, MovementController, MovementService |
| REQ-012 | Consultar historial de movimientos de una obra | SEQ-015 | EST-012 | movements | MovementController |
| REQ-013 | Registrar exposición (física/virtual) con fechas | SEQ-006, SEQ-018 | EST-004, EST-016 | exhibitions | Exhibition, ExhibitionController, ExhibitionService |
| REQ-014 | Asignar obra a exposición | SEQ-007, SEQ-019 | - | exhibition_artwork | ExhibitionArtwork, ExhibitionController |
| REQ-015 | Registrar reserva de obra | SEQ-008 | EST-002, EST-014 | reservations | Reservation, ReservationController, ReservationService |
| REQ-016 | Cancelar reserva | SEQ-013 | EST-002, EST-014 | reservations | Reservation, ReservationController, ReservationService |
| REQ-017 | Registrar cliente (nombre, apellido, documento, email, teléfono) | SEQ-014 | EST-008, EST-017 | customers | Customer, CustomerController |
| REQ-018 | Listar clientes | SEQ-014 | - | customers | CustomerController |
| REQ-019 | Registrar venta con uno o más detalles | SEQ-009 | EST-003, EST-013 | sales, sale_details | Sale, SaleDetail, SaleController, SaleService, StoreSaleRequest |
| REQ-020 | Confirmar venta | SEQ-010 | EST-003, EST-013 | sales | Sale, SaleController, SaleService |
| REQ-021 | Anular venta | SEQ-011 | EST-003, EST-013 | sales | Sale, SaleController, SaleService |
| REQ-022 | Registrar pago de venta | SEQ-012 | EST-006, EST-015 | payments | Payment, PaymentController, StorePaymentRequest |
| REQ-023 | Estados de obra: DISPONIBLE, RESERVADA, VENDIDA, NO_DISPONIBLE | SEQ-020 | EST-001 | artwork | Artwork, ArtworkStatusHistory |
| REQ-024 | Transiciones de estado válidas | SEQ-020 | EST-001 | artwork, artwork_status_history | Artwork, ArtworkService, ArtworkStatusHistory |
| REQ-025 | Filtro por estado de obra | SEQ-003 | - | artwork | ArtworkController |
| REQ-026 | Filtro por artista | SEQ-003 | - | artwork_artists, artists | ArtworkController |
| REQ-027 | Filtro por ubicación | SEQ-003 | - | artwork, locations | ArtworkController |
| REQ-028 | Paginación de resultados | SEQ-003 | - | - | ArtworkController (Paginator) |
| REQ-029 | Moneda BOB en ventas | SEQ-009 | - | sales | Sale |
| REQ-030 | Original: solo una copia vendida | SEQ-009 | - | sale_details, artwork | SaleService::validateExclusivity() |
| REQ-031 | Coherencia de fechas de exposición | SEQ-006 | EST-004 | exhibitions | ExhibitionService |
| REQ-032 | Sin solapamiento en exposiciones físicas | SEQ-007 | - | exhibition_artwork, exhibitions | ExhibitionService::validateOverlap() |
| REQ-033 | Cálculo de subtotales y totales | SEQ-009 | - | sale_details, sales | SaleDetail::calcularSubtotal(), Sale::calcularTotales() |
| REQ-034 | Campos opcionales: impuesto, descuento | SEQ-009 | - | sale_details | SaleDetail |
| REQ-035 | Auditoría de cambios de estado de venta | SEQ-010, SEQ-011 | EST-013 | sale_audits | SaleService (event/observer) |
| REQ-036 | Pago monto > 0 | SEQ-012 | - | payments | Payment, StorePaymentRequest |
| REQ-037 | Pago referencia o comprobante | SEQ-012 | - | payments | Payment, StorePaymentRequest |
| REQ-038 | Ventas por cliente | SEQ-016 | - | sales, customers | CustomerController::sales() |
| REQ-039 | Pagos por venta | SEQ-017 | - | payments, sales | SaleController::payments() |
| REQ-040 | Exposición virtual requiere URL | SEQ-018 | - | exhibitions | Exhibition, StoreExhibitionRequest |
| REQ-041 | Capacidad de ubicación | SEQ-005 | - | locations | Location::tieneCapacidad(), MovementService::validateCapacity() |
| REQ-042 | Registro de cambios de estado | SEQ-020 | EST-011 | artwork_status_history | ArtworkStatusHistory |
| REQ-043 | Campos obligatorios validados | SEQ-001 | - | - | StoreArtworkRequest, FormRequests |
| REQ-044 | Responsable en movimiento | SEQ-005 | - | movements | Movement |
| REQ-045 | Fecha en movimiento | SEQ-005 | - | movements | Movement |
| REQ-046 | Descripción en movimiento | SEQ-005 | - | movements | Movement |
| REQ-047 | Origen/destino distintos | SEQ-005 | - | movements | MovementService |
| REQ-048 | Detalle: precio unitario | SEQ-009 | - | sale_details | SaleDetail |
| REQ-049 | Detalle: impuesto por unidad | SEQ-009 | - | sale_details | SaleDetail |
| REQ-050 | Detalle: descuento por unidad | SEQ-009 | - | sale_details | SaleDetail |
| REQ-051 | Detalle: subtotal = precio - descuento + impuesto | SEQ-009 | - | sale_details | SaleDetail::calcularSubtotal() |
| REQ-052 | Total venta = suma de subtotales | SEQ-009 | - | sales | Sale::calcularTotales() |
| REQ-053 | Bloqueo de venta si obra reservada por otro | SEQ-008 | - | reservations | ReservationService |
| REQ-054 | Reserva liberada al cancelar | SEQ-013 | - | reservations, artwork | ReservationService::cancel() |
| REQ-055 | Exposición física: start_date < end_date | SEQ-006 | - | exhibitions | ExhibitionService |
| REQ-056 | Exposición: nombre, descripción, fechas obligatorias | SEQ-006, SEQ-018 | - | exhibitions | StoreExhibitionRequest |
| REQ-057 | Movimiento: origen/destino obligatorios | SEQ-005 | - | movements | StoreMovementRequest |
| REQ-058 | Movimiento: fecha, motivo, responsable obligatorios | SEQ-005 | - | movements | StoreMovementRequest |
| REQ-059 | Búsqueda por título de obra | SEQ-003 | - | artwork | ArtworkController |
| REQ-060 | Sorting por campos de obra | SEQ-003 | - | artwork | ArtworkController |

## Matriz EST → Tablas

| EST | Entidad | Tabla | Transiciones |
|-----|---------|-------|--------------|
| EST-001 | Obra | artwork | DISPONIBLE ↔ RESERVADA ↔ VENDIDA ↔ NO_DISPONIBLE |
| EST-002 | Reserva | reservations | ACTIVA → CANCELADA/CUMPLIDA |
| EST-003 | Venta | sales | PENDIENTE → CONFIRMADA → ANULADA |
| EST-004 | Exposición | exhibitions | PROGRAMADA → EN_CURSO → FINALIZADA/CANCELADA |
| EST-005 | Movimiento | movements | REGISTRADO → EN_TRANSITO → COMPLETADO |
| EST-006 | Pago | payments | REGISTRADO → VERIFICADO/RECHAZADO |
| EST-007 | Artista | artists | ACTIVO ↔ INACTIVO (HU-01) |
| EST-008 | Cliente | customers | ACTIVO ↔ INACTIVO |
| EST-009 | Ubicación | locations | ACTIVA ↔ INACTIVA |
| EST-010 | Autoría | artwork_artists | ACTIVA → REVOCADA |
| EST-011 | Historial estado obra | artwork_status_history | Append-only |
| EST-012 | Historial movimientos | movements | Append-only |
| EST-013 | Auditoría ventas | sale_audits | Append-only |
| EST-014 | Historial reservas | reservation_history | Append-only |
| EST-015 | Historial pagos | payment_history | Append-only |
| EST-016 | Historial exposiciones | exhibition_history | Append-only |
| EST-017 | Historial clientes | customer_history | Append-only |
| EST-018 | Historial ubicaciones | location_history | Append-only |
| EST-019 | Historial artistas | artist_history | Append-only (HU-01) |
| EST-020 | Historial autorías | artwork_artist_history | Append-only |

## Matriz SEQ → REQ

| SEQ | Descripción | REQs cubiertos |
|-----|-------------|----------------|
| SEQ-001 | Registrar obra con autoría | REQ-001, REQ-006, REQ-043 |
| SEQ-002 | Asignar AUTOR DESCONOCIDO | REQ-007 |
| SEQ-003 | Consultar catálogo de obras | REQ-002, REQ-025, REQ-026, REQ-027, REQ-059, REQ-060 |
| SEQ-004 | Registrar ubicación | REQ-009, REQ-010 |
| SEQ-005 | Registrar movimiento | REQ-011, REQ-041, REQ-044, REQ-045, REQ-046, REQ-047, REQ-057, REQ-058 |
| SEQ-006 | Registrar exposición física | REQ-013, REQ-031, REQ-055, REQ-056 |
| SEQ-007 | Asignar obra a exposición física | REQ-014, REQ-032 |
| SEQ-008 | Registrar reserva | REQ-015, REQ-053 |
| SEQ-009 | Registrar venta con detalles | REQ-019, REQ-029, REQ-030, REQ-033, REQ-034, REQ-048, REQ-049, REQ-050, REQ-051, REQ-052 |
| SEQ-010 | Confirmar venta | REQ-020, REQ-035 |
| SEQ-011 | Anular venta | REQ-021, REQ-035 |
| SEQ-012 | Registrar pago | REQ-022, REQ-036, REQ-037 |
| SEQ-013 | Cancelar reserva | REQ-016, REQ-054 |
| SEQ-014 | Registrar cliente | REQ-017, REQ-018 |
| SEQ-015 | Consultar historial de movimientos | REQ-012 |
| SEQ-016 | Consultar ventas de cliente | REQ-038 |
| SEQ-017 | Consultar pagos de venta | REQ-039 |
| SEQ-018 | Registrar exposición virtual | REQ-040, REQ-056 |
| SEQ-019 | Asignar obra a exposición virtual | REQ-014, REQ-032 |
| SEQ-020 | Cambiar estado de obra | REQ-023, REQ-024, REQ-042 |

## Cobertura de Requerimientos

| Rango | Total | Cubiertos | Pendientes |
|-------|-------|-----------|------------|
| REQ-001 a REQ-010 | 10 | 10 | 0 |
| REQ-011 a REQ-020 | 10 | 10 | 0 |
| REQ-021 a REQ-030 | 10 | 10 | 0 |
| REQ-031 a REQ-040 | 10 | 10 | 0 |
| REQ-041 a REQ-050 | 10 | 10 | 0 |
| REQ-051 a REQ-060 | 10 | 10 | 0 |
| **Total** | **60** | **60** | **0** |
