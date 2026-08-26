# HU-02 — Requerimientos (60)

## Requerimientos Funcionales

### REQ-001 — Registrar obra
- **ID:** REQ-001
- **Nombre:** Registrar obra de arte
- **Descripción:** Permitir registrar una obra con todos sus campos obligatorios y opcionales.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Artista registrado en el sistema (HU-01).
- **Resultado esperado:** Obra creada con estado DISPONIBLE y asociada a un artista.
- **Reglas de negocio:** Toda obra requiere título, artista, naturaleza y estado comercial inicial.
- **Entidades afectadas:** artwork, artists
- **Criterio de aceptación:** POST /artworks con datos válidos retorna 201 y registro persistido.

### REQ-002 — Consultar catálogo de obras
- **ID:** REQ-002
- **Nombre:** Listar obras
- **Descripción:** Permitir consultar el catálogo de obras con filtros básicos.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Al menos una obra registrada.
- **Resultado esperado:** Lista paginada de obras con datos del artista.
- **Reglas de negocio:** El catálogo excluye obras eliminadas lógicamente.
- **Entidades afectadas:** artwork, artists
- **Criterio de aceptación:** GET /artworks retorna lista con obras visibles.

### REQ-003 — Consultar detalle de obra
- **ID:** REQ-003
- **Nombre:** Ver obra individual
- **Descripción:** Permitir consultar una obra específica por su ID.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Obra registrada.
- **Resultado esperado:** Datos completos de la obra incluyendo artista y ubicación actual.
- **Reglas de negocio:** La respuesta incluye relación con artista y ubicación.
- **Entidades afectadas:** artwork, artists, locations
- **Criterio de aceptación:** GET /artworks/{id} retorna obra con relaciones.

### REQ-004 — Editar obra
- **ID:** REQ-004
- **Nombre:** Actualizar datos de obra
- **Descripción:** Permitir modificar campos editables de una obra existente.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Obra registrada.
- **Resultado esperado:** Campos actualizados sin cambiar estado comercial.
- **Reglas de negocio:** No se puede cambiar el artista original de una obra.
- **Entidades afectadas:** artwork
- **Criterio de aceptación:** PUT /artworks/{id} actualiza campos permitidos.

### REQ-005 — Eliminar obra (lógicamente)
- **ID:** REQ-005
- **Nombre:** Soft delete de obra
- **Descripción:** Permitir eliminar una obra sin borrarla físicamente.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Obra registrada, sin ventas confirmadas pendientes.
- **Resultado esperado:** Obra marcada como eliminada, no visible en catálogo.
- **Reglas de negocio:** No se puede eliminar una obra con venta confirmada.
- **Entidades afectadas:** artwork
- **Criterio de aceptación:** DELETE /artworks/{id} marca obra como eliminada.

### REQ-006 — Asociar artista a obra
- **ID:** REQ-006
- **Nombre:** Asignar autoría
- **Descripción:** Permitir asociar un artista registrado a una obra con tipo de autoría.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Artista y obra registrados.
- **Resultado esperado:** Relación artista-obra creada con tipo de autoría.
- **Reglas de negocio:** Toda obra tiene al menos un autor. Un autor puede ser CONFIRMADA o ATRIBUIDA.
- **Entidades afectadas:** artwork, artists, artwork_artists
- **Criterio de aceptación:** POST /artworks/{id}/artists crea la relación.

### REQ-007 — Asignar AUTOR DESCONOCIDO
- **ID:** REQ-007
- **Nombre:** Asignar autor desconocido
- **Descripción:** Permitir asignar el registro especial AUTOR DESCONOCIDO a una obra.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Obra registrada.
- **Resultado esperado:** Obra asociada a AUTOR DESCONOCIDO con autoría CONFIRMADA.
- **Reglas de negocio:** AUTOR DESCONOCIDO usa autoría CONFIRMADA. No representa a una persona real.
- **Entidades afectadas:** artwork, artists, artwork_artists
- **Criterio de_ACCEPTACIÓN:** POST /artworks/{id}/unknown-author asigna AUTOR DESCONOCIDO.

