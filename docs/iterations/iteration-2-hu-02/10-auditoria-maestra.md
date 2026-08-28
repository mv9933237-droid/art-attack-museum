# FASE 9 — Auditoría Maestra de HU-02

## 1. Resumen Ejecutivo

La auditoría de HU-02 revela una implementación **sólida en su estructura general** pero con **defectos significativos** en reglas de negocio críticas, transiciones de estado incompletas y reglas de integridad omitidas. De 60 requisitos, 48 están cumplidos, 5 parcialmente cumplidos y 7 no cumplidos. De 20 secuencias, 18 están cumplidas y 2 no cumplidas. De 20 historias de estado, 6 están plenamente implementadas, 5 parcialmente y 9 no implementadas.

**Hallazgo más crítico:** El cálculo de `subtotal` en `SaleDetail` ignora impuestos y descuentos, produciendo totales incorrectos en ventas con múltiples ítems.

---

## 2. Alcance Auditado

| Componente | Cantidad |
|---|---|
| Requerimientos REQ | 60 |
| Secuencias SEQ | 20 |
| Historias de estado EST | 20 |
| Tablas de BD | 13 |
| Modelos | 13 |
| Services | 5 |
| Controllers | 11 |
| Form Requests | 6 |
| Tests | 151 |
| Assertions | 538 |

---

## 3. Resultado REQ-001 → REQ-060

### ✅ Cumplidos (48)

| REQ | Descripción | Implementación | Prueba |
|---|---|---|---|
| REQ-001 | Registrar obra | ArtworkController::store, ArtworkService::create | ArtworkCatalogTest |
| REQ-002 | Consultar catálogo | ArtworkController::index (filtros, paginación) | ArtworkCatalogTest |
| REQ-003 | Consultar detalle | ArtworkController::show (relaciones) | ArtworkCatalogTest |
| REQ-004 | Editar obra | ArtworkController::update | ArtworkCatalogTest |
| REQ-005 | Eliminar obra (lógicamente) | ArtworkController::destroy + SoftDeletes | ArtworkCatalogTest |
| REQ-006 | Asociar artista a obra | ArtworkArtistController::store | ArtworkArtistTest |
| REQ-007 | Asignar AUTOR DESCONOCIDO | ArtworkArtistController::assignUnknown | IntegrationTest |
| REQ-008 | Consultar autores | ArtworkArtistController::index | ArtworkArtistTest |
| REQ-009 | Registrar ubicación | LocationController::store | LocationTest |
| REQ-010 | Consultar ubicaciones | LocationController::index, show | LocationTest |
| REQ-011 | Registrar movimiento | MovementController::store, MovementService::create | MovementTest |
| REQ-012 | Historial de movimientos | MovementController::history | MovementTest, IntegrationTest |
| REQ-013 | Registrar exposición | ExhibitionController::store | ExhibitionTest |
| REQ-014 | Asignar obra a exposición | ExhibitionArtworkController::store | ExhibitionTest |
| REQ-015 | Registrar reserva | ReservationController::store, ReservationService::create | ReservationTest |
| REQ-016 | Cancelar reserva | ReservationController::cancel, ReservationService::cancel | ReservationTest |
| REQ-017 | Registrar cliente | CustomerController::store | CustomerTest |
| REQ-018 | Consultar clientes | CustomerController::index, show | CustomerTest |
| REQ-019 | Registrar venta | SalesController::store, SaleService::create | SaleTest |
| REQ-020 | Confirmar venta | SalesController::confirm, SaleService::confirm | SaleTest |
| REQ-021 | Anular venta | SalesController::annul, SaleService::annul | SaleTest |
| REQ-022 | Registrar pago | PaymentController::store | PaymentTest |
| REQ-023 | Consultar estado de obra | GET /artworks/{id} retorna estado_comercial | ArtworkCatalogTest |
| REQ-024 | Cambiar estado de obra | ArtworkController::changeStatus | ArtworkCatalogTest |
| REQ-025 | Obras por ubicación | LocationController::artworks, index con location_id | LocationTest |
| REQ-026 | Obras por artista | Artist::artworks(), index con artist_id | ArtworkArtistTest |
| REQ-027 | Obras por estado | index con status filter | ArtworkCatalogTest |
| REQ-028 | Obras en exposición | ExhibitionController::artworks | ExhibitionTest |
| REQ-029 | Naturaleza de obra | StoreArtworkRequest validación | ArtworkCatalogTest |
| REQ-030 | Exclusividad original | SaleService::create validación | SaleTest, IntegrationTest |
| REQ-031 | Fechas exposición | StoreExhibitionRequest validación | ExhibitionTest |
| REQ-032 | Anti-solapamiento físico | ExhibitionService::hasPhysicalOverlap | ExhibitionTest, IntegrationTest |
| REQ-033 | Cálculo de totales | Sale::calcularTotales() | SaleTest |
| REQ-034 | Moneda BOB | SaleService::create moneda fija | SaleTest, IntegrationTest |
| REQ-035 | Auditoría ventas | created_at/updated_at automáticos | Implícito |
| REQ-036 | Pago parcial | PaymentController::store acepta montos parciales | PaymentTest |
| REQ-037 | Comprobante pago | Payment campo comprobante nullable | PaymentTest |
| REQ-039 | Pagos de venta | PaymentController::index | PaymentTest |
| REQ-040 | Exposición virtual | StoreExhibitionRequest (url required_if) | ExhibitionTest |
| REQ-042 | Historial estados obra | ArtworkService::changeStatus + ArtworkStatusHistory | ArtworkCatalogTest |
| REQ-043 | Validar artista existe | ArtworkArtistController (exists:artists) | ArtworkArtistTest |
| REQ-044 | Validar obra en movimientos | MovementService (findOrFail) | MovementTest |
| REQ-045 | Validar ubicaciones | MovementService (findOrFail) | MovementTest |
| REQ-046 | Fecha movimiento válida | StoreMovementRequest (before_or_equal:today) | MovementTest |
| REQ-047 | Obra sin ubicación | current_location_id nullable | ArtworkCatalogTest |
| REQ-048 | Validar cliente venta | SaleService (findOrFail) | SaleTest |
| REQ-049 | Al menos un detalle | StoreSaleRequest (required|array|min:1) | SaleTest |
| REQ-050 | Precio por detalle | SaleDetail::create con precio | SaleTest |
| REQ-051 | Impuesto detalle | SaleDetail impuesto nullable | SaleTest |
| REQ-052 | Descuento detalle | SaleDetail descuento nullable | SaleTest |
| REQ-053 | Estado reserva | Reservation default 'activa' | ReservationTest |
| REQ-054 | Fecha reserva | created_at automático | Implícito |
| REQ-055 | Descripción exposición | StoreExhibitionRequest (required) | ExhibitionTest |
| REQ-056 | Tipo exposición | StoreExhibitionRequest (in:physical,virtual) | ExhibitionTest |
| REQ-057 | Responsable movimiento | StoreMovementRequest (required) | MovementTest |
| REQ-058 | Motivo movimiento | StoreMovementRequest (required) | MovementTest |
| REQ-060 | Formato JSON | Todos los controllers retornan JSON consistente | Implícito |

