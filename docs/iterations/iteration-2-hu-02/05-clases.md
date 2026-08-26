# HU-02 — Diseño de Clases

## Diagrama de Clases (Mermaid)

```mermaid
classDiagram
    class Artist {
        <<Model HU-01>>
        +id: int
        +nombre: string
        +apellido: string
        +nacionalidad: string
        +estado: string
        +is_system: bool
        +isSystem(): bool
        +isActive(): bool
        +autorDesconocido(): static
    }

    class Artwork {
        <<Model>>
        +id: int
        +titulo: string
        +descripcion: string
        +naturaleza: string
        +estado_comercial: string
        +dimensiones: string
        +tecnica: string
        +anio_creacion: int
        +current_location_id: int
        +isDisponible(): bool
        +isReservada(): bool
        +isVendida(): bool
        +cambiarEstado(nuevoEstado): void
    }

    class ArtworkArtist {
        <<Model>>
        +id: int
        +artwork_id: int
        +artist_id: int
        +tipo_autoria: string
        +esConfirmada(): bool
        +esAtribuida(): bool
    }

    class Location {
        <<Model>>
        +id: int
        +nombre: string
        +descripcion: string
        +capacidad: int
        +estado: string
        +tieneCapacidad(): bool
    }

    class Movement {
        <<Model>>
        +id: int
        +artwork_id: int
        +origin_location_id: int
        +destination_location_id: int
        +fecha: date
        +motivo: string
        +responsable: string
    }

    class Exhibition {
        <<Model>>
        +id: int
        +nombre: string
        +descripcion: string
        +tipo: string
        +url: string
        +start_date: date
        +end_date: date
        +estado: string
        +esFisica(): bool
        +esVirtual(): bool
        +estaActiva(): bool
    }

    class ExhibitionArtwork {
        <<Model>>
        +id: int
        +exhibition_id: int
        +artwork_id: int
    }

    class Reservation {
        <<Model>>
        +id: int
        +artwork_id: int
        +customer_id: int
        +estado: string
        +estaActiva(): bool
    }

    class Customer {
        <<Model>>
        +id: int
        +nombre: string
        +apellido: string
        +documento: string
        +email: string
        +telefono: string
        +estado: string
    }

    class Sale {
        <<Model>>
        +id: int
        +customer_id: int
        +estado: string
        +subtotal: decimal
        +impuesto_total: decimal
        +descuento_total: decimal
        +total: decimal
        +moneda: string
        +calcularTotales(): void
        +confirmar(): void
        +anular(): void
    }

    class SaleDetail {
        <<Model>>
        +id: int
        +sale_id: int
        +artwork_id: int
        +precio: decimal
        +impuesto: decimal
        +descuento: decimal
        +subtotal: decimal
        +calcularSubtotal(): void
    }

    class Payment {
        <<Model>>
        +id: int
        +sale_id: int
        +monto: decimal
        +metodo_pago: string
        +comprobante: string
        +estado: string
    }

    class ArtworkStatusHistory {
        <<Model>>
        +id: int
        +artwork_id: int
        +estado_anterior: string
        +estado_nuevo: string
        +responsable: string
    }

    Artist "1" --> "*" ArtworkArtist : tiene autorías
    Artwork "1" --> "*" ArtworkArtist : tiene autores
    Artwork "1" --> "*" Movement : tiene movimientos
    Artwork "1" --> "*" ExhibitionArtwork : participa en exposiciones
    Artwork "1" --> "*" SaleDetail : se vende en
    Artwork "1" --> "*" Reservation : se reserva
    Artwork "1" --> "*" ArtworkStatusHistory : cambia de estado
    Location "1" --> "*" Movement : origen/destino
    Exhibition "1" --> "*" ExhibitionArtwork : contiene obras
    Customer "1" --> "*" Sale : realiza ventas
    Customer "1" --> "*" Reservation : hace reservas
    Sale "1" --> "*" SaleDetail : tiene detalles
    Sale "1" --> "*" Payment : recibe pagos
```

## Models (Entidades de dominio)

### Artist (HU-01 — no modificar)
- **Responsabilidad:** Representar un artista del museo.
- **No se modifica.**

### Artwork
- **Responsabilidad:** Representar una obra de arte con todos sus atributos.
- **Atributos:** titulo, descripcion, naturaleza, estado_comercial, dimensiones, tecnica, anio_creacion, current_location_id, deleted_at.
- **Métodos:** isDisponible(), isReservada(), isVendida(), cambiarEstado().
- **Relaciones:** artists (N:M via artwork_artists), location (1:N), movements (1:N), exhibitions (N:M via exhibition_artwork), sales (N:M via sale_details), reservations (1:N), statusHistory (1:N).
- **Reglas:** Estado comercial sigue máquina de estados. Soft delete.

### ArtworkArtist
- **Responsabilidad:** Modelar la relación entre obra y artista con tipo de autoría.
- **Atributos:** artwork_id, artist_id, tipo_autoria.
- **Reglas:** tipo_autoria es 'confirmada' o 'atribuida'.

