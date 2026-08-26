# Iteración 1 — HU-01: catálogo de artistas

## Alcance

Implementar únicamente el registro y la consulta de artistas para el catálogo. No incluye obras, tipos de autoría aplicados a obras, ubicaciones, movimientos, exposiciones, reservas, ventas ni roles completos.

## Decisiones de dominio utilizadas

- Toda obra futura tendrá un autor registrado; para autoría histórica no identificable existirá el artista especial `AUTOR DESCONOCIDO`.
- La autoría `CONFIRMADA` o `ATRIBUIDA` corresponde a una obra y queda fuera de HU-01.
- El catálogo de artistas es responsabilidad del Gestor de Catálogo; la autorización concreta se implementará cuando se aborde seguridad/roles.

## Criterios técnicos mínimos

1. Inicializar una aplicación Laravel/PHP compatible con MySQL, sin componentes ajenos al MVP.
2. Incorporar una única representación persistente de artista, justificada por HU-01.
3. Permitir registrar un artista real/identificable y consultar el catálogo de artistas.
4. Garantizar la disponibilidad del registro especial `AUTOR DESCONOCIDO` sin duplicarlo.
5. Validar los datos mínimos solo después de acordar cuáles son obligatorios para un artista real; no se inventarán campos ni reglas biográficas.
6. Mantener pruebas automatizadas de las operaciones de HU-01.
7. No incorporar lógica de obras ni el atributo de autoría a la entidad de artista.

## Pruebas requeridas

| Nivel | Caso |
|---|---|
| Feature | Registrar un artista válido y consultarlo en el catálogo. |
| Feature | Rechazar datos que incumplan los campos mínimos aprobados. |
| Feature/integración | Crear o asegurar el registro especial `AUTOR DESCONOCIDO` y evitar duplicados. |
| Unitario | Regla de normalización/identificación del artista especial, solo si queda aislada fuera de la capa HTTP. |

## Trazabilidad

| Requisito | Historia | Regla | Implementación prevista | Prueba |
|---|---|---|---|---|
| Gestión de artistas: registrar y consultar | HU-01 | RN-02: un artista puede tener múltiples obras futuras | Recurso persistente de artista, interfaz de registro/consulta | Feature: registrar y consultar artista |
| Decisión 8: autor obligatorio | HU-01 | RN-17: toda obra futura tiene autor registrado | Registro especial `AUTOR DESCONOCIDO` dentro del catálogo | Feature/integración: existencia única del registro especial |
| Catálogo mantenible | HU-01 | No crear lógica de obras antes de HU-02 | Solo componentes de artista y pruebas asociadas | Revisión de alcance + suite |

## Verificación de entorno

El entorno de desarrollo debe proporcionar PHP y Composer antes de inicializar Laravel y ejecutar el conjunto de pruebas. La versión concreta de Laravel/PHP se determinará por compatibilidad en ese entorno y se documentará con el primer esqueleto técnico.