### ⚠️ Parcialmente Cumplidos (5)

| REQ | Descripción | Gap |
|---|---|---|
| REQ-023 | Consultar estado de obra | No hay endpoint dedicado GET /artworks/{id}/status. El estado se retorna como parte del show general. Funcional pero no como endpoint separado. |
| REQ-033 | Cálculo de totales | **FALLA CRÍTICA:** `SaleDetail::calcularSubtotal()` nunca se invoca. El subtotal se asigna directamente como `precio`, ignorando impuesto y descuento. Totales incorrectos. |
| REQ-041 | Capacidad de ubicación | No se valida capacidad de destino al crear movimiento. REQ lo exige. |
| REQ-059 | Paginación | Solo ArtworkController usa paginate(15). Otros controllers retornan listas completas sin paginación. |
| REQ-038 | Ventas por cliente | No existe endpoint GET /customers/{id}/sales. |

### ❌ No Cumplidos (7)

| REQ | Descripción | Justificación |
|---|---|---|
| REQ-038 | Consultar ventas por cliente | No existe ruta ni controller method para GET /customers/{id}/sales. Diseño lo requiere (SEQ-016). |
| REQ-041 | Validar capacidad ubicación | No hay validación de capacidad en MovementService. Diseño lo requiere. |
| REQ-030 | Exclusividad original (parcial) | Solo verifica ventas CONFIRMADAS. No verifica ventas PENDIENTES. Dos pending pueden crearse para la misma original. |
| REQ-024 | Cambiar estado obra (parcial) | No hay transición NO_DISPONIBLE → DISPONIBLE desde endpoint. Solo posible mediante changeStatus manual con knowledge del estado. |
| REQ-035 | Auditoría ventas (parcial) | No hay tabla de auditoría dedicated (sale_audits). Solo timestamps automáticos. |
| REQ-022 | Pago (parcial) | No hay transición de estados de pago (registrado → verificado/rechazado). |

---

## 4. Resultado SEQ-001 → SEQ-020

### ✅ Cumplidas (18)