### REQ-008 — Consultar autores de obra
- **ID:** REQ-008
- **Nombre:** Listar autores de una obra
- **Descripción:** Permitir consultar qué artistas están asociados a una obra.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Obra registrada.
- **Resultado esperado:** Lista de artistas con tipo de autoría.
- **Reglas de negocio:** Cada autor tiene un tipo (CONFIRMADA o ATRIBUIDA).
- **Entidades afectadas:** artwork_artists, artists
- **Criterio de aceptación:** GET /artworks/{id}/artists retorna lista de autores.

### REQ-009 — Registrar ubicación
- **ID:** REQ-009
- **Nombre:** Crear ubicación física
- **Descripción:** Permitir registrar ubicaciones físicas del museo.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Ubicación creada con nombre y capacidad.
- **Reglas de negocio:** Toda ubicación tiene nombre único y capacidad definida.
- **Entidades afectadas:** locations
- **Criterio de aceptación:** POST /locations crea ubicación válida.

### REQ-010 — Consultar ubicaciones
- **ID:** REQ-010
- **Nombre:** Listar ubicaciones
- **Descripción:** Permitir consultar todas las ubicaciones registradas.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Lista de ubicaciones con capacidad y obras actuales.
- **Reglas de negocio:** La respuesta incluye conteo de obras en cada ubicación.
- **Entidades afectadas:** locations
- **Criterio de aceptación:** GET /locations retorna lista completa.

### REQ-011 — Registrar movimiento de obra
- **ID:** REQ-011
- **Nombre:** Crear movimiento
- **Descripción:** Permitir registrar el traslado de una obra de una ubicación a otra.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Obra registrada, origen y destino existentes.
- **Resultado esperado:** Movimiento registrado con fecha, origen, destino y responsable.
- **Reglas de negocio:** Todo movimiento conserva origen, destino, fecha, motivo y responsable.
- **Entidades afectadas:** movements, artwork, locations
- **Criterio de aceptación:** POST /movements crea registro con trazabilidad completa.

### REQ-012 — Consultar historial de movimientos
- **ID:** REQ-012
- **Nombre:** Historial de movimientos
- **Descripción:** Permitir consultar el historial de movimientos de una obra.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Obra con al menos un movimiento.
- **Resultado esperado:** Lista cronológica de movimientos.
- **Reglas de negocio:** El historial no debe sobrescribirse.
- **Entidades afectadas:** movements
- **Criterio de aceptación:** GET /artworks/{id}/movements retorna historial.

### REQ-013 — Registrar exposición
- **ID:** REQ-013
- **Nombre:** Crear exposición
- **Descripción:** Permitir registrar exposiciones físicas o virtuales.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Exposición creada con tipo, fechas y descripción.
- **Reglas de negocio:** Exposición puede ser física o virtual. Fechas definen el período.
- **Entidades afectadas:** exhibitions
- **Criterio de aceptación:** POST /exhibitions crea exposición válida.

### REQ-014 — Asignar obra a exposición
- **ID:** REQ-014
- **Nombre:** Asignar obra a exposición
- **Descripción:** Permitir asignar una obra a una exposición.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Obra y exposición registradas.
- **Resultado esperado:** Obra asignada a la exposición.
- **Reglas de negocio:** Una obra no puede participar en dos exposiciones físicas con períodos solapados. Sí puede participar en una física y una virtual simultáneamente.
- **Entidades afectadas:** exhibition_artwork, exhibitions, artwork
- **Criterio de aceptación:** POST /exhibitions/{id}/artworks asigna obra.

### REQ-015 — Registrar reserva
- **ID:** REQ-015
- **Nombre:** Crear reserva
- **Descripción:** Permitir registrar una reserva sobre una obra.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Obra en estado DISPONIBLE.
- **Resultado esperado:** Reserva creada, obra pasa a RESERVADA.
- **Reglas de negocio:** Una reserva vigente bloquea nuevas ventas. Solo obras DISPONIBLES pueden reservarse.
- **Entidades afectadas:** reservations, artwork
- **Criterio de aceptación:** POST /reservations crea reserva y cambia estado de obra.

### REQ-016 — Cancelar reserva
- **ID:** REQ-016
- **Nombre:** Cancelar reserva
- **Descripción:** Permitir cancelar una reserva existente.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Reserva activa sobre la obra.
- **Resultado esperado:** Reserva cancelada, obra vuelve a DISPONIBLE.
- **Reglas de negocio:** Solo se pueden cancelar reservas vigentes.
- **Entidades afectadas:** reservations, artwork
- **Criterio de aceptación:** DELETE /reservations/{id} cancela reserva.

