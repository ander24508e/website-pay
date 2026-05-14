# Catalogo Universal

## Objetivo
Convertir el catalogo actual, hoy centrado en `products` y `services`, en una estructura reutilizable para multiples tipos de negocio dentro de una misma empresa.

Ejemplos:
- Taller: servicios, productos, bar
- Restaurante: comida, bebidas, postres
- Negocio mixto: industrial, servicios, productos, bar

## Estado actual
Hoy el proyecto depende de dos tipos fijos:
- `Product`
- `Service`

Eso impacta:
- base de datos
- admin
- website
- carrito
- ordenes
- reservas

## Vision objetivo
El sistema debe evolucionar hacia un catalogo universal compuesto por:

1. `CatalogType`
Define la seccion visible del negocio.

Ejemplos:
- Servicios
- Productos
- Comida
- Bebidas
- Bar
- Industrial

2. `CatalogCategory`
Organiza items dentro de un tipo.

Ejemplos:
- Servicios > Lavado
- Comida > Entradas
- Bebidas > Cocteles

3. `CatalogItem`
Item universal visible en website y administrable desde panel.

Campos base sugeridos:
- empresa_id
- catalog_type_id
- catalog_category_id nullable
- name
- slug nullable
- description nullable
- base_price nullable
- image nullable
- active
- featured
- purchasable
- reservable
- sort_order nullable

4. `CatalogItemVariant`
Variantes opcionales para items que lo necesiten.

Ejemplos:
- 250 ml
- litro
- combo
- porcion

## Reglas de negocio sugeridas
- Un item puede ser solo visible
- Un item puede ser comprable
- Un item puede ser reservable
- Un item puede ser ambas cosas si el negocio lo necesita
- El website no debe asumir que todo es producto o servicio

## Estrategia recomendada
No reemplazar el sistema actual de golpe.

### Fase 1
- Crear modulo padre `Catalogo` en admin
- Mantener `products`, `services`, `categories` y `banners` funcionando
- Documentar arquitectura nueva

### Fase 2
- Crear tablas nuevas:
  - `catalog_types`
  - `catalog_categories`
  - `catalog_items`
  - `catalog_item_variants`
- Relacionarlas con `empresa`

#### Estructura base aprobada para esta fase

`catalog_types`
- empresa_id
- name
- slug nullable
- description nullable
- icon nullable
- sort_order
- active

`catalog_categories`
- empresa_id
- catalog_type_id
- name
- slug nullable
- description nullable
- sort_order
- active

`catalog_items`
- empresa_id
- catalog_type_id
- catalog_category_id nullable
- legacy_source_type nullable
- legacy_source_id nullable
- name
- slug nullable
- description nullable
- base_price nullable
- image nullable
- active
- featured
- purchasable
- reservable
- sort_order
- metadata nullable

`catalog_item_variants`
- catalog_item_id
- name
- presentation nullable
- specification nullable
- sku nullable
- price nullable
- stock nullable
- active
- is_default
- sort_order
- metadata nullable

Los campos `legacy_source_type` y `legacy_source_id` se incluyen para facilitar la futura migracion desde `products` y `services` sin perder trazabilidad.

### Fase 3
- Crear admin nuevo:
  - vista general de catalogo
  - CRUD de tipos
  - CRUD de categorias universales
  - CRUD de items universales
  - CRUD de variantes

### Fase 4
- Adaptar website para leer primero del catalogo universal
- Mantener compatibilidad temporal con `products` y `services`

### Fase 5
- Adaptar carrito y ordenes para que apunten a `CatalogItem`
- Evaluar si `OrderItem` mantiene morph o pasa a FK directa

### Fase 6
- Migrar datos existentes:
  - `products` -> `catalog_items`
  - `services` -> `catalog_items`
  - variantes actuales -> `catalog_item_variants`

### Fase 7
- Retirar progresivamente menus y controladores viejos

## Riesgos principales
- El carrito hoy solo entiende `product` y `service`
- Las reservas actuales asumen logica de servicios
- `categories.type` hoy solo acepta `product` o `service`
- El website mezcla dos colecciones manualmente

## Decision tecnica recomendada
Si el proyecto debe escalar para muchos rubros, el destino correcto es `CatalogItem` universal.

Mientras tanto:
- `Catalogo` sera el padre visual del admin
- `Productos`, `Servicios`, `Categorias` y `Banners` seguiran como hijos
- el cambio profundo se hara por fases