| SEQ | Descripción | Estado |
|---|---|---|
| SEQ-001 | Registrar obra con autoría | ✅ |
| SEQ-002 | Asignar AUTOR DESCONOCIDO | ✅ |
| SEQ-003 | Consultar catálogo | ✅ |
| SEQ-004 | Registrar ubicación | ✅ |
| SEQ-005 | Registrar movimiento | ✅ |
| SEQ-006 | Registrar exposición física | ✅ |
| SEQ-007 | Asignar obra a exposición física | ✅ |
| SEQ-008 | Registrar reserva | ✅ |
| SEQ-009 | Registrar venta con detalles | ✅ |
| SEQ-010 | Confirmar venta | ✅ |
| SEQ-011 | Anular venta | ✅ |
| SEQ-012 | Registrar pago | ✅ |
| SEQ-013 | Cancelar reserva | ✅ |
| SEQ-014 | Registrar cliente | ✅ |
| SEQ-015 | Consultar historial movimientos | ✅ |
| SEQ-017 | Consultar pagos de venta | ✅ |
| SEQ-018 | Registrar exposición virtual | ✅ |
| SEQ-019 | Asignar obra a exposición virtual | ✅ |

### ❌ No Cumplidas (2)

| SEQ | Descripción | Gap |
|---|---|---|
| SEQ-016 | Consultar ventas de cliente | No existe endpoint GET /customers/{id}/sales. |
| SEQ-020 | Cambiar estado de obra manualmente | Endpoint existe (changeStatus) pero no incluye transición NO_DISPONIBLE → DISPONIBLE. |

---

## 5. Resultado EST-001 → EST-020

### ✅ Implementadas (6)

| EST | Descripción | Estado |
|---|---|---|
| EST-001 | Estado de obra (máquina principal) | ✅ Transiciones implementadas en ArtworkService |
| EST-002 | Estado de reserva | ✅ Model con constantes ACTIVA/CANCELADA/CUMPLIDA |
| EST-003 | Estado de venta | ✅ Transiciones implementadas en SaleService |
| EST-005 | Estado de movimiento | N/A — movimientos no tienen campo estado |
| EST-010 | Estado de autoría | N/A — artwork_artists no tiene campo estado |
| EST-011 | Historial estados (append-only) | ✅ ArtworkStatusHistory implementado |

### ⚠️ Parcialmente Implementadas (5)

| EST | Descripción | Gap |
|---|---|---|
| EST-001 | Estado de obra | Transición NO_DISPONIBLE → DISPONIBLE no tiene endpoint dedicado |
| EST-002 | Estado de reserva | Transición ACTIVA → CUMPLIDA nunca se ejecuta (reservations quedan en 'activa' tras confirmar venta) |
| EST-004 | Estado de exposición | Modelo tiene constantes pero NO hay transiciones (programada → en_curso → finalizada) |
| EST-006 | Estado de pago | Modelo tiene constantes pero NO hay transiciones (registrado → verificado/rechazado) |
| EST-008 | Estado de cliente | Campo estado existe pero NO se valida en ventas ni reservas |

### ❌ No Implementadas (9)

| EST | Descripción |
|---|---|
| EST-007 | Estado de artista (activo/inactivo) — de HU-01 |
| EST-009 | Estado de ubicación (activa/inactiva) |
| EST-012 | Historial de movimientos (append-only) |
| EST-013 | Auditoría de ventas (sale_audits) |
| EST-014 | Historial de reservas |
| EST-015 | Historial de pagos |
| EST-016 | Historial de exposiciones |
| EST-017 | Historial de clientes |
| EST-018 | Historial de ubicaciones |
| EST-019 | Historial de artistas |
| EST-020 | Historial de autorías |

---

## 6. Auditoría de Máquinas de Estado

### Artwork (EST-001)

| Transición | Diseño | Implementación | Estado |
|---|---|---|---|
| DISPONIBLE → RESERVADA | ✅ Permitida | ✅ ReservationService::create | OK |
| DISPONIBLE → VENDIDA | ✅ Permitida | ✅ SaleService::confirm | OK |
| DISPONIBLE → NO_DISPONIBLE | ✅ Permitida | ✅ ArtworkService::changeStatus | OK |
| RESERVADA → DISPONIBLE | ✅ Permitida | ✅ ReservationService::cancel | OK |
| RESERVADA → VENDIDA | ✅ Permitida | ✅ ArtworkService::TRANSICIONES_PERMITIDAS lo permite | OK |
| VENDIDA → DISPONIBLE | ✅ Permitida | ✅ SaleService::annul | OK |
| NO_DISPONIBLE → DISPONIBLE | ✅ Permitida | ⚠️ Transición definida pero sin endpoint dedicado | PARCIAL |
| RESERVADA → NO_DISPONIBLE | ❌ Prohibida | ✅ No está en TRANSICIONES_PERMITIDAS | OK |
| VENDIDA → NO_DISPONIBLE | ❌ Prohibida | ✅ No está en TRANSICIONES_PERMITIDAS | OK |

### Reserva (EST-002)