### REQ-017 — Registrar cliente
- **ID:** REQ-017
- **Nombre:** Crear cliente
- **Descripción:** Permitir registrar clientes del museo.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Cliente registrado con nombre, documento y contacto.
- **Reglas de negocio:** Todo cliente tiene documento de identidad único.
- **Entidades afectadas:** customers
- **Criterio de aceptación:** POST /customers crea cliente válido.

### REQ-018 — Consultar clientes
- **ID:** REQ-018
- **Nombre:** Listar clientes
- **Descripción:** Permitir consultar el catálogo de clientes.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Lista de clientes registrados.
- **Reglas de negocio:** Los clientes se listan por apellido.
- **Entidades afectadas:** customers
- **Criterio de aceptación:** GET /customers retorna lista.

### REQ-019 — Registrar venta
- **ID:** REQ-019
- **Nombre:** Crear venta
- **Descripción:** Permitir registrar una venta de una o más obras a un cliente.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Cliente registrado, obra(s) DISPONIBLE(s).
- **Resultado esperado:** Venta creada con detalles, totales y estado PENDIENTE.
- **Reglas de negocio:** Una venta puede incluir múltiples obras. Cada detalle registra precio, subtotal, impuesto, descuento y total. Moneda: BOB. Una obra original solo puede tener una venta confirmada.
- **Entidades afectadas:** sales, sale_details, artwork, customers
- **Criterio de aceptación:** POST /sales crea venta con detalles.

### REQ-020 — Confirmar venta
- **ID:** REQ-020
- **Nombre:** Confirmar venta
- **Descripción:** Permitir confirmar una venta registrada.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Venta en estado PENDIENTE.
- **Resultado esperado:** Venta CONFIRMADA, obras cambian a VENDIDA.
- **Reglas de negocio:** Venta confirmada no se elimina físicamente. Puede pasar a ANULADA.
- **Entidades afectadas:** sales, sale_details, artwork
- **Criterio de aceptación:** PUT /sales/{id}/confirm cambia estado a CONFIRMADA.

### REQ-021 — Anular venta
- **ID:** REQ-021
- **Nombre:** Anular venta
- **Descripción:** Permitir anular una venta confirmada.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Venta en estado CONFIRMADA.
- **Resultado esperado:** Venta ANULADA, obras vuelven a DISPONIBLE.
- **Reglas de negocio:** La anulación debe conservar trazabilidad y auditoría.
- **Entidades afectadas:** sales, artwork
- **Criterio de aceptación:** PUT /sales/{id}/annul cambia estado a ANULADA.

### REQ-022 — Registrar pago
- **ID:** REQ-022
- **Nombre:** Registrar pago físico
- **Descripción:** Permitir registrar el pago de una venta.
- **Tipo:** Funcional
- **Prioridad:** Alta
- **Precondiciones:** Venta registrada.
- **Resultado esperado:** Pago registrado con monto, método y comprobante.
- **Reglas de negocio:** Pago es físico (efectivo/transferencia). Puede generar comprobante.
- **Entidades afectadas:** payments, sales
- **Criterio de aceptación:** POST /sales/{id}/payments registra pago.

### REQ-023 — Consultar estado de obra
- **ID:** REQ-023
- **Nombre:** Ver estado de obra
- **Descripción:** Permitir consultar el estado comercial actual de una obra.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Obra registrada.
- **Resultado esperado:** Estado actual (DISPONIBLE, RESERVADA, VENDIDA, NO_DISPONIBLE).
- **Reglas de negocio:** El estado comercial es independiente de la ubicación física.
- **Entidades afectadas:** artwork
- **Criterio de aceptación:** GET /artworks/{id}/status retorna estado.

### REQ-024 — Cambiar estado de obra
- **ID:** REQ-024
- **Nombre:** Actualizar estado de obra
- **Descripción:** Permitir cambiar el estado comercial de una obra.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Obra registrada.
- **Resultado esperado:** Estado actualizado según reglas de transición.
- **Reglas de negocio:** Las transiciones deben seguir la máquina de estados aprobada.
- **Entidades afectadas:** artwork
- **Criterio de aceptación:** PUT /artworks/{id}/status cambia estado válido.

