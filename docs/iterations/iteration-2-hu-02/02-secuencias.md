# HU-02 — Secuencias (20)

## SEQ-001 — Registrar obra con autoría
- **ID:** SEQ-001
- **Nombre:** Alta de obra completa
- **Objetivo:** Registrar una obra con todos sus datos y asociar un artista.
- **Actor:** Gestor de Catálogo
- **Precondiciones:** Artista registrado en HU-01.
- **Flujo principal:**
  1. Gestor envía POST /artworks con título, artista_id, naturaleza, descripción, dimensiones.
  2. Sistema valida campos obligatorios.
  3. Sistema crea obra con estado DISPONIBLE.
  4. Sistema asocia artista con autoría CONFIRMADA.
  5. Sistema retorna obra creada (201).
- **Flujos alternativos:**
  3a. Si artista no existe → error 404.
  3b. Si campos faltantes → error 422.
- **Excepciones:**
  - Error de BD → error 500.
- **Resultado:** Obra persistida con artista asociado.
- **Requerimientos:** REQ-001, REQ-006, REQ-043
- **Entidades:** artwork, artists, artwork_artists

## SEQ-002 — Asignar AUTOR DESCONOCIDO
- **ID:** SEQ-002
- **Nombre:** Autoría desconocida
- **Objetivo:** Asociar AUTOR DESCONOCIDO a una obra.
- **Actor:** Gestor de Catálogo
- **Precondiciones:** Obra registrada.
- **Flujo principal:**
  1. Gestor envía POST /artworks/{id}/unknown-author.
  2. Sistema obtiene o crea AUTOR DESCONOCIDO via Artist::autorDesconocido().
  3. Sistema asocia artista con autoría CONFIRMADA.
  4. Sistema retorna confirmación.
- **Flujos alternativos:**
  3a. Si ya tiene AUTOR DESCONOCIDO → retorna existente.
- **Resultado:** Obra asociada a AUTOR DESCONOCIDO.
- **Requerimientos:** REQ-007
- **Entidades:** artwork, artists, artwork_artists

## SEQ-003 — Consultar catálogo de obras
- **ID:** SEQ-003
- **Nombre:** Listar obras
- **Objetivo:** Obtener lista paginada de obras.
- **Actor:** Cualquier usuario
- **Precondiciones:** Ninguna.
- **Flujo principal:**
  1. Actor envía GET /artworks.
  2. Sistema aplica filtros (status, artist_id, location_id).
  3. Sistema pagina resultados.
  4. Sistema retorna lista con datos de artista.
- **Resultado:** Lista paginada de obras.
- **Requerimientos:** REQ-002, REQ-025, REQ-026, REQ-027, REQ-059
- **Entidades:** artwork, artists

## SEQ-004 — Registrar ubicación
- **ID:** SEQ-004
- **Nombre:** Alta de ubicación
- **Objetivo:** Crear una ubicación física del museo.
- **Actor:** Gestor de Inventario
- **Precondiciones:** Ninguna.
- **Flujo principal:**
  1. Gestor envía POST /locations con nombre, capacidad, descripción.
  2. Sistema valida nombre único.
  3. Sistema crea ubicación.
  4. Sistema retorna ubicación creada (201).
- **Flujos alternativos:**
  2a. Si nombre duplicado → error 422.
- **Resultado:** Ubicación persistida.
- **Requerimientos:** REQ-009
- **Entidades:** locations

## SEQ-005 — Registrar movimiento de obra
- **ID:** SEQ-005
- **Nombre:** Traslado de obra
- **Objetivo:** Mover una obra de ubicación origen a destino.
- **Actor:** Gestor de Inventario
- **Precondiciones:** Obra y ubicaciones registradas.
- **Flujo principal:**
  1. Gestor envía POST /movements con artwork_id, origin_location_id, destination_location_id, fecha, motivo, responsable.
  2. Sistema valida que origen y destino existen.
  3. Sistema valida capacidad de destino.
  4. Sistema crea movimiento.
  5. Sistema actualiza ubicación actual de la obra.
  6. Sistema retorna movimiento creado (201).