| Transición | Diseño | Implementación | Estado |
|---|---|---|---|
| → ACTIVA | ✅ Al crear | ✅ ReservationService::create | OK |
| ACTIVA → CANCELADA | ✅ Permitida | ✅ ReservationService::cancel | OK |
| ACTIVA → CUMPLIDA | ✅ Al confirmar venta | ❌ Nunca se ejecuta | **FALLA** |

### Venta (EST-003)

| Transición | Diseño | Implementación | Estado |
|---|---|---|---|
| → PENDIENTE | ✅ Al crear | ✅ SaleService::create | OK |
| PENDIENTE → CONFIRMADA | ✅ Permitida | ✅ SaleService::confirm | OK |
| CONFIRMADA → ANULADA | ✅ Permitida | ✅ SaleService::annul | OK |
| PENDIENTE → ANULADA | ❌ Prohibida | ✅ SaleService::annul solo acepta CONFIRMADA | OK |
| CONFIRMADA → CONFIRMADA | ❌ Prohibida | ✅ SaleService::confirm solo acepta PENDIENTE | OK |
| ANULADA → CONFIRMADA | ❌ Prohibida | ✅ No hay transición | OK |

### Pago (EST-006)

| Transición | Diseño | Implementación | Estado |
|---|---|---|---|
| → REGISTRADO | ✅ Al crear | ✅ PaymentController::store | OK |
| REGISTRADO → VERIFICADO | ✅ Permitida | ❌ No hay endpoint | **NO IMPLEMENTADO** |
| REGISTRADO → RECHAZADO | ✅ Permitida | ❌ No hay endpoint | **NO IMPLEMENTADO** |

---

## 7. Auditoría de Reglas de Negocio

### 7.1 AUTOR DESCONOCIDO
- ✅ Solo accessible desde endpoint dedicado (`/unknown-author`)
- ✅ Único registro system ( Artist::autorDesconocido() )
- ✅ Tipo_autoria = confirmada
- ✅ No se puede crear desde endpoint general (validación en ArtworkArtistController)

### 7.2 AUTORÍA
- ✅ Tipos: confirmada, atribuida
- ✅ Múltiples artistas por obra
- ✅ Duplicados prevenidos por unique constraint (artwork_id, artist_id)

### 7.3 ORIGINAL — Exclusividad
- ⚠️ **Parcial:** Solo verifica ventas CONFIRMADAS
- ❌ **Falla:** No verifica ventas PENDIENTES. Dos pending pueden crearse para la misma original simultáneamente.

### 7.4 UBICACIONES
- ✅ Origen ≠ destino (validado en MovementService)
- ✅ Ubicación actual se actualiza con cada movimiento
- ✅ Historial append-only (movements table)
- ❌ No se valida capacidad de destino (REQ-041)
- ❌ No se valida estado de ubicación (activa/inactiva)

### 7.5 EXPOSICIONES
- ✅ Física vs virtual
- ✅ start_date < end_date
- ✅ URL obligatoria para virtual
- ✅ Anti-solapamiento físico
- ✅ Exposiciones consecutivas permitidas
- ✅ Una obra puede estar en física + virtual simultáneamente
- ✅ Duplicación prevenida por unique constraint
- ⚠️ **Parcial:** update() no re-valida fechas de solapamiento

### 7.6 RESERVAS
- ✅ Solo obras DISPONIBLES
- ✅ Cambio a RESERVADA
- ✅ Cancelación → DISPONIBLE
- ❌ **Falla:** Reserva no pasa a CUMPLIDA cuando se confirma venta
- ✅ Reservas duplicadas prevenidas (check en ReservationService)

### 7.7 VENTAS
- ✅ Moneda BOB
- ✅ Múltiples detalles
- ❌ **Falla CRÍTICA:** Cálculo de subtotal incorrecto (calcularSubtotal nunca se invoca)
- ⚠️ Impuestos y descuentos se almacenan pero no se suman correctamente al total
- ✅ Exclusividad original (parcial — solo confirmadas)

### 7.8 PAGOS
- ✅ monto > 0
- ✅ Métodos permitidos: efectivo, transferencia
- ❌ No se valida que monto ≤ total de venta
- ❌ No se valida estado de venta antes de aceptar pago
- ❌ Transiciones de estado no implementadas

---

## 8. Auditoría de Base de Datos

### 8.1 Tablas y Restricciones