### REQ-025 — Consultar obras por ubicación
- **ID:** REQ-025
- **Nombre:** Filtrar obras por ubicación
- **Descripción:** Permitir listar obras que se encuentran en una ubicación específica.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Ubicación registrada.
- **Resultado esperado:** Lista de obras en la ubicación indicada.
- **Reglas de negocio:** Se muestran obras según su último movimiento.
- **Entidades afectadas:** artwork, locations, movements
- **Criterio de aceptación:** GET /locations/{id}/artworks retorna obras.

### REQ-026 — Consultar obras por artista
- **ID:** REQ-026
- **Nombre:** Filtrar obras por artista
- **Descripción:** Permitir listar obras de un artista específico.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Artista registrado.
- **Resultado esperado:** Lista de obras del artista.
- **Reglas de negocio:** Se incluyen obras donde el artista es autor confirmado o atribuido.
- **Entidades afectadas:** artwork, artwork_artists
- **Criterio de aceptación:** GET /artists/{id}/artworks retorna obras.

### REQ-027 — Consultar obras por estado
- **ID:** REQ-027
- **Nombre:** Filtrar obras por estado
- **Descripción:** Permitir listar obras filtradas por estado comercial.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Lista de obras con el estado indicado.
- **Reglas de negocio:** Filtro válido: DISPONIBLE, RESERVADA, VENDIDA, NO_DISPONIBLE.
- **Entidades afectadas:** artwork
- **Criterio de aceptación:** GET /artworks?status=X retorna obras filtradas.

### REQ-028 — Consultar obras en exposición
- **ID:** REQ-028
- **Nombre:** Listar obras en exposición
- **Descripción:** Permitir consultar las obras participando en una exposición.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Exposición registrada.
- **Resultado esperado:** Lista de obras de la exposición.
- **Reglas de negocio:** Solo se muestran obras activas en la exposición.
- **Entidades afectadas:** exhibition_artwork, exhibitions
- **Criterio de aceptación:** GET /exhibitions/{id}/artworks retorna obras.

### REQ-029 — Registrar naturaleza de obra
- **ID:** REQ-029
- **Nombre:** Definir naturaleza
- **Descripción:** Asignar naturaleza (ORIGINAL, RÉPLICA, REPRODUCCIÓN) al registrar obra.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Obra creada con naturaleza válida.
- **Reglas de negocio:** Naturaleza es ORIGINAL, RÉPLICA o REPRODUCCIÓN.
- **Entidades afectadas:** artwork
- **Criterio de aceptación:** Solo valores permitidos son aceptados.

### REQ-030 — Exclusividad de obra original
- **ID:** REQ-030
- **Nombre:** Control de exclusividad
- **Descripción:** Una obra original no puede tener más de una venta confirmada.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Obra con naturaleza ORIGINAL.
- **Resultado esperado:** Segunda venta confirmada rechazada.
- **Reglas de negocio:** Solo original está sujeta a exclusividad de pieza única.
- **Entidades afectadas:** artwork, sales, sale_details
- **Criterio de aceptación:** Intento de segunda venta de original retorna error.

### REQ-031 — Validación de fechas de exposición
- **ID:** REQ-031
- **Nombre:** Fechas de exposición
- **Descripción:** Validar que las fechas de exposición sean coherentes.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Fechas de inicio anteriores a fecha de fin.
- **Reglas de negocio:** No se permite exposición con fecha fin anterior a inicio.
- **Entidades afectadas:** exhibitions
- **Criterio de aceptación:** Fechas inválidas son rechazadas.

### REQ-032 — Validación de solapamiento de exposiciones físicas
- **ID:** REQ-032
- **Nombre:** Anti-solapamiento físico
- **Descripción:** Verificar que una obra no participe en dos exposiciones físicas simultáneas.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Obra en al menos una exposición física.
- **Resultado esperado:** Asignación rechazada si hay solapamiento físico.
- **Reglas de negocio:** Una obra NO puede participar en dos exposiciones físicas con períodos solapados. SÍ puede participar en una física y una virtual.
- **Entidades afectadas:** exhibition_artwork, exhibitions
- **Criterio de aceptación:** Asignación con solapamiento físico retorna error.