- **Flujos alternativos:**
  3a. Si destino sin capacidad → error 422.
- **Resultado:** Movimiento registrado, ubicación de obra actualizada.
- **Requerimientos:** REQ-011, REQ-041, REQ-044, REQ-045, REQ-057, REQ-058
- **Entidades:** movements, artwork, locations

## SEQ-006 — Registrar exposición física
- **ID:** SEQ-006
- **Nombre:** Crear exposición
- **Objetivo:** Registrar una exposición física con fechas.
- **Actor:** Gestor de Catálogo
- **Precondiciones:** Ninguna.
- **Flujo principal:**
  1. Gestor envía POST /exhibitions con nombre, tipo=physical, description, start_date, end_date.
  2. Sistema valida fechas coherentes.
  3. Sistema crea exposición.
  4. Sistema retorna exposición (201).
- **Flujos alternativos:**
  2a. Si fecha fin < inicio → error 422.
- **Resultado:** Exposición física persistida.
- **Requerimientos:** REQ-013, REQ-031, REQ-055, REQ-056
- **Entidades:** exhibitions

## SEQ-007 — Asignar obra a exposición física
- **ID:** SEQ-007
- **Nombre:** Participación en exposición
- **Objetivo:** Asignar una obra a una exposición física.
- **Actor:** Gestor de Catálogo
- **Precondiciones:** Obra y exposición registradas.
- **Flujo principal:**
  1. Gestor envía POST /exhibitions/{id}/artworks con artwork_id.
  2. Sistema verifica que la exposición es física.
  3. Sistema verifica solapamiento con otras exposiciones físicas de la obra.
  4. Sistema crea asignación.
  5. Sistema retorna confirmación (201).
- **Flujos alternativos:**
  3a. Si hay solapamiento físico → error 422.
- **Resultado:** Obra asignada a exposición.
- **Requerimientos:** REQ-014, REQ-032
- **Entidades:** exhibition_artwork, exhibitions, artwork

## SEQ-008 — Registrar reserva
- **ID:** SEQ-008
- **Nombre:** Reservar obra
- **Objetivo:** Reservar una obra disponible.
- **Actor:** Gestor Comercial
- **Precondiciones:** Obra en estado DISPONIBLE.
- **Flujo principal:**
  1. Gestor envía POST /reservations con artwork_id, customer_id.
  2. Sistema verifica que obra está DISPONIBLE.
  3. Sistema crea reserva en estado ACTIVA.
  4. Sistema cambia estado de obra a RESERVADA.
  5. Sistema retorna reserva (201).
- **Flujos alternativos:**
  2a. Si obra no está DISPONIBLE → error 422.
- **Resultado:** Reserva creada, obra reservada.
- **Requerimientos:** REQ-015, REQ-053
- **Entidades:** reservations, artwork

## SEQ-009 — Registrar venta con detalles
- **ID:** SEQ-009
- **Nombre:** Alta de venta
- **Objetivo:** Registrar una venta de una o más obras a un cliente.
- **Actor:** Gestor Comercial
- **Precondiciones:** Cliente registrado, obra(s) DISPONIBLE(s).
- **Flujo principal:**
  1. Gestor envía POST /sales con customer_id y detalles (artwork_id, precio, impuesto, descuento).
  2. Sistema valida cliente.
  3. Sistema valida que cada obra está DISPONIBLE.
  4. Sistema valida exclusividad de original (REQ-030).
  5. Sistema crea venta estado PENDIENTE.
  6. Sistema crea detalles con cálculo de totales (REQ-033).
  7. Sistema retorna venta (201).
- **Flujos alternativos:**
  3a. Si obra no DISPONIBLE → error 422.
  4a. Si original ya vendida → error 422.