| Tabla | PK | FKs | Índices | Unique | Observaciones |
|---|---|---|---|---|---|
| artists | ✅ | — | ✅ | — | HU-01 |
| locations | ✅ | — | ✅ estado | nombre | OK |
| artworks | ✅ | current_location_id → locations (nullOnDelete) | ✅ 4 idx | — | OK |
| artwork_artists | ✅ | artwork_id → artworks (cascade), artist_id → artists (cascade) | ✅ 2 idx | (artwork_id, artist_id) | OK |
| movements | ✅ | artwork_id → artworks, origin/destination → locations (nullOnDelete) | ✅ 4 idx | — | OK |
| exhibitions | ✅ | — | ✅ 3 idx | — | OK |
| exhibition_artwork | ✅ | exhibition_id → exhibitions (cascade), artwork_id → artworks (cascade) | ✅ 2 idx | (exhibition_id, artwork_id) | OK |
| customers | ✅ | — | ✅ estado | documento | OK |
| reservations | ✅ | artwork_id → artworks, customer_id → customers | ✅ 3 idx | — | Sin unique (artwork, customer) |
| sales | ✅ | customer_id → customers | ✅ 2 idx | — | OK |
| sale_details | ✅ | sale_id → sales (cascade), artwork_id → artworks | ✅ 2 idx | — | OK |
| payments | ✅ | sale_id → sales | ✅ 2 idx | — | OK |
| artwork_status_history | ✅ | artwork_id → artworks | ✅ 2 idx | — | OK |

### 8.2 Problemas de Integridad

1. **Sin CHECK constraints:** No hay restricciones CHECK para:
   - monto ≥ 0 en payments, sale_details, sales
   - precio ≥ 0 en sale_details
   - capacidad ≥ 0 en locations
   - start_date ≤ end_date en exhibitions
   - states values (DB no fuerza IN (...))

2. **ON DELETE inconsistente:**
   - artwork_artists: CASCADE (correcto)
   - exhibition_artwork: CASCADE (correcto)
   - sale_details → artworks: SIN ON DELETE (protegido por código)
   - reservations → artworks: SIN ON DELETE (protegido por código)
   - sales → customers: SIN ON DELETE (protegido por código)

3. **Registros huérfanos posibles:**
   - Si se elimina un artwork con reservas activas → huérfanos (FK sin CASCADE)
   - Si se elimina un customer con ventas → huérfanos (FK sin CASCADE)

---

## 9. Auditoría de Concurrencia

### 9.1 Atomicidad de Services

| Service | Operación | DB::transaction | Análisis |
|---|---|---|---|
| ArtworkService | changeStatus | ✅ | OK — actualiza + historial atómicamente |
| MovementService | create | ✅ | OK — crea movimiento + actualiza ubicación |
| ReservationService | create | ✅ | OK — crea reserva + cambia estado obra |
| ReservationService | cancel | ✅ | OK — cancela reserva + cambia estado obra |
| SaleService | create | ✅ | OK — crea venta + detalles atómicamente |
| SaleService | confirm | ✅ | OK — confirma venta + cambia estado obras |
| SaleService | annul | ✅ | OK — anula venta + restaura estado obras |
| ExhibitionService | assignArtwork | ❌ NO | No usa DB::transaction |

### 9.2 Riesgos de Condiciones de Carrera

| Escenario | Riesgo | Mitigación |
|---|---|---|
| Dos reservas simultáneas para la misma obra | **ALTO** — ambas pasan check isDisponible() antes de commit | DB::transaction aísla pero no previene race condition sin SELECT FOR UPDATE |
| Dos ventas simultáneas para la misma original | **ALTO** — ambas pasan check exclusividad | Solo verifica CONFIRMADAS, no PENDIENTES |
| Doble pago excediendo total | **MEDIO** — no hay validación de monto ≤ total | Ninguna |
| Confirmación + anulación simultánea | **BAJO** — DB::transaction previene | OK |

### 9.3 Aislamiento

- No se usa `DB::transaction` con nivel de aislamiento explícito
- No se usa `SELECT ... FOR UPDATE` en ninguna validación crítica
- El nivel por defecto de SQLite (serializable) mitiga parcialmente en tests, pero no en MySQL producción

---

## 10. Auditoría de Seguridad

### 10.1 Protecciones Existentes

| Aspecto | Estado |
|---|---|
| Mass assignment | ✅ Protegido — todos los modelos usan $fillable explícito |
| FK validation | ✅ Protegido — exists: en requests o findOrFail en services |
| SQL Injection | ✅ Protegido — Eloquent ORM + parameterized queries |
| XSS | ✅ Protegido — respuestas JSON, no HTML |
| Secretos en repo | ✅ Protegido — .gitignore incluye .env, database.sqlite |
| TLS | N/A — framework level |

### 10.2 Deudas de Seguridad

| Aspecto | Estado | Riesgo |
|---|---|---|
| Autenticación | ❌ No implementada | ALTO — cualquier usuario accede a cualquier endpoint |
| Autorización/Roles | ❌ No implementada | ALTO — no hay control de permisos |
| Soft deletes en obras con ventas | ❌ No validado | MEDIO — se puede borrar obra con venta confirmada |
| Validación de pago vs total | ❌ No implementada | MEDIO — se pueden registrar pagos excesivos |
| Rate limiting | ❌ No implementado | BAJO — sin API pública |

---

## 11. Auditoría de API / Rutas