### REQ-033 — Cálculo automático de totales en venta
- **ID:** REQ-033
- **Nombre:** Totales automáticos
- **Descripción:** Calcular subtotal, impuesto, descuento y total automáticamente.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Venta con uno o más detalles.
- **Resultado esperado:** Totales calculados correctamente.
- **Reglas de negocio:** Total = Suma(subtotales) + Suma(impuestos) - Suma(descuentos).
- **Entidades afectadas:** sales, sale_details
- **Criterio de aceptación:** Totales coinciden con cálculo manual.

### REQ-034 — Moneda fija BOB
- **ID:** REQ-034
- **Nombre:** Moneda del sistema
- **Descripción:** Todas las ventas usan moneda BOB (Bolivianos).
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Moneda fija en todas las ventas.
- **Reglas de negocio:** No se aceptan otras monedas.
- **Entidades afectadas:** sales
- **Criterio de aceptación:** Intento de usar otra moneda es rechazado.

### REQ-035 — Auditoría de ventas
- **ID:** REQ-035
- **Nombre:** Trazabilidad de ventas
- **Descripción:** Registrar fecha de creación, actualización y usuario responsable.
- **Tipo:** No funcional
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Campos created_at, updated_at actualizados.
- **Reglas de negocio:** Toda operación queda registrada.
- **Entidades afectadas:** sales, sale_details
- **Criterio de aceptación:** Timestamps se actualizan correctamente.

### REQ-036 — Pago parcial
- **ID:** REQ-036
- **Nombre:** Pagos parciales
- **Descripción:** Permitir registrar pagos parciales de una venta.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Venta registrada.
- **Resultado esperado:** Pago registrado, saldo pendiente actualizado.
- **Reglas de negocio:** Un venta puede tener múltiples pagos.
- **Entidades afectadas:** payments, sales
- **Criterio de aceptación:** POST /sales/{id}/payments acepta monto parcial.

### REQ-037 — Comprobante de pago
- **ID:** REQ-037
- **Nombre:** Generar comprobante
- **Descripción:** Asociar un comprobante a un pago registrado.
- **Tipo:** Funcional
- **Prioridad:** Baja
- **Precondiciones:** Pago registrado.
- **Resultado esperado:** Comprobante asociado al pago.
- **Reglas de negocio:** Comprobante es opcional.
- **Entidades afectadas:** payments
- **Criterio de aceptación:** Campo comprobante se almacena.

### REQ-038 — Consultar ventas por cliente
- **ID:** REQ-038
- **Nombre:** Historial de ventas del cliente
- **Descripción:** Listar todas las ventas de un cliente específico.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Cliente con al menos una venta.
- **Resultado esperado:** Lista de ventas del cliente.
- **Reglas de negocio:** Se muestran todas las ventas, incluyendo anuladas.
- **Entidades afectadas:** sales, customers
- **Criterio de aceptación:** GET /customers/{id}/sales retorna ventas.

### REQ-039 — Consultar pagos de venta
- **ID:** REQ-039
- **Nombre:** Detalle de pagos
- **Descripción:** Listar todos los pagos de una venta.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Venta con al menos un pago.
- **Resultado esperado:** Lista de pagos con montos y fechas.
- **Reglas de negocio:** Se muestran todos los pagos registrados.
- **Entidades afectadas:** payments
- **Criterio de aceptación:** GET /sales/{id}/payments retorna pagos.

### REQ-040 — Registrar_exposición virtual
- **ID:** REQ-040
- **Nombre:** Exposición virtual
- **Descripción:** Registrar exposiciones con URL en lugar de ubicación física.
- **Tipo:** Funcional
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Exposición virtual con URL accesible.
- **Reglas de negocio:** Exposición virtual tiene URL en lugar de ubicación física.
- **Entidades afectadas:** exhibitions
- **Criterio de aceptación:** POST /exhibitions con tipo=virtual acepta URL.