### Location
- **Responsabilidad:** Representar una ubicación física del museo.
- **Atributos:** nombre, descripcion, capacidad, estado.
- **Métodos:** tieneCapacidad().

### Movement
- **Responsabilidad:** Registrar el traslado de una obra entre ubicaciones.
- **Atributos:** artwork_id, origin_location_id, destination_location_id, fecha, motivo, responsable.

### Exhibition
- **Responsabilidad:** Representar una exposición (física o virtual).
- **Atributos:** nombre, descripcion, tipo, url, start_date, end_date, estado.
- **Métodos:** esFisica(), esVirtual(), estaActiva().

### ExhibitionArtwork
- **Responsabilidad:** Asignación de obras a exposiciones.
- **Atributos:** exhibition_id, artwork_id.

### Reservation
- **Responsabilidad:** Registrar la reserva de una obra por un cliente.
- **Atributos:** artwork_id, customer_id, estado.
- **Métodos:** estaActiva().

### Customer
- **Responsabilidad:** Representar un cliente del museo.
- **Atributos:** nombre, apellido, documento, email, telefono, estado.

### Sale
- **Responsabilidad:** Representar una venta con sus detalles y totales.
- **Atributos:** customer_id, estado, subtotal, impuesto_total, descuento_total, total, moneda.
- **Métodos:** calcularTotales(), confirmar(), anular().
- **Relaciones:** customers (N:1), saleDetails (1:N), payments (1:N).

### SaleDetail
- **Responsabilidad:** Detalle de una obra en una venta.
- **Atributos:** sale_id, artwork_id, precio, impuesto, descuento, subtotal.
- **Métodos:** calcularSubtotal().

### Payment
- **Responsabilidad:** Registrar un pago de una venta.
- **Atributos:** sale_id, monto, metodo_pago, comprobante, estado.

### ArtworkStatusHistory
- **Responsabilidad:** Registrar cambios de estado de obras.
- **Atributos:** artwork_id, estado_anterior, estado_nuevo, responsable.

## Controllers

### ArtworkController
- **Responsabilidad:** CRUD de obras.
- **Acciones:** index, store, show, update, destroy, changeStatus.

### ArtworkArtistController
- **Responsabilidad:** Gestión de autores de una obra.
- **Acciones:** index, store (asociar), destroy (desasociar), assignUnknown.

### LocationController
- **Responsabilidad:** CRUD de ubicaciones.
- **Acciones:** index, store, show, update.

### MovementController
- **Responsabilidad:** Registro de movimientos.
- **Acciones:** index, store, history (por obra).

### ExhibitionController
- **Responsabilidad:** CRUD de exposiciones.
- **Acciones:** index, store, show, update, assignArtwork, removeArtwork.

### ReservationController
- **Responsabilidad:** Gestión de reservas.
- **Acciones:** index, store, cancel.

### CustomerController
- **Responsabilidad:** CRUD de clientes.
- **Acciones:** index, store, show, update, sales.

### SaleController
- **Responsabilidad:** Gestión de ventas.
- **Acciones:** index, store, show, confirm, annul, payments.

### PaymentController
- **Responsabilidad:** Registro de pagos.
- **Acciones:** store (para una venta).

## Requests (Form Requests)

### StoreArtworkRequest
- Valida campos de creación de obra.

### UpdateArtworkRequest
- Valida campos de actualización de obra.

### StoreMovementRequest
- Valida creación de movimiento.

### StoreExhibitionRequest
- Valida creación de exposición.

### StoreReservationRequest
- Valida creación de reserva.

### StoreCustomerRequest
- Valida creación de cliente.

### StoreSaleRequest
- Valida creación de venta con detalles.

### StorePaymentRequest
- Valida creación de pago.

## Services (acciones de dominio)

### ArtworkService
- **Responsabilidad:** Lógica de negocio de obras.
- **Métodos:** create(), update(), changeStatus(), assignArtist(), removeArtist(), assignUnknownAuthor().

### SaleService
- **Responsabilidad:** Lógica de negocio de ventas.
- **Métodos:** create(), confirm(), annul(), calculateTotals(), validateExclusivity().

### ReservationService
- **Responsabilidad:** Lógica de negocio de reservas.
- **Métodos:** create(), cancel().

### MovementService
- **Responsabilidad:** Lógica de negocio de movimientos.
- **Métodos:** create(), validateCapacity().

### ExhibitionService
- **Responsabilidad:** Lógica de negocio de exposiciones.
- **Métodos:** create(), assignArtwork(), validateOverlap().

## Policies

### ArtworkPolicy
- **Responsabilidad:** Verificar permisos sobre obras.
- **Métodos:** create(), update(), delete(), changeStatus().

### SalePolicy
- **Responsabilidad:** Verificar permisos sobre ventas.
- **Métodos:** create(), confirm(), annul().

## DTOs

### ArtworkDTO
- Transferencia de datos de obra entre capas.

### SaleDTO
- Transferencia de datos de venta.

### SaleDetailDTO
- Transferencia de datos de detalle de venta.