### 11.1 Rutas HU-02

| Método | Ruta | Controller | Valido |
|---|---|---|---|
| GET | /artworks | ArtworkController::index | ✅ |
| POST | /artworks | ArtworkController::store | ✅ |
| GET | /artworks/{artwork} | ArtworkController::show | ✅ |
| PUT | /artworks/{artwork} | ArtworkController::update | ✅ |
| DELETE | /artworks/{artwork} | ArtworkController::destroy | ✅ |
| PUT | /artworks/{artwork}/status | ArtworkController::changeStatus | ✅ |
| GET | /artworks/{artwork}/artists | ArtworkArtistController::index | ✅ |
| POST | /artworks/{artwork}/artists | ArtworkArtistController::store | ✅ |
| DELETE | /artworks/{artwork}/artists/{artist} | ArtworkArtistController::destroy | ✅ |
| POST | /artworks/{artwork}/unknown-author | ArtworkArtistController::assignUnknown | ✅ |
| GET | /artworks/{artwork}/movements | MovementController::history | ✅ |
| GET | /artworks/{artwork}/exhibitions | ArtworkController::exhibitions | ✅ |
| POST | /movements | MovementController::store | ✅ |
| GET | /locations | LocationController::index | ✅ |
| POST | /locations | LocationController::store | ✅ |
| GET | /locations/{location} | LocationController::show | ✅ |
| PUT | /locations/{location} | LocationController::update | ✅ |
| GET | /locations/{location}/artworks | LocationController::artworks | ✅ |
| GET | /exhibitions | ExhibitionController::index | ✅ |
| POST | /exhibitions | ExhibitionController::store | ✅ |
| GET | /exhibitions/{exhibition} | ExhibitionController::show | ✅ |
| PUT | /exhibitions/{exhibition} | ExhibitionController::update | ✅ |
| GET | /exhibitions/{exhibition}/artworks | ExhibitionController::artworks | ✅ |
| POST | /exhibitions/{exhibition}/artworks | ExhibitionArtworkController::store | ✅ |
| DELETE | /exhibitions/{exhibition}/artworks/{artwork} | ExhibitionArtworkController::destroy | ✅ |
| GET | /customers | CustomerController::index | ✅ |
| POST | /customers | CustomerController::store | ✅ |
| GET | /customers/{customer} | CustomerController::show | ✅ |
| GET | /reservations | ReservationController::index | ✅ |
| POST | /reservations | ReservationController::store | ✅ |
| GET | /reservations/{reservation} | ReservationController::show | ✅ |
| POST | /reservations/{reservation}/cancel | ReservationController::cancel | ✅ |
| GET | /sales | SalesController::index | ✅ |
| POST | /sales | SalesController::store | ✅ |
| GET | /sales/{sale} | SalesController::show | ✅ |
| PUT | /sales/{sale}/confirm | SalesController::confirm | ✅ |
| PUT | /sales/{sale}/annul | SalesController::annul | ✅ |
| GET | /sales/{sale}/payments | PaymentController::index | ✅ |
| POST | /sales/{sale}/payments | PaymentController::store | ✅ |

### 11.2 Rutas Faltantes (diseño vs implementación)

| Ruta Esperada | REQ/SEQ | Estado |
|---|---|---|
| GET /customers/{id}/sales | REQ-038, SEQ-016 | ❌ No implementada |
| GET /artworks/{id}/status | REQ-023 | ❌ No endpoint dedicado |
| PUT /exhibitions/{id}/status | EST-004 | ❌ No implementada |
| PUT /payments/{id}/verify | EST-006 | ❌ No implementada |
| PUT /payments/{id}/reject | EST-006 | ❌ No implementada |

### 11.3 Inconsistencias

| Diseño | Implementación | Observación |
|---|---|---|
| SEQ-013 usa DELETE /reservations/{id} | Implementado como POST /reservations/{id}/cancel | No es RESTful pero funcional |

---

## 12. Auditoría de Código

### 12.1 Código Muerto

| Archivo | Método | Problema |
|---|---|---|
| Artwork.php | isReservada() | Nunca llamado |
| Artwork.php | isVendida() | Nunca llamado |
| Artwork.php | cambiarEstado() | Nunca llamado (se usa update directo) |
| SaleDetail.php | calcularSubtotal() | Nunca llamado — calcula fórmula correcta pero se ignora |
| Reservation.php | ESTADO_CUMPLIDA | Constante definida pero nunca usada |
| Payment.php | ESTADO_VERIFICADO | Constante definida pero nunca usada |
| Payment.php | ESTADO_RECHAZADO | Constante definida pero nunca usada |

### 12.2 Lógica Duplicada/Inconsistente