### REQ-041 — Validar capacidad de ubicación
- **ID:** REQ-041
- **Nombre:** Capacidad de ubicación
- **Descripción:** Validar que la ubicación no exceda su capacidad.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ubicación con capacidad definida.
- **Resultado esperado:** Rechazo si ubicación está llena.
- **Reglas de negocio:** No se puede asignar más obras de las que la capacidad permite.
- **Entidades afectadas:** locations, movements
- **Criterio de aceptación:** Movimiento a ubicación llena retorna error.

### REQ-042 — Historial de estados de obra
- **ID:** REQ-022
- **Nombre:** Trazabilidad de estados
- **Descripción:** Registrar cambios de estado de obra con fecha y responsable.
- **Tipo:** No funcional
- **Prioridad:** Media
- **Precondiciones:** Obra con cambios de estado.
- **Resultado esperado:** Historial completo de transiciones.
- **Reglas de negocio:** Todo cambio de estado queda registrado.
- **Entidades afectadas:** artwork_status_history
- **Criterio de aceptación:** Historial se registra automáticamente.

### REQ-043 — Validar existencia de artista al asignar
- **ID:** REQ-043
- **Nombre:** Artista debe existir
- **Descripción:** Verificar que el artista existe antes de asociarlo a una obra.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Error si artista no existe.
- **Reglas de negocio:** No se puede asociar artista inexistente.
- **Entidades afectadas:** artists, artwork_artists
- **Criterio de aceptación:** Asociación con ID inexistente retorna 404.

### REQ-044 — Validar existencia de obra en movimientos
- **ID:** REQ-044
- **Nombre:** Obra debe existir para movimiento
- **Descripción:** Verificar que la obra existe antes de registrar movimiento.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Error si obra no existe.
- **Reglas de negocio:** Movimiento requiere obra válida.
- **Entidades afectadas:** artwork, movements
- **Criterio de aceptación:** Movimiento con obra inexistente retorna error.

### REQ-045 — Validar existencia de ubicación en movimientos
- **ID:** REQ-045
- **Nombre:** Ubicaciones deben existir
- **Descripción:** Verificar origen y destino existen antes de registrar movimiento.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Error si origen o destino no existen.
- **Reglas de negocio:** Movimiento requiere ubicaciones válidas.
- **Entidades afectadas:** locations, movements
- **Criterio de aceptación:** Movimiento con ubicación inexistente retorna error.

### REQ-046 — Fecha de movimiento no futura
- **ID:** REQ-046
- **Nombre:** Fecha de movimiento válida
- **Descripción:** Validar que la fecha de movimiento no sea futura.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Error si fecha es futura.
- **Reglas de negocio:** No se permiten movimientos con fecha futura.
- **Entidades afectadas:** movements
- **Criterio de aceptación:** Fecha futura es rechazada.

### REQ-047 — Obra sin ubicación inicial
- **ID:** REQ-047
- **Nombre:** Ubicación opcional al crear
- **Descripción:** Una obra puede crearse sin ubicación asignada.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Obra creada sin ubicación.
- **Reglas de negocio:** La ubicación se asigna mediante movimientos.
- **Entidades afectadas:** artwork
- **Criterio de aceptación:** Obra sin location_id se crea correctamente.

### REQ-048 — Validar cliente para venta
- **ID:** REQ-048
- **Nombre:** Cliente debe existir para venta
- **Descripción:** Verificar que el cliente existe antes de crear venta.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Error si cliente no existe.
- **Reglas de negocio:** Toda venta requiere cliente válido.
- **Entidades afectadas:** customers, sales
- **Criterio de aceptación:** Venta con cliente inexistente retorna error.

### REQ-049 — Al menos un detalle en venta
- **ID:** REQ-049
- **Nombre:** Detalle mínimo en venta
- **Descripción:** Toda venta debe tener al menos un detalle (obra).
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Error si venta no tiene detalles.
- **Reglas de negocio:** Venta sin obras no es válida.
- **Entidades afectadas:** sales, sale_details
- **Criterio de aceptación:** Venta vacía retorna error de validación.

### REQ-050 — Precio por obra en detalle
- **ID:** REQ-050
- **Nombre:** Precio unitario en detalle
- **Descripción:** Cada detalle de venta registra el precio de la obra.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Precio registrado en cada detalle.
- **Reglas de negocio:** El precio es definido al momento de la venta.
- **Entidades afectadas:** sale_details
- **Criterio de aceptación:** Precio se almacena correctamente.

