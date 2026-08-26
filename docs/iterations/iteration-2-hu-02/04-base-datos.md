# HU-02 — Diseño de Base de Datos

## Diagrama ER (Mermaid)

```mermaid
erDiagram
    artists ||--o{ artwork_artists : "tiene autorías"
    artworks ||--o{ artwork_artists : "tiene autores"
    artworks ||--o{ movements : "tiene movimientos"
    artworks ||--o{ exhibition_artwork : "participa en exposiciones"
    artworks ||--o{ sale_details : "se vende en"
    artworks ||--o{ reservations : "se reserva"
    artworks ||--o{ artwork_status_history : "cambia de estado"
    locations ||--o{ movements : "origen/destino"
    exhibitions ||--o{ exhibition_artwork : "contiene obras"
    customers ||--o{ sales : "realiza ventas"
    customers ||--o{ reservations : "hace reservas"
    sales ||--o{ sale_details : "tiene detalles"
    sales ||--o{ payments : "recibe pagos"

    artists {
        bigint id PK
        varchar nombre
        varchar apellido
        varchar nacionalidad
        varchar estado
        date fecha_nacimiento
        date fecha_fallecimiento
        text biografia
        boolean is_system
        timestamps
    }

    artworks {
        bigint id PK
        varchar titulo
        text descripcion
        varchar naturaleza
        varchar estado_comercial
        varchar dimensiones
        varchar tecnica
        int anio_creacion
        bigint current_location_id FK
        timestamp deleted_at
        timestamps
    }

    artwork_artists {
        bigint id PK
        bigint artwork_id FK
        bigint artist_id FK
        varchar tipo_autoria
        timestamps
    }

    locations {
        bigint id PK
        varchar nombre
        text descripcion
        int capacidad
        varchar estado
        timestamps
    }

    movements {
        bigint id PK
        bigint artwork_id FK
        bigint origin_location_id FK
        bigint destination_location_id FK
        date fecha
        text motivo
        varchar responsable
        timestamps
    }

    exhibitions {
        bigint id PK
        varchar nombre
        text descripcion
        varchar tipo
        varchar url
        date start_date
        date end_date
        varchar estado
        timestamps
    }

    exhibition_artwork {
        bigint id PK
        bigint exhibition_id FK
        bigint artwork_id FK
        timestamps
    }

    reservations {
        bigint id PK
        bigint artwork_id FK
        bigint customer_id FK
        varchar estado
        timestamps
    }

    customers {
        bigint id PK
        varchar nombre
        varchar apellido
        varchar documento
        varchar email
        varchar telefono
        varchar estado
        timestamps
    }

    sales {
        bigint id PK
        bigint customer_id FK
        varchar estado
        decimal subtotal
        decimal impuesto_total
        decimal descuento_total
        decimal total
        varchar moneda
        timestamps
    }

    sale_details {
        bigint id PK
        bigint sale_id FK
        bigint artwork_id FK
        decimal precio
        decimal impuesto
        decimal descuento
        decimal subtotal
        timestamps
    }

    payments {
        bigint id PK
        bigint sale_id FK
        decimal monto
        varchar metodo_pago
        text comprobante
        varchar estado
        timestamps
    }

    artwork_status_history {
        bigint id PK
        bigint artwork_id FK
        varchar estado_anterior
        varchar estado_nuevo
        varchar responsable
        timestamps
    }
```

## Tablas existentes (HU-01)

### artists (NO modificar)
- Ya implementada en HU-01.
- Se reutiliza mediante relación FK.

## Tablas nuevas (HU-02)

### artwork
- **Propósito:** Almacenar obras de arte.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `titulo` VARCHAR(255) NOT NULL
  - `descripcion` TEXT NULLABLE
  - `naturaleza` VARCHAR(20) NOT NULL (original|replica|reproduccion)
  - `estado_comercial` VARCHAR(20) NOT NULL DEFAULT 'disponible' (disponible|reservada|vendida|no_disponible)
  - `dimensiones` VARCHAR(100) NULLABLE
  - `tecnica` VARCHAR(100) NULLABLE
  - `anio_creacion` INT NULLABLE
  - `current_location_id` BIGINT UNSIGNED NULLABLE FK → locations.id
  - `deleted_at` TIMESTAMP NULLABLE (soft delete)
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_artwork_estado_comercial` ON (estado_comercial)
  - `idx_artwork_naturaleza` ON (naturaleza)
  - `idx_artwork_current_location` ON (current_location_id)
  - `idx_artwork_deleted_at` ON (deleted_at)
- **Restricciones:**
  - naturaleza IN ('original', 'replica', 'reproduccion')
  - estado_comercial IN ('disponible', 'reservada', 'vendida', 'no_disponible')

### artwork_artists
- **Propósito:** Relación N:M entre obras y artistas con tipo de autoría.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `artwork_id` BIGINT UNSIGNED NOT NULL FK → artworks.id ON DELETE CASCADE
  - `artist_id` BIGINT UNSIGNED NOT NULL FK → artists.id ON DELETE CASCADE
  - `tipo_autoria` VARCHAR(20) NOT NULL (confirmada|atribuida)
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_aa_artwork` ON (artwork_id)
  - `idx_aa_artist` ON (artist_id)
  - UNIQUE `uq_aa_artwork_artist` ON (artwork_id, artist_id)
- **Restricciones:**
  - tipo_autoria IN ('confirmada', 'atribuida')

### locations
- **Propósito:** Ubicaciones físicas del museo.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `nombre` VARCHAR(255) NOT NULL UNIQUE
  - `descripcion` TEXT NULLABLE
  - `capacidad` INT NOT NULL DEFAULT 0
  - `estado` VARCHAR(20) NOT NULL DEFAULT 'activa' (activa|inactiva)
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - UNIQUE `uq_location_nombre` ON (nombre)