| Problema | Detalle |
|---|---|
| Subtotal calculation | `SaleDetail::calcularSubtotal()` formula: `precio - descuento + impuesto`. `SaleService::create()` usa: `subtotal = precio`. Resultados diferentes. |
| Naming inconsistency | Reservation/Exhibition usan `estaActiva()`. Customer/Location usan `isActive()`. Artist mezcla ambos. |

### 12.3 Lógica de Negocio en Controllers

| Controller | Lógica que debería estar en Service |
|---|---|
| ArtworkController::changeStatus | Validación de transición + try/catch |
| ReservationController::store | Validación de existencia + try/catch |
| ExhibitionArtworkController::store | Validación de existencia + try/catch |
| CustomerController::store | Validación de documento único |

### 12.4 Inconsistencias HTTP

| Problema | Endpoint | Detalle |
|---|---|---|
| POST para cancelar | /reservations/{id}/cancel | Debería ser DELETE según diseño SEQ-013 |
| Sin paginación | Location, Exhibition, Customer, Reservation, Sale, Payment | Solo ArtworkController pagina |

---

## 13. Pruebas Negativas Realizadas

| Prueba | Resultado | Evidencia |
|---|---|---|
| Vender obra reservada | ✅ Rechazada (422) | IntegrationTest::test_reserva_bloquea_venta_directa |
| Cancelar reserva y vender | ✅ Permitida | IntegrationTest::test_flujo_completo_artista_a_validator |
| Doble venta original | ✅ Rechazada (422) | IntegrationTest::test_obra_original_no_doble_venta |
| Solapamiento exposición física | ✅ Rechazada (422) | IntegrationTest::test_solapamiento_exposiciones_fisicas |
| Exposiciones virtuales simultáneas | ✅ Permitida | IntegrationTest::test_exposiciones_virtuales_simultaneas |
| Documento duplicado cliente | ✅ Rechazada (422) | IntegrationTest::test_cliente_documento_unico |
| Movimiento origen=destino | ✅ Rechazada (422) | MovementTest |
| Crear obra sin artista | ✅ Permitida (artista se asocia después) | ArtworkCatalogTest |
| Pago con monto excesivo | ⚠️ No hay validación — acepta montos ilimitados | PaymentController::store sin max validation |
| Múltiples pending para misma original | ⚠️ No hay prevención | SaleService solo verifica CONFIRMADAS |

---

## 14. Regresión

| Suite | Tests | Assertions | Estado |
|---|---|---|---|
| HU-01 (ArtistCatalogTest) | 23 | 75 | ✅ Todos pasan |
| HU-01 (AutorDesconocidoTest) | 3 | 9 | ✅ Todos pasan |
| HU-02 (todos) | 128 | 463 | ✅ Todos pasan |
| **Total** | **151** | **538** | **✅ Todos pasan** |
| Pint | — | — | ✅ Limpio |

---

## 15. Hallazgos

### CRÍTICO (1)

| # | Hallazgo | Detalle | Ubicación |
|---|---|---|---|
| C-001 | **Cálculo de subtotal incorrecto** | `SaleDetail::calcularSubtotal()` formula: `precio - descuento + impuesto`. Nunca se invoca. `SaleService::create()` asigna `subtotal = precio`. Totales incorrectos cuando hay impuestos/descuentos. | SaleService:50-57, SaleDetail:34 |

### ALTO (5)

| # | Hallazgo | Detalle | Ubicación |
|---|---|---|---|
| A-001 | **Reserva no pasa a CUMPLIDA** | Al confirmar venta, reservations queda en 'activa' indefinidamente. | SaleService::confirm, ReservationService |
| A-002 | **Sin transiciones de exposición** | Exhibition tiene 4 estados definidos pero ningún endpoint para transicionar (programada → en_curso → finalizada). | ExhibitionController |
| A-003 | **Sin transiciones de pago** | Payment tiene 3 estados pero ningún endpoint para verificar/rechazar. | PaymentController |
| A-004 | **Exclusividad original incompleta** | Solo verifica ventas CONFIRMADAS. Dos ventas PENDIENTES pueden crearse para la misma original. | SaleService::create:33-44 |
| A-005 | **Sin validación monto ≤ total venta** | PaymentController acepta montos sin validar contra total de venta. | PaymentController::store:14 |

### MEDIO (7)

| # | Hallazgo | Detalle | Ubicación |
|---|---|---|---|
| M-001 | Sin endpoint GET /customers/{id}/sales | REQ-038 no implementado. | routes/web.php |
| M-002 | Sin validación capacidad ubicación | REQ-041 no implementado. | MovementService::create |
| M-003 | Sin validación ubicación activa | No se verifica estado de ubicación en movimientos. | MovementService::create |
| M-004 | Sin validación obra con ventas antes de delete | ArtworkService::delete no verifica ventas confirmadas. | ArtworkService::delete |
| M-005 | Sin paginación en la mayoría de endpoints | Solo ArtworkController pagina. | Location, Exhibition, Customer, etc. |
| M-006 | update() de exposición no re-valida solapamiento | ExhibitionController::update permite cambiar fechas sin verificar overlap. | ExhibitionController::update |
| M-007 | Valores monetarios sin CHECK constraints | DB permite montos negativos en payments, sale_details, sales. | Migraciones |