### REQ-051 — Impuesto en detalle
- **ID:** REQ-051
- **Nombre:** Impuesto por detalle
- **Descripción:** Cada detalle puede incluir impuesto calculado.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Impuesto registrado.
- **Reglas de negocio:** Impuesto es calculable por detalle.
- **Entidades afectadas:** sale_details
- **Criterio de aceptación:** Campo impuesto se almacena.

### REQ-052 — Descuento en detalle
- **ID:** REQ-052
- **Nombre:** Descuento por detalle
- **Descripción:** Cada detalle puede incluir descuento.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Descuento registrado.
- **Reglas de negocio:** Descuento reduce el total del detalle.
- **Entidades afectadas:** sale_details
- **Criterio de aceptación:** Campo descuento se almacena.

### REQ-053 — Estado de reserva
- **ID:** REQ-053
- **Nombre:** Estado de reserva
- **Descripción:** Reserva tiene estado ACTIVA o CANCELADA.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Reserva creada en estado ACTIVA.
- **Reglas de negocio:** Solo reservas ACTIVAS bloquean ventas.
- **Entidades afectadas:** reservations
- **Criterio de aceptación:** Estado se registra correctamente.

### REQ-054 — Fecha de reserva
- **ID:** REQ-054
- **Nombre:** Fecha de creación de reserva
- **Descripción:** Registrar fecha de creación de reserva.
- **Tipo:** No funcional
- **Prioridad:** Baja
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Timestamp de creación registrado.
- **Reglas de negocio:** La fecha se registra automáticamente.
- **Entidades afectadas:** reservations
- **Criterio de aceptación:** created_at se establece.

### REQ-055 — Descripción de exposición
- **ID:** REQ-055
- **Nombre:** Descripción de exposición
- **Descripción:** Toda exposición tiene descripción textual.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Descripción almacenada.
- **Reglas de negocio:** Descripción es obligatoria.
- **Entidades afectadas:** exhibitions
- **Criterio de aceptación:** Campo description se valida.

### REQ-056 — Tipo de exposición
- **ID:** REQ-056
- **Nombre:** Tipo de exposición
- **Descripción:** Exposición es física o virtual.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Tipo válido registrado.
- **Reglas de negocio:** Solo dos tipos: physical, virtual.
- **Entidades afectadas:** exhibitions
- **Criterio de aceptación:** Solo tipos permitidos son aceptados.

### REQ-057 — Responsable en movimiento
- **ID:** REQ-057
- **Nombre:** Responsable de movimiento
- **Descripción:** Todo movimiento registra el responsable.
- **Tipo:** Regla de negocio
- **Prioridad:** Alta
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Responsable registrado.
- **Reglas de negocio:** Responsable es texto obligatorio.
- **Entidades afectadas:** movements
- **Criterio de aceptación:** Campo responsible se valida como required.

### REQ-058 — Motivo de movimiento
- **ID:** REQ-058
- **Nombre:** Motivo de movimiento
- **Descripción:** Todo movimiento registra el motivo.
- **Tipo:** Regla de negocio
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Motivo registrado.
- **Reglas de negocio:** Motivo es texto obligatorio.
- **Entidades afectadas:** movements
- **Criterio de aceptación:** Campo reason se valida como required.

### REQ-059 — Paginación en listados
- **ID:** REQ-059
- **Nombre:** Paginación estándar
- **Descripción:** Todos los listados soportan paginación.
- **Tipo:** No funcional
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Respuestas paginadas con metadata.
- **Reglas de negocio:** Paginación por defecto: 15 por página.
- **Entidades afectadas:** Todas
- **Criterio de aceptación:** Respuesta incluye links y meta de paginación.

### REQ-060 — Formato de respuesta JSON
- **ID:** REQ-060
- **Nombre:** Respuesta JSON consistente
- **Descripción:** Todas las respuestas usan formato JSON consistente.
- **Tipo:** No funcional
- **Prioridad:** Media
- **Precondiciones:** Ninguna.
- **Resultado esperado:** Respuestas con estructura {data: ...} o {data: [...], links: ..., meta: ...}.
- **Reglas de negocio:** Consistencia en toda la API.
- **Entidades afectadas:** Todas
- **Criterio de aceptación:** Respuestas siguen formato definido.