- **Resultado:** Venta pendiente con detalles.
- **Requerimientos:** REQ-019, REQ-030, REQ-033, REQ-034, REQ-048, REQ-049, REQ-050, REQ-051, REQ-052
- **Entidades:** sales, sale_details, artwork, customers

## SEQ-010 — Confirmar venta
- **ID:** SEQ-010
- **Nombre:** Confirmación de venta
- **Objetivo:** Confirmar una venta pendiente.
- **Actor:** Gestor Comercial
- **Precondiciones:** Venta en estado PENDIENTE.
- **Flujo principal:**
  1. Gestor envía PUT /sales/{id}/confirm.
  2. Sistema verifica estado PENDIENTE.
  3. Sistema cambia estado de venta a CONFIRMADA.
  4. Sistema cambia estado de obras a VENDIDA.
  5. Sistema retorna venta actualizada.
- **Flujos alternativos:**
  2a. Si no está PENDIENTE → error 422.
- **Resultado:** Venta confirmada, obras marcadas como VENDIDAS.
- **Requerimientos:** REQ-020
- **Entidades:** sales, artwork

## SEQ-011 — Anular venta
- **ID:** SEQ-011
- **Nombre:** Anulación de venta
- **Objetivo:** Anular una venta confirmada.
- **Actor:** Gestor Comercial
- **Precondiciones:** Venta en estado CONFIRMADA.
- **Flujo principal:**
  1. Gestor envía PUT /sales/{id}/annul.
  2. Sistema verifica estado CONFIRMADA.
  3. Sistema cambia estado de venta a ANULADA.
  4. Sistema cambia estado de obras a DISPONIBLE.
  5. Sistema retorna venta actualizada.
- **Flujos alternativos:**
  2a. Si no está CONFIRMADA → error 422.
- **Resultado:** Venta anulada, obras disponibles.
- **Requerimientos:** REQ-021
- **Entidades:** sales, artwork

## SEQ-012 — Registrar pago
- **ID:** SEQ-012
- **Nombre:** Pago de venta
- **Objetivo:** Registrar un pago físico para una venta.
- **Actor:** Gestor Comercial
- **Precondiciones:** Venta registrada.
- **Flujo principal:**
  1. Gestor envía POST /sales/{id}/payments con monto, metodo_pago, comprobante.
  2. Sistema valida monto > 0.
  3. Sistema crea pago.
  4. Sistema retorna pago (201).
- **Flujos alternativos:**
  2a. Si monto <= 0 → error 422.
- **Resultado:** Pago registrado.
- **Requerimientos:** REQ-022, REQ-036, REQ-037
- **Entidades:** payments

## SEQ-013 — Cancelar reserva
- **ID:** SEQ-013
- **Nombre:** Cancelación de reserva
- **Objetivo:** Cancelar una reserva activa.
- **Actor:** Gestor Comercial
- **Precondiciones:** Reserva en estado ACTIVA.
- **Flujo principal:**
  1. Gestor envía DELETE /reservations/{id}.
  2. Sistema verifica estado ACTIVA.
  3. Sistema cambia estado de reserva a CANCELADA.
  4. Sistema cambia estado de obra a DISPONIBLE.
  5. Sistema retorna confirmación.
- **Resultado:** Reserva cancelada, obra disponible.
- **Requerimientos:** REQ-016
- **Entidades:** reservations, artwork

## SEQ-014 — Registrar cliente
- **ID:** SEQ-014
- **Nombre:** Alta de cliente
- **Objetivo:** Registrar un cliente del museo.
- **Actor:** Gestor Comercial
- **Precondiciones:** Ninguna.
- **Flujo principal:**
  1. Gestor envía POST /customers con nombre, apellido, documento, email, telefono.
  2. Sistema valida documento único.
  3. Sistema crea cliente.
  4. Sistema retorna cliente (201).
- **Flujos alternativos:**
  2a. Si documento duplicado → error 422.