### BAJO (5)

| # | Hallazgo | Detalle | Ubicación |
|---|---|---|---|
| B-001 | Naming inconsistente estaActiva() vs isActive() | Reservation/Exhibition: estaActiva(). Customer/Location: isActive(). | Modelos |
| B-002 | POST para cancelar reserva (no RESTful) | Diseño SEQ-013 dice DELETE. Implementado como POST /cancel. | routes/web.php:62 |
| B-003 | Código muerto | isReservada(), isVendida(), cambiarEstado(), calcularSubtotal() nunca se llaman. | Artwork, SaleDetail |
| B-004 | Lógica en controllers que debería estar en services | try/catch + validaciones manuales en ArtworkController, ReservationController. | Controllers |
| B-005 | ExhibitionController::update usa StoreExhibitionRequest | Mismo FormRequest para store y update, pero update debería permitir campos opcionales. | ExhibitionController |

### INFO (3)

| # | Hallazgo | Detalle |
|---|---|---|
| I-001 | EST-012 a EST-020 no implementadas | 9 historias append-only no implementadas (movimientos, ventas, reservas, pagos, exposiciones, clientes, ubicaciones, artistas, autorías). |
| I-002 | Exhibition::ESTADO_* constantes definidas pero no usadas | Modelo define 4 estados pero no se exponen en API. |
| I-003 | DB::transaction level no especificado | Se usa nivel default. En MySQL producción podría necesitar READ COMMITTED o REPEATABLE READ. |

---

## 16. Riesgos

| Riesgo | Severidad | Probabilidad | Impacto |
|---|---|---|---|
| Totales incorrectos en ventas con impuestos/descuentos | CRÍTICO | Alta | Datos financieros incorrectos |
| Reservas huérfanas en estado 'activa' después de venta confirmada | ALTO | Alta | Bloqueo lógico, datos inconsistentes |
| Exposiciones sin gestión de ciclo de vida | ALTO | Media | Estados desactualizados |
| Pagos sin verificación | ALTO | Media | Pagos fraudulentos no detectados |
| Race condition en reservas/ventas simultáneas | ALTO | Media | Doble reserva/venta |
| Borrado de obras con relaciones activas | MEDIO | Baja | Registros huérfanos |
| Sin autenticación/autorización | ALTO | — | Deuda técnica conocida |

---

## 17. Deuda Técnica

| Item | Categoría | Esfuerzo estimado |
|---|---|---|
| Autenticación y autorización | Seguridad | Alto |
| Tablas de auditoría (EST-013 a EST-020) | Trazabilidad | Alto |
| Transiciones de exposición | Funcionalidad | Medio |
| Transiciones de pago | Funcionalidad | Medio |
| Reserv → CUMPLIDA en confirmación de venta | Funcionalidad | Bajo |
| Paginación en todos los endpoints | Consistencia | Medio |
| Naming consistente (estaActiva vs isActive) | Calidad | Bajo |
| Eliminar código muerto | Calidad | Bajo |
| Migrar lógica de controllers a services | Arquitectura | Medio |
| CHECK constraints en BD | Integridad | Bajo |

---

## 18. Recomendaciones

1. **INMEDIATO (antes de merge):** Corregir C-001 (subtotal incorrecto). Es un defecto financiero.
2. **ALTO:** Implementar reservation → CUMPLIDA al confirmar venta.
3. **ALTO:** Agregar validación de exclusividad para ventas PENDIENTES en originales.
4. **ALTO:** Agregar validación de monto ≤ total en pagos.
5. **MEDIO:** Implementar transiciones de exposición y pago.
6. **MEDIO:** Agregar endpoint GET /customers/{id}/sales.
7. **BAJO:** Unificar naming, eliminar código muerto, migrar lógica a services.
8. **FUTURO:** Autenticación, roles, CHECK constraints, tablas de auditoría.

---

## 19. Veredicto Final

**HU-02 NO está lista para producción en su estado actual.**

El defecto crítico C-001 (cálculo de subtotal) produce datos financieros incorrectos. Los hallazgos altos A-001 a A-005 comprometen la integridad del ciclo de vida de ventas, reservas, exposiciones y pagos.

La implementación es **funcionalmente sólida** en su estructura general: la mayoría de los 60 REQ están implementados, los services usan transacciones atómicas, y los 151 tests pasan. Sin embargo, las fallas en reglas de negocio financieras y de ciclo de vida impiden considerarla completa.

**Se requiere FASE 10 de corrección antes de merge.**