### movements
- **Propósito:** Registro de traslados de obras.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `artwork_id` BIGINT UNSIGNED NOT NULL FK → artworks.id
  - `origin_location_id` BIGINT UNSIGNED NULLABLE FK → locations.id
  - `destination_location_id` BIGINT UNSIGNED NULLABLE FK → locations.id
  - `fecha` DATE NOT NULL
  - `motivo` TEXT NOT NULL
  - `responsable` VARCHAR(255) NOT NULL
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_movement_artwork` ON (artwork_id)
  - `idx_movement_origin` ON (origin_location_id)
  - `idx_movement_destination` ON (destination_location_id)
  - `idx_movement_fecha` ON (fecha)

### exhibitions
- **Propósito:** Exposiciones físicas y virtuales.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `nombre` VARCHAR(255) NOT NULL
  - `descripcion` TEXT NOT NULL
  - `tipo` VARCHAR(20) NOT NULL (physical|virtual)
  - `url` VARCHAR(500) NULLABLE (solo para virtual)
  - `start_date` DATE NOT NULL
  - `end_date` DATE NOT NULL
  - `estado` VARCHAR(20) NOT NULL DEFAULT 'programada' (programada|en_curso|finalizada|cancelada)
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_exhibition_tipo` ON (tipo)
  - `idx_exhibition_dates` ON (start_date, end_date)
  - `idx_exhibition_estado` ON (estado)
- **Restricciones:**
  - tipo IN ('physical', 'virtual')
  - end_date >= start_date
  - url required WHERE tipo = 'virtual'

### exhibition_artwork
- **Propósito:** Asignación de obras a exposiciones.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `exhibition_id` BIGINT UNSIGNED NOT NULL FK → exhibitions.id ON DELETE CASCADE
  - `artwork_id` BIGINT UNSIGNED NOT NULL FK → artworks.id ON DELETE CASCADE
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_ea_exhibition` ON (exhibition_id)
  - `idx_ea_artwork` ON (artwork_id)
  - UNIQUE `uq_ea_exhibition_artwork` ON (exhibition_id, artwork_id)

### reservations
- **Propósito:** Reservas de obras.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `artwork_id` BIGINT UNSIGNED NOT NULL FK → artworks.id
  - `customer_id` BIGINT UNSIGNED NOT NULL FK → customers.id
  - `estado` VARCHAR(20) NOT NULL DEFAULT 'activa' (activa|cancelada|cumplida)
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_reservation_artwork` ON (artwork_id)
  - `idx_reservation_customer` ON (customer_id)
  - `idx_reservation_estado` ON (estado)

### customers
- **Propósito:** Clientes del museo.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `nombre` VARCHAR(255) NOT NULL
  - `apellido` VARCHAR(255) NOT NULL
  - `documento` VARCHAR(50) NOT NULL UNIQUE
  - `email` VARCHAR(255) NULLABLE
  - `telefono` VARCHAR(50) NULLABLE
  - `estado` VARCHAR(20) NOT NULL DEFAULT 'activo' (activo|inactivo)
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - UNIQUE `uq_customer_documento` ON (documento)

### sales
- **Propósito:** Ventas realizadas.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `customer_id` BIGINT UNSIGNED NOT NULL FK → customers.id
  - `estado` VARCHAR(20) NOT NULL DEFAULT 'pendiente' (pendiente|confirmada|anulada)
  - `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0
  - `impuesto_total` DECIMAL(12,2) NOT NULL DEFAULT 0
  - `descuento_total` DECIMAL(12,2) NOT NULL DEFAULT 0
  - `total` DECIMAL(12,2) NOT NULL DEFAULT 0
  - `moneda` VARCHAR(3) NOT NULL DEFAULT 'BOB'
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_sale_customer` ON (customer_id)
  - `idx_sale_estado` ON (estado)
- **Restricciones:**
  - estado IN ('pendiente', 'confirmada', 'anulada')
  - moneda = 'BOB'

### sale_details
- **Propósito:** Detalle de cada obra en una venta.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `sale_id` BIGINT UNSIGNED NOT NULL FK → sales.id ON DELETE CASCADE
  - `artwork_id` BIGINT UNSIGNED NOT NULL FK → artworks.id
  - `precio` DECIMAL(12,2) NOT NULL
  - `impuesto` DECIMAL(12,2) NOT NULL DEFAULT 0
  - `descuento` DECIMAL(12,2) NOT NULL DEFAULT 0
  - `subtotal` DECIMAL(12,2) NOT NULL
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_sd_sale` ON (sale_id)
  - `idx_sd_artwork` ON (artwork_id)

### payments
- **Propósito:** Pagos de ventas.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `sale_id` BIGINT UNSIGNED NOT NULL FK → sales.id
  - `monto` DECIMAL(12,2) NOT NULL
  - `metodo_pago` VARCHAR(50) NOT NULL (efectivo|transferencia|otro)
  - `comprobante` TEXT NULLABLE
  - `estado` VARCHAR(20) NOT NULL DEFAULT 'registrado' (registrado|verificado|rechazado)
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_payment_sale` ON (sale_id)
  - `idx_payment_estado` ON (estado)

### artwork_status_history
- **Propósito:** Historial de cambios de estado de obras.
- **Columnas:**
  - `id` BIGINT PK AUTO_INCREMENT
  - `artwork_id` BIGINT UNSIGNED NOT NULL FK → artworks.id
  - `estado_anterior` VARCHAR(20) NOT NULL
  - `estado_nuevo` VARCHAR(20) NOT NULL
  - `responsable` VARCHAR(255) NULLABLE
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP
- **Índices:**
  - `idx_ash_artwork` ON (artwork_id)
  - `idx_ash_fecha` ON (created_at)