- **Resultado:** Cliente persistido.
- **Requerimientos:** REQ-017
- **Entidades:** customers

## SEQ-015 — Consultar historial de movimientos
- **ID:** SEQ-015
- **Nombre:** Historial de obra
- **Objetivo:** Obtener historial completo de movimientos de una obra.
- **Actor:** Cualquier usuario
- **Precondiciones:** Obra con movimientos.
- **Flujo principal:**
  1. Actor envía GET /artworks/{id}/movements.
  2. Sistema ordena por fecha descendente.
  3. Sistema retorna lista de movimientos.
- **Resultado:** Lista cronológica de movimientos.
- **Requerimientos:** REQ-012
- **Entidades:** movements

## SEQ-016 — Consultar ventas de cliente
- **ID:** SEQ-016
- **Nombre:** Historial de ventas del cliente
- **Objetivo:** Listar todas las ventas de un cliente.
- **Actor:** Gestor Comercial
- **Precondiciones:** Cliente con ventas.
- **Flujo principal:**
  1. Gestor envía GET /customers/{id}/sales.
  2. Sistema retorna lista de ventas del cliente.
- **Resultado:** Lista de ventas.
- **Requerimientos:** REQ-038
- **Entidades:** sales, customers

## SEQ-017 — Consultar pagos de venta
- **ID:** SEQ-017
- **Nombre:** Detalle de pagos
- **Objetivo:** Listar pagos de una venta específica.
- **Actor:** Gestor Comercial
- **Precondiciones:** Venta con pagos.
- **Flujo principal:**
  1. Gestor envía GET /sales/{id}/payments.
  2. Sistema retorna lista de pagos.
- **Resultado:** Lista de pagos.
- **Requerimientos:** REQ-039
- **Entidades:** payments

## SEQ-018 — Registrar exposición virtual
- **ID:** SEQ-018
- **Nombre:** Exposición virtual
- **Objetivo:** Registrar una exposición con URL.
- **Actor:** Gestor de Catálogo
- **Precondiciones:** Ninguna.
- **Flujo principal:**
  1. Gestor envía POST /exhibitions con nombre, tipo=virtual, description, url, start_date, end_date.
  2. Sistema valida URL presente para tipo virtual.
  3. Sistema crea exposición.
  4. Sistema retorna exposición (201).
- **Resultado:** Exposición virtual persistida.
- **Requerimientos:** REQ-040, REQ-056
- **Entidades:** exhibitions

## SEQ-019 — Asignar obra a exposición virtual
- **ID:** SEQ-019
- **Nombre:** Participación virtual
- **Objetivo:** Asignar obra a exposición virtual.
- **Actor:** Gestor de Catálogo
- **Precondiciones:** Obra y exposición virtual registradas.
- **Flujo principal:**
  1. Gestor envía POST /exhibitions/{id}/artworks con artwork_id.
  2. Sistema verifica que exposición es virtual.
  3. Sistema permite asignación (sin verificación de solapamiento físico).
  4. Sistema retorna confirmación (201).
- **Resultado:** Obra asignada a exposición virtual.
- **Requerimientos:** REQ-014, REQ-032
- **Entidades:** exhibition_artwork

## SEQ-020 — Cambiar estado de obra manualmente
- **ID:** SEQ-020
- **Nombre:** Actualización de estado
- **Objetivo:** Cambiar el estado comercial de una obra.
- **Actor:** Gestor de Inventario
- **Precondiciones:** Obra registrada.
- **Flujo principal:**
  1. Gestor envía PUT /artworks/{id}/status con nuevo_estado.
  2. Sistema valida transición permitida.
  3. Sistema actualiza estado.
  4. Sistema registra en historial de estados.
  5. Sistema retorna obra actualizada.
- **Flujos alternativos:**
  2a. Si transición no permitida → error 422.
- **Resultado:** Estado actualizado.
- **Requerimientos:** REQ-024, REQ-042
- **Entidades:** artwork, artwork_status_history
