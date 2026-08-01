<section class="section section-darker" id="catalogo">
    <div class="section-header fade-up">
        <div class="section-tag">Todo en un solo lugar</div>
        <h2 class="section-title">NUESTRO <span>CATALOGO</span></h2>
        <div class="divider"></div>
        <p class="section-sub">Explora los subnegocios e items que tu empresa muestra en su catalogo publico</p>
    </div>

    <div class="filtros-container fade-up">
        <div class="filtros-tipo" role="tablist" aria-label="Filtrar catalogo por tipo">
            @foreach ($catalogFilters ?? [] as $filter)
                <button type="button" data-tipo="{{ $filter['value'] }}"
                    class="filtro-btn {{ $tipo === $filter['value'] ? 'active' : '' }}">
                    {{ $filter['label'] }}
                </button>
            @endforeach
        </div>

        <div class="buscador">
            <label for="search-catalogo" class="sr-only">Buscar en catalogo</label>
            <input type="text" id="search-catalogo" placeholder="Buscar por nombre, descripcion o categoria"
                value="{{ $search }}">
        </div>
    </div>

    <div id="catalogo-grid" class="catalogo-grid" aria-live="polite">
        @include('website.catalogo-items', ['items' => $catalogo])
    </div>

    <div id="catalogo-pagination" class="pagination-wrapper">
        @include('website.catalogo-pagination', ['pagination' => $pagination])
    </div>

    <div class="catalogo-modal-overlay" id="catalogoDetailOverlay" hidden>
        <div class="catalogo-modal" role="dialog" aria-modal="true" aria-labelledby="catalogoDetailTitle">
            <button type="button" class="catalogo-modal-close" id="catalogoDetailClose"
                aria-label="Cerrar detalle">&times;</button>
            <div class="catalogo-modal-media" id="catalogoDetailMedia"></div>
            <div class="catalogo-modal-body">
                <p class="catalogo-modal-category" id="catalogoDetailCategory"></p>
                <h3 class="catalogo-modal-title" id="catalogoDetailTitle"></h3>
                <p class="catalogo-modal-price" id="catalogoDetailPrice"></p>
                <p class="catalogo-modal-desc" id="catalogoDetailDescription"></p>
                <div class="catalogo-detail-vehicle" id="catalogoDetailVehicle" hidden>
                    <label for="detailVehicleSelect">Vehiculo o tipo de vehiculo</label>
                    <select id="detailVehicleSelect">
                        <option value="">Selecciona una opcion</option>
                    </select>
                    <p class="catalogo-detail-hint" id="detailVehicleHint">
                        El precio se calcula segÃºn el tipo del vehÃ­culo seleccionado.
                    </p>
                </div>
                <div class="catalogo-detail-service-actions" id="catalogoDetailServiceActions" hidden>
                    <button type="button" class="btn-reservar-main" id="detailServiceAddBtn">Agregar servicio</button>
                    <button type="button" class="btn-reservar-main" id="detailServiceReserveBtn">Reservar</button>
                </div>
                <div class="catalogo-detail-product-controls" id="catalogoDetailProductControls" hidden>
                    <div class="catalogo-detail-grid">
                        <label>Presentacion
                            <select id="detailVariantSelect"></select>
                        </label>
                        <label>Cantidad
                            <div class="catalog-stock-counter catalog-stock-counter-modal" id="detailQtyCounter">
                                <button type="button" class="catalog-stock-btn" id="detailQtyMinus"
                                    aria-label="Restar cantidad">âˆ’</button>
                                <span class="catalog-stock-value" id="detailQtyValue">1</span>
                                <button type="button" class="catalog-stock-btn" id="detailQtyPlus"
                                    aria-label="Sumar cantidad">+</button>
                            </div>
                            <input type="hidden" id="detailQtyInput" value="1">
                        </label>
                    </div>
                    <p class="catalogo-modal-price" id="detailVariantPrice">$0.00</p>
                    <button type="button" class="btn-reservar-main" id="detailAddToCartBtn">Agregar al carrito</button>
                </div>
            </div>
        </div>
    </div>

    <div class="catalogo-modal-overlay" id="catalogoReserveOverlay" hidden>
        <div class="catalogo-modal catalogo-modal-reserve" role="dialog" aria-modal="true"
            aria-labelledby="catalogoReserveTitle">
            <button type="button" class="catalogo-modal-close" id="catalogoReserveClose"
                aria-label="Cerrar reserva">&times;</button>
            <div class="catalogo-modal-body">
                <h3 class="catalogo-modal-title" id="catalogoReserveTitle">Reservar</h3>
                <p class="catalogo-modal-desc" id="catalogoReserveSubtitle"></p>

                <form id="catalogoReserveForm" class="catalogo-reserve-form" novalidate>
                    <input type="hidden" id="reserveItemId">
                    <input type="hidden" id="reserveItemType">
                    <input type="hidden" id="reserveItemName">
                    <input type="hidden" id="reserveVehicleId">
                    <input type="hidden" id="reserveVehicleTypeId">
                    <input type="hidden" id="reserveVehicleSpecificationId">
                    <input type="hidden" id="reserveVehicleLabel">

                    <label>Item seleccionado
                        <input type="text" id="reserveSelectedItem" readonly>
                    </label>
                    <label>Nombre
                        <input type="text" id="reserveName" required minlength="3" placeholder="Tu nombre completo">
                    </label>
                    <label>Telefono
                        <input type="tel" id="reservePhone" required pattern="[0-9+\s()\-]{10,15}"
                            placeholder="+593 98 123 4546">
                    </label>
                    <div class="catalogo-reserve-grid">
                        <label>Fecha
                            <input type="date" id="reserveDate" required>
                        </label>
                        <label>Hora
                            <select id="reserveTime" class="contact-hour-select" required>
                                <option value="">Selecciona hora</option>
                                @for ($h = 8; $h <= 17; $h++)
                                    @php($hour = str_pad((string) $h, 2, '0', STR_PAD_LEFT) . ':00')
                                    <option value="{{ $hour }}">{{ $hour }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                    <button type="submit" class="btn-reservar-main">Reservar por WhatsApp</button>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        (() => {
            let currentTipo = @json($tipo ?? 'todos');
            let currentSearch = @json($search ?? '');
            let currentPage = Number(@json($pagination['current_page'] ?? 1)) || 1;

            const gridContainer = document.getElementById('catalogo-grid');
            const paginationContainer = document.getElementById('catalogo-pagination');
            const detailOverlay = document.getElementById('catalogoDetailOverlay');
            const detailClose = document.getElementById('catalogoDetailClose');
            const detailMedia = document.getElementById('catalogoDetailMedia');
            const detailCategory = document.getElementById('catalogoDetailCategory');
            const detailTitle = document.getElementById('catalogoDetailTitle');
            const detailPrice = document.getElementById('catalogoDetailPrice');
            const detailDescription = document.getElementById('catalogoDetailDescription');
            const detailVehicle = document.getElementById('catalogoDetailVehicle');
            const detailVehicleSelect = document.getElementById('detailVehicleSelect');
            const detailVehicleHint = document.getElementById('detailVehicleHint');
            const detailServiceActions = document.getElementById('catalogoDetailServiceActions');
            const detailServiceAddBtn = document.getElementById('detailServiceAddBtn');
            const detailServiceReserveBtn = document.getElementById('detailServiceReserveBtn');
            const detailProductControls = document.getElementById('catalogoDetailProductControls');
            const detailVariantSelect = document.getElementById('detailVariantSelect');
            const detailQtyInput = document.getElementById('detailQtyInput');
            const detailQtyValue = document.getElementById('detailQtyValue');
            const detailQtyMinus = document.getElementById('detailQtyMinus');
            const detailQtyPlus = document.getElementById('detailQtyPlus');
            const detailVariantPrice = document.getElementById('detailVariantPrice');
            const detailAddToCartBtn = document.getElementById('detailAddToCartBtn');
            const reserveOverlay = document.getElementById('catalogoReserveOverlay');
            const reserveClose = document.getElementById('catalogoReserveClose');
            const reserveForm = document.getElementById('catalogoReserveForm');
            const reserveSubtitle = document.getElementById('catalogoReserveSubtitle');
            const searchInput = document.getElementById('search-catalogo');
            const filtroBtns = Array.from(document.querySelectorAll('.filtro-btn'));
            const searchRoute = @json(route('catalogo.buscar'));
            const cartIconSvg = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
            <path d="M1 1.75A.75.75 0 0 1 1.75 1h1.5a.75.75 0 0 1 .728.57l.249 1.01h11.55a.75.75 0 0 1 .734.904l-1.5 7A.75.75 0 0 1 14.278 11H5.03l.273 1.109A1.75 1.75 0 0 0 7 13.5h7.25a.75.75 0 0 1 0 1.5H7a3.25 3.25 0 0 1-3.154-2.464L2.47 2.5H1.75A.75.75 0 0 1 1 1.75ZM6.5 18a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM14 18a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z" />
        </svg>
    `;

            const whatsappPhone = @json(preg_replace('/\D+/', '', (string) ($empresa->telefono_contacto ?? '')));
            const reserveRoute = @json(route('reservas.catalogo'));
            const csrfToken = @json(csrf_token());
            const initialCatalogItems = @json(($catalogo ?? collect())->values());
            const customerVehicles = @json(($customerVehicles ?? collect())->values());
            const vehicleSpecifications = @json(($vehicleSpecifications ?? collect())->values());
            const catalogItemsByKey = new Map();
            let currentDetailItem = null;

            if (!gridContainer || !paginationContainer || !searchInput || !filtroBtns.length) {
                return;
            }

            function normalizePhone(phone) {
                const digits = String(phone || '').replace(/\D+/g, '');
                if (!digits) return '';
                if (digits.startsWith('593')) return digits;
                if (digits.length === 10 && digits.startsWith('0')) return `593${digits.slice(1)}`;
                return digits;
            }

            function isSunday(dateText) {
                const date = new Date(`${dateText}T00:00:00`);
                return date.getDay() === 0;
            }

            function updateActiveTipoButtons() {
                filtroBtns.forEach((btn) => {
                    const isActive = btn.dataset.tipo === currentTipo;
                    btn.classList.toggle('active', isActive);
                    btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
            }

            function escapeHtml(str) {
                return String(str || '').replace(/[&<>"']/g, (m) => {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    if (m === '"') return '&quot;';
                    return '&#039;';
                });
            }

            function itemKey(tipo, id) {
                return `${tipo}_${Number(id)}`;
            }

            function upsertCatalogItems(items) {
                if (!Array.isArray(items)) return;
                items.forEach((item) => {
                    if (!item || typeof item !== 'object') return;
                    catalogItemsByKey.set(itemKey(item.tipo, item.id), item);
                });
            }

            function getVariants(item) {
                const variants = Array.isArray(item?.variantes) ? item.variantes : [];
                return variants
                    .filter((v) => v && Number(v.price) >= 0)
                    .map((v) => ({
                        id: Number(v.id),
                        name: String(v.name || ''),
                        price: Number(v.price || 0),
                        stock: Number(v.stock || 0),
                        is_default: Boolean(v.is_default),
                    }));
            }

            function getDefaultVariant(item) {
                const variants = getVariants(item);
                if (!variants.length) return null;

                const availableVariants = variants.filter((variant) => !item?.inventariable || Number(variant.stock || 0) > 0);
                return availableVariants.find((variant) => variant.is_default) || availableVariants[0] || variants.find((variant) => variant.is_default) || variants[0];
            }

            function getItemAvailableStock(item) {
                if (!item?.inventariable) return 9999;
                const selected = getDefaultVariant(item);
                return Math.max(0, Number(selected?.stock ?? item.stock_disponible ?? 0));
            }

            function getSelectedVariantStock() {
                if (!currentDetailItem) return 0;
                const variants = getVariants(currentDetailItem);
                const selectedId = Number(detailVariantSelect?.value || 0);
                const selected = variants.find((v) => v.id === selectedId) || variants[0];
                if (selected) return Math.max(0, Number(selected.stock || 0));
                return getItemAvailableStock(currentDetailItem);
            }

            function setCounterValue(counter, value, max) {
                const nextValue = Math.max(1, Math.min(Number(value) || 1, Math.max(1, Number(max) || 1)));
                const valueEl = counter.querySelector('.js-stock-value');
                const minus = counter.querySelector('.js-stock-minus');
                const plus = counter.querySelector('.js-stock-plus');
                if (valueEl) valueEl.textContent = String(nextValue);
                if (minus) minus.disabled = nextValue <= 1;
                if (plus) plus.disabled = nextValue >= Number(max);
                counter.dataset.quantity = String(nextValue);
            }

            function renderPurchaseAction(item, tipo) {
                if (!item.comprable) return '';
                if (item.inventariable && item.agotado) return '<span class="catalog-stock-empty">Agotado</span>';

                if (!item.inventariable) {
                    const needsVehicle = Boolean(item.requiere_tipo_vehiculo);
                    return `<button type="button" class="btn-reservar-main ${needsVehicle ? 'js-open-priced-service' : 'js-add-simple-cart'}" data-id="${Number(item.id)}" data-tipo="${tipo}">${needsVehicle ? 'Elegir vehiculo' : 'Agregar servicio'}</button>`;
                }

                const variants = getVariants(item);
                const selectedVariant = getDefaultVariant(item);
                const stock = Math.max(0, Number(selectedVariant?.stock || 0));

                if (!variants.length || stock <= 0) return '<span class="catalog-stock-empty">Agotado</span>';

                const options = variants.map((variant) => {
                    const disabled = Number(variant.stock || 0) <= 0 ? 'disabled' : '';
                    const selected = selectedVariant && Number(selectedVariant.id) === Number(variant.id) ? 'selected' : '';
                    return `<option value="${variant.id}" data-stock="${Number(variant.stock || 0)}" data-price="${Number(variant.price || 0)}" ${selected} ${disabled}>${escapeHtml(buildVariantLabel(variant))}</option>`;
                }).join('');

                return `
            <select class="catalog-variant-select js-card-variant-select" aria-label="Seleccionar presentacion">
                ${options}
            </select>
            <div class="catalog-stock-counter" data-stock="${stock}" data-quantity="1">
                <button type="button" class="catalog-stock-btn js-stock-minus" aria-label="Restar cantidad" disabled>âˆ’</button>
                <span class="catalog-stock-value js-stock-value">1</span>
                <button type="button" class="catalog-stock-btn js-stock-plus" aria-label="Sumar cantidad" ${stock <= 1 ? 'disabled' : ''}>+</button>
            </div>
            <button type="button" class="btn-reservar-main js-add-counter-cart" data-id="${Number(item.id)}" data-tipo="${tipo}">Agregar</button>
        `;
            }

            function createCard(item) {
                const card = document.createElement('div');
                card.className = 'card item-catalogo';

                const tipo = 'catalog';
                const placeholder = 'ITM';
                const safeName = escapeHtml(item.nombre);
                const safeCategory = escapeHtml(item.categoria);
                const safeDesc = escapeHtml(item.descripcion || '');
                const safeImg = item.imagen ? `/storage/${item.imagen}` : '';
                const isPurchasable = Boolean(item.comprable);
                const isReservable = Boolean(item.reservable);
                const detailDataAttrs = `data-id="${Number(item.id)}" data-tipo="${tipo}" data-nombre="${safeName}" data-categoria="${safeCategory}" data-precio="${Number(item.precio)}" data-descripcion="${safeDesc}" data-inventariable="${item.inventariable ? '1' : '0'}" data-comprable="${item.comprable ? '1' : '0'}" data-reservable="${item.reservable ? '1' : '0'}"`;
                const imageHtml = item.imagen ?
                    `<button type="button" class="card-image-wrap js-open-detail" ${detailDataAttrs} data-imagen="${safeImg}" aria-label="Ver detalle de ${safeName}"><img src="${safeImg}" alt="${safeName}" class="card-image"></button>` :
                    `<button type="button" class="card-image-wrap js-open-detail" ${detailDataAttrs} data-imagen="" aria-label="Ver detalle de ${safeName}"><div class="card-placeholder">${placeholder}</div></button>`;

                card.innerHTML = `
            ${imageHtml}
            <div class="card-body">
                <div class="card-category">${escapeHtml(item.tipo_label || 'Catalogo')} Â· ${escapeHtml(item.categoria)}</div>
                <div class="card-top">
                    <div class="card-name-row">
                        <div class="card-name">${escapeHtml(item.nombre)}</div>
                    </div>
                </div>
                <div class="card-footer">
                    ${isPurchasable ? renderPurchaseAction(item, tipo) : ''}
                    ${isReservable ? `<button type="button" class="btn-reservar btn-reservar-main ${item.requiere_tipo_vehiculo ? 'js-open-priced-service' : 'js-open-reserve'}" data-id="${Number(item.id)}" data-tipo="${tipo}" data-nombre="${safeName}" data-precio="${Number(item.precio)}" title="Reservar" aria-label="Reservar">Reservar</button>` : ''}
                </div>
            </div>
        `;

                return card;
            }

            function renderPagination(pagination) {
                if (!pagination || Number(pagination.last_page) <= 1) {
                    return '';
                }

                let html = '';
                for (let i = 1; i <= Number(pagination.last_page); i += 1) {
                    const activeClass = i === Number(pagination.current_page) ? 'active' : '';
                    html +=
                    `<button type="button" data-page="${i}" class="paginacion-btn ${activeClass}">${i}</button>`;
                }

                return html;
            }

            function attachPaginationEvents() {
                document.querySelectorAll('.paginacion-btn').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        currentPage = Number(btn.getAttribute('data-page')) || 1;
                        loadCatalog();
                    });
                });
            }

            async function loadCatalog() {
                const params = new URLSearchParams({
                    tipo: currentTipo,
                    search: currentSearch,
                    page: String(currentPage),
                });

                try {
                    const response = await fetch(`${searchRoute}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();
                    const items = Array.isArray(data.items) ? data.items : [];
                    catalogItemsByKey.clear();
                    upsertCatalogItems(items);

                    gridContainer.innerHTML = '';

                    if (!items.length) {
                        gridContainer.innerHTML = '<p class="catalogo-empty">No se encontraron resultados.</p>';
                        paginationContainer.innerHTML = '';
                        return;
                    }

                    items.forEach((item) => {
                        gridContainer.appendChild(createCard(item));
                    });

                    paginationContainer.innerHTML = renderPagination(data.pagination);
                    attachPaginationEvents();
                } catch (error) {
                    console.error('Error al cargar catalogo:', error);
                    gridContainer.innerHTML =
                        '<p class="catalogo-empty">No se pudo cargar el catalogo. Intenta nuevamente.</p>';
                    paginationContainer.innerHTML = '';
                }
            }

            updateActiveTipoButtons();
            attachPaginationEvents();
            upsertCatalogItems(initialCatalogItems);

            function buildVariantLabel(variant) {
                return variant.name || 'Presentacion';
            }

            function updateDetailVariantPrice() {
                if (!currentDetailItem || !detailVariantSelect || !detailVariantPrice) return;
                const variants = getVariants(currentDetailItem);
                const selectedId = Number(detailVariantSelect.value || 0);
                const selected = variants.find((v) => v.id === selectedId) || variants[0];
                const price = selected ? selected.price : Number(currentDetailItem.precio || 0);
                const maxStock = getSelectedVariantStock();
                detailVariantPrice.textContent = `Precio: $${price.toFixed(2)}`;
                setDetailQuantity(1, maxStock);
                if (detailAddToCartBtn) {
                    detailAddToCartBtn.disabled = Boolean(currentDetailItem.inventariable) && maxStock <= 0;
                    detailAddToCartBtn.textContent = detailAddToCartBtn.disabled ? 'Agotado' : 'Agregar al carrito';
                }
            }
            function setDetailQuantity(value, max = null) {
                const maxStock = max === null ? getSelectedVariantStock() : Number(max || 0);
                const safeMax = Math.max(1, maxStock || 1);
                const nextValue = Math.max(1, Math.min(Number(value) || 1, safeMax));
                if (detailQtyInput) detailQtyInput.value = String(nextValue);
                if (detailQtyValue) detailQtyValue.textContent = String(nextValue);
                if (detailQtyMinus) detailQtyMinus.disabled = nextValue <= 1;
                if (detailQtyPlus) detailQtyPlus.disabled = nextValue >= safeMax || maxStock <= 0;
            }

            function getVehiclePrices(item) {
                return Array.isArray(item?.precios_vehiculo) ? item.precios_vehiculo : [];
            }

            function getSelectedVehicleContext() {
                const option = detailVehicleSelect?.selectedOptions?.[0];
                if (!option || !option.value) {
                    return { vehicleId: null, vehicleTypeId: null, vehicleSpecificationId: null };
                }

                return {
                    vehicleId: option.dataset.vehicleId ? Number(option.dataset.vehicleId) : null,
                    vehicleTypeId: option.dataset.vehicleTypeId ? Number(option.dataset.vehicleTypeId) : null,
                    vehicleSpecificationId: option.dataset.vehicleSpecificationId ? Number(option.dataset.vehicleSpecificationId) : null,
                    vehicleLabel: option.textContent?.trim() || null,
                };
            }

            function updateServiceVehiclePrice() {
                if (!currentDetailItem || !detailVehicleSelect) return;
                const option = detailVehicleSelect.selectedOptions[0];
                const price = option?.dataset.price !== undefined && option?.dataset.price !== ''
                    ? Number(option.dataset.price)
                    : Number(currentDetailItem.precio_base ?? currentDetailItem.precio ?? 0);

                detailPrice.textContent = option?.value
                    ? `Precio: ${price.toFixed(2)}`
                    : `Desde ${Number(currentDetailItem.precio || 0).toFixed(2)}`;
            }

            function renderServiceVehicleOptions(item) {
                if (!detailVehicle || !detailVehicleSelect) return;
                const prices = getVehiclePrices(item);
                const priceBySpecification = new Map(prices.map((price) => [Number(price.vehicle_specification_id), Number(price.price)]));
                const requiresVehicle = Boolean(item.requiere_tipo_vehiculo) && prices.length > 0;
                const basePrice = Number(item.precio_base ?? item.precio ?? 0);
                const priceForSpecification = (vehicleSpecificationId) => priceBySpecification.has(Number(vehicleSpecificationId))
                    ? priceBySpecification.get(Number(vehicleSpecificationId))
                    : basePrice;

                detailVehicle.hidden = !requiresVehicle;
                detailVehicleSelect.innerHTML = '<option value="">Selecciona una opción</option>';

                if (!requiresVehicle) return;

                const pricedSpecificationIds = new Set(prices.map((price) => Number(price.vehicle_specification_id)));
                const compatibleVehicles = customerVehicles.filter((vehicle) => pricedSpecificationIds.has(Number(vehicle.vehicle_specification_id)));
                if (compatibleVehicles.length) {
                    const group = document.createElement('optgroup');
                    group.label = 'Mis vehículos';
                    compatibleVehicles.forEach((vehicle) => {
                        const option = document.createElement('option');
                        option.value = `vehicle:${vehicle.id}`;
                        option.dataset.vehicleId = String(vehicle.id);
                        option.dataset.vehicleSpecificationId = String(vehicle.vehicle_specification_id);
                        option.dataset.vehicleTypeId = String(vehicle.vehicle_type_id || '');
                        option.dataset.price = String(priceForSpecification(vehicle.vehicle_specification_id));
                        option.textContent = `${vehicle.label} · ${vehicle.type_name}`;
                        group.appendChild(option);
                    });
                    detailVehicleSelect.appendChild(group);
                }

                const genericGroup = document.createElement('optgroup');
                genericGroup.label = compatibleVehicles.length ? 'Otra especificación' : 'Seleccionar especificación';
                const pricedVehicleSpecifications = prices
                    .filter((price) => Number(price.vehicle_specification_id) > 0)
                    .map((price) => ({
                        id: Number(price.vehicle_specification_id),
                        typeId: Number(price.vehicle_type_id),
                        name: price.vehicle_specification_name || price.vehicle_type_name || 'Especificación de vehículo',
                    }));

                pricedVehicleSpecifications.forEach((vehicleSpecification) => {
                    const option = document.createElement('option');
                    option.value = `spec:${vehicleSpecification.id}`;
                    option.dataset.vehicleSpecificationId = String(vehicleSpecification.id);
                    option.dataset.vehicleTypeId = String(vehicleSpecification.typeId || '');
                    option.dataset.price = String(priceForSpecification(vehicleSpecification.id));
                    option.textContent = `${vehicleSpecification.name} · ${priceForSpecification(vehicleSpecification.id).toFixed(2)}`;
                    genericGroup.appendChild(option);
                });
                detailVehicleSelect.appendChild(genericGroup);

                if (detailVehicleHint) {
                    detailVehicleHint.textContent = compatibleVehicles.length
                        ? 'Selecciona uno de tus vehículos o usa una especificación temporal.'
                        : 'Selecciona temporalmente la especificación del vehículo para calcular el precio.';
                }
            }

            function openDetailModal(data) {
                if (!detailOverlay || !detailMedia || !detailCategory || !detailTitle || !detailPrice || !
                    detailDescription) return;
                const fullItem = catalogItemsByKey.get(itemKey(data.tipo, data.id));
                currentDetailItem = fullItem ? {
                    ...data,
                    ...fullItem
                } : data;

                detailMedia.innerHTML = data.imagen ?
                    `<img src="${data.imagen}" alt="${data.nombre}" class="catalogo-modal-image">` :
                    `<div class="catalogo-modal-placeholder">ITM</div>`;
                detailCategory.textContent =
                    `${currentDetailItem.tipo_label || 'Catalogo'} Â· ${currentDetailItem.categoria || ''}`;
                detailTitle.textContent = currentDetailItem.nombre || '';
                const isProductDetail = currentDetailItem.tipo === 'catalog' && currentDetailItem.comprable && currentDetailItem.inventariable;
                const isServiceDetail = currentDetailItem.tipo === 'catalog' && !currentDetailItem.inventariable;
                detailPrice.textContent = isProductDetail ? '' : `Desde $${Number(currentDetailItem.precio || 0).toFixed(2)}`;
                detailDescription.textContent = currentDetailItem.descripcion || (isProductDetail ? '' : 'Sin descripcion adicional.');
                detailDescription.hidden = isProductDetail && !Boolean(currentDetailItem.descripcion);
                renderServiceVehicleOptions(currentDetailItem);

                if (detailServiceActions) {
                    detailServiceActions.hidden = !isServiceDetail;
                }
                if (detailServiceAddBtn) {
                    detailServiceAddBtn.hidden = !(isServiceDetail && currentDetailItem.comprable);
                }
                if (detailServiceReserveBtn) {
                    detailServiceReserveBtn.hidden = !(isServiceDetail && currentDetailItem.reservable);
                }

                if (isProductDetail && detailProductControls && detailVariantSelect && detailQtyInput) {
                    const variants = getVariants(currentDetailItem);
                    detailVariantSelect.innerHTML = '';
                    if (variants.length) {
                        variants.forEach((variant) => {
                            const option = document.createElement('option');
                            option.value = String(variant.id);
                            option.textContent = buildVariantLabel(variant);
                            option.disabled = Boolean(currentDetailItem.inventariable) && Number(variant
                                .stock || 0) <= 0;
                            detailVariantSelect.appendChild(option);
                        });
                        const availableVariants = variants.filter((variant) => !currentDetailItem.inventariable ||
                            Number(variant.stock || 0) > 0);
                        const defaultVariant = availableVariants.find((v) => v.is_default) || availableVariants[0] ||
                            variants[0];
                        if (defaultVariant) detailVariantSelect.value = String(defaultVariant.id);
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Presentacion base';
                        detailVariantSelect.appendChild(option);
                    }
                    setDetailQuantity(1);
                    detailProductControls.hidden = false;
                    updateDetailVariantPrice();
                } else if (detailProductControls) {
                    detailVariantSelect.innerHTML = '';
                    if (detailVariantPrice) detailVariantPrice.textContent = '';
                    if (detailAddToCartBtn) {
                        detailAddToCartBtn.disabled = false;
                        detailAddToCartBtn.textContent = 'Agregar al carrito';
                    }
                    detailProductControls.hidden = true;
                }

                detailOverlay.hidden = false;
                document.body.style.overflow = 'hidden';
            }

            function closeDetailModal() {
                if (!detailOverlay) return;
                detailOverlay.hidden = true;
                document.body.style.overflow = '';
                currentDetailItem = null;
            }

            function openReserveModal(data) {
                if (!reserveOverlay || !reserveForm || !reserveSubtitle) return;
                reserveForm.reset();
                const reserveItemId = document.getElementById('reserveItemId');
                const reserveItemType = document.getElementById('reserveItemType');
                const reserveItemName = document.getElementById('reserveItemName');
                const reserveSelectedItem = document.getElementById('reserveSelectedItem');
                const dateInput = document.getElementById('reserveDate');
                if (!reserveItemId || !reserveItemType || !reserveItemName || !reserveSelectedItem || !dateInput)
            return;

                reserveItemId.value = data.id || '';
                reserveItemType.value = data.tipo || '';
                reserveItemName.value = data.nombre || '';
                document.getElementById('reserveVehicleId').value = data.vehicleId || '';
                document.getElementById('reserveVehicleTypeId').value = data.vehicleTypeId || '';
                document.getElementById('reserveVehicleSpecificationId').value = data.vehicleSpecificationId || '';
                document.getElementById('reserveVehicleLabel').value = data.vehicleLabel || '';
                reserveSelectedItem.value = data.nombre || '';
                reserveSubtitle.textContent = `Completa la reserva para: ${data.nombre || ''}`;
                const now = new Date();
                dateInput.min = now.toISOString().split('T')[0];
                reserveOverlay.hidden = false;
                document.body.style.overflow = 'hidden';
            }

            function closeReserveModal() {
                if (!reserveOverlay) return;
                reserveOverlay.hidden = true;
                document.body.style.overflow = '';
            }

            function collectDataset(el) {
                return {
                    id: el.dataset.id || '',
                    tipo: el.dataset.tipo || '',
                    nombre: el.dataset.nombre || '',
                    categoria: el.dataset.categoria || '',
                    precio: el.dataset.precio || 0,
                    descripcion: el.dataset.descripcion || '',
                    tipo_descripcion: el.dataset.tipoDescripcion || '',
                    inventariable: el.dataset.inventariable === '1',
                    comprable: el.dataset.comprable === '1',
                    reservable: el.dataset.reservable === '1',
                    requiere_tipo_vehiculo: el.dataset.requiereTipoVehiculo === '1',
                    imagen: el.dataset.imagen || '',
                };
            }

            gridContainer.addEventListener('click', (event) => {
                const detailTarget = event.target.closest('.js-open-detail');
                if (detailTarget) {
                    openDetailModal(collectDataset(detailTarget));
                    return;
                }
                const reserveTarget = event.target.closest('.js-open-reserve');
                if (reserveTarget) {
                    openReserveModal(collectDataset(reserveTarget));
                    return;
                }
                const pricedServiceTarget = event.target.closest('.js-open-priced-service');
                if (pricedServiceTarget) {
                    const item = catalogItemsByKey.get(itemKey(
                        pricedServiceTarget.dataset.tipo || 'catalog',
                        pricedServiceTarget.dataset.id
                    ));
                    if (item) {
                        openDetailModal({
                            ...item,
                            imagen: item.imagen ? `/storage/${item.imagen}` : '',
                        });
                    }
                    return;
                }

                const variantSelect = event.target.closest('.js-card-variant-select');
                if (variantSelect) {
                    const card = variantSelect.closest('.item-catalogo');
                    const option = variantSelect.selectedOptions?.[0];
                    const stock = Math.max(0, Number(option?.dataset.stock || 0));
                    const counter = card?.querySelector('.catalog-stock-counter');
                    if (counter) {
                        counter.dataset.stock = String(stock);
                        setCounterValue(counter, 1, stock);
                    }
                    return;
                }

                const stockButton = event.target.closest('.catalog-stock-btn');
                if (stockButton) {
                    const counter = stockButton.closest('.catalog-stock-counter');
                    if (!counter) return;
                    const max = Number(counter.dataset.stock || 1);
                    const current = Number(counter.dataset.quantity || counter.querySelector('.js-stock-value')
                        ?.textContent || 1);
                    setCounterValue(counter, current + (stockButton.classList.contains('js-stock-plus') ? 1 : -
                        1), max);
                    return;
                }

                const addCounterTarget = event.target.closest('.js-add-counter-cart');
                if (addCounterTarget) {
                    const card = addCounterTarget.closest('.item-catalogo');
                    const counter = card?.querySelector('.catalog-stock-counter');
                    const variantSelect = card?.querySelector('.js-card-variant-select');
                    const variantId = Number(variantSelect?.value || 0) || null;
                    const quantity = Number(counter?.dataset.quantity || counter?.querySelector(
                        '.js-stock-value')?.textContent || 1);
                    addToCart(Number(addCounterTarget.dataset.id), addCounterTarget.dataset.tipo || 'catalog',
                        quantity, variantId);
                    return;
                }

                const addSimpleTarget = event.target.closest('.js-add-simple-cart');
                if (addSimpleTarget) {
                    addToCart(Number(addSimpleTarget.dataset.id), addSimpleTarget.dataset.tipo || 'catalog', 1);
                }
            });

            detailClose?.addEventListener('click', closeDetailModal);
            detailVariantSelect?.addEventListener('change', updateDetailVariantPrice);
            detailVehicleSelect?.addEventListener('change', updateServiceVehiclePrice);
            detailQtyMinus?.addEventListener('click', () => setDetailQuantity(Number(detailQtyInput?.value || 1) - 1));
            detailQtyPlus?.addEventListener('click', () => setDetailQuantity(Number(detailQtyInput?.value || 1) + 1));
            detailAddToCartBtn?.addEventListener('click', () => {
                if (!currentDetailItem || currentDetailItem.tipo !== 'catalog') return;
                const qty = Math.max(1, Number(detailQtyInput?.value || 1));
                const variantId = Number(detailVariantSelect?.value || 0) || null;
                addToCart(Number(currentDetailItem.id), currentDetailItem.tipo, qty, variantId);
                closeDetailModal();
            });
            detailServiceAddBtn?.addEventListener('click', () => {
                if (!currentDetailItem || currentDetailItem.tipo !== 'catalog') return;
                const vehicleContext = getSelectedVehicleContext();
                if (currentDetailItem.requiere_tipo_vehiculo && !vehicleContext.vehicleId && !vehicleContext.vehicleSpecificationId) {
                    return window.websiteNotify?.('error', 'Selecciona tu vehiculo o un tipo de vehiculo.');
                }
                addToCart(Number(currentDetailItem.id), currentDetailItem.tipo, 1, null, vehicleContext.vehicleId, vehicleContext.vehicleTypeId, vehicleContext.vehicleSpecificationId);
                closeDetailModal();
            });
            detailServiceReserveBtn?.addEventListener('click', () => {
                if (!currentDetailItem || currentDetailItem.tipo !== 'catalog') return;
                const itemForReserve = {
                    id: currentDetailItem.id,
                    tipo: currentDetailItem.tipo,
                    nombre: currentDetailItem.nombre,
                    ...getSelectedVehicleContext(),
                };
                if (currentDetailItem.requiere_tipo_vehiculo && !itemForReserve.vehicleId && !itemForReserve.vehicleSpecificationId) {
                    return window.websiteNotify?.('error', 'Selecciona tu vehiculo o un tipo de vehiculo.');
                }
                closeDetailModal();
                openReserveModal(itemForReserve);
            });
            reserveClose?.addEventListener('click', closeReserveModal);
            detailOverlay?.addEventListener('click', (e) => {
                if (e.target === detailOverlay) closeDetailModal();
            });
            reserveOverlay?.addEventListener('click', (e) => {
                if (e.target === reserveOverlay) closeReserveModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeDetailModal();
                    closeReserveModal();
                }
            });

            reserveForm?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const reserveName = document.getElementById('reserveName');
                const reservePhone = document.getElementById('reservePhone');
                const reserveDate = document.getElementById('reserveDate');
                const reserveTime = document.getElementById('reserveTime');
                const reserveItemName = document.getElementById('reserveItemName');
                const reserveItemId = document.getElementById('reserveItemId');
                const reserveItemType = document.getElementById('reserveItemType');
                const reserveVehicleId = document.getElementById('reserveVehicleId');
                const reserveVehicleTypeId = document.getElementById('reserveVehicleTypeId');
                const reserveVehicleSpecificationId = document.getElementById('reserveVehicleSpecificationId');
                const reserveVehicleLabel = document.getElementById('reserveVehicleLabel');
                if (!reserveName || !reservePhone || !reserveDate || !reserveTime || !reserveItemName || !
                    reserveItemId || !reserveItemType || !reserveVehicleId || !reserveVehicleTypeId ||
                    !reserveVehicleSpecificationId ||
                    !reserveVehicleLabel) return;

                const name = reserveName.value.trim();
                const phone = reservePhone.value.trim();
                const date = reserveDate.value;
                const time = reserveTime.value;
                const itemName = reserveItemName.value;
                const itemId = reserveItemId.value;
                const itemType = reserveItemType.value;

                if (!name || name.length < 3) {
                    return window.websiteNotify?.('error', 'Ingresa un nombre valido.');
                }
                if (!phone) {
                    return window.websiteNotify?.('error', 'Ingresa un telefono.');
                }
                if (!date || !time) {
                    return window.websiteNotify?.('error', 'Completa fecha y hora.');
                }
                if (isSunday(date)) {
                    return window.websiteNotify?.('error', 'No se agendan reservas en domingo.');
                }

                if (!itemId || !itemType) {
                    return window.websiteNotify?.('error', 'No se encontro el item a reservar.');
                }

                try {
                    const response = await fetch(reserveRoute, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            item_id: Number(itemId),
                            item_type: itemType,
                            vehicle_id: reserveVehicleId.value ? Number(reserveVehicleId.value) : null,
                            vehicle_type_id: reserveVehicleTypeId.value ? Number(reserveVehicleTypeId.value) : null,
                            vehicle_specification_id: reserveVehicleSpecificationId.value ? Number(reserveVehicleSpecificationId.value) : null,
                        }),
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(data.message || 'No se pudo guardar la reserva.');
                    }
                } catch (error) {
                    window.websiteNotify?.('error', error.message || 'No se pudo guardar la reserva.');
                    return;
                }

                const waPhone = normalizePhone(whatsappPhone || phone);
                const message = encodeURIComponent(
                    `NUEVA RESERVA\n\n` +
                    `Nombre: ${name}\n` +
                    `Telefono: ${phone}\n` +
                    `Item: ${itemName}\n` +
                    `${reserveVehicleLabel.value ? `Vehiculo: ${reserveVehicleLabel.value}\n` : ''}` +
                    `Fecha: ${date}\n` +
                    `Hora: ${time}`
                );
                const waUrl = waPhone ? `https://wa.me/${waPhone}?text=${message}` : '';
                if (!waUrl) {
                    window.websiteNotify?.('error', 'No hay numero de WhatsApp configurado.');
                    return;
                }
                const popup = window.open('about:blank', '_blank');
                if (popup) popup.location.href = waUrl;
                else window.location.href = waUrl;
                closeReserveModal();
                window.websiteNotify?.('success', 'Reserva guardada y WhatsApp abierto para continuar.');
            });

            filtroBtns.forEach((btn) => {
                btn.addEventListener('click', () => {
                    currentTipo = btn.dataset.tipo || 'todos';
                    currentPage = 1;
                    updateActiveTipoButtons();
                    loadCatalog();
                });
            });

            let debounceTimeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    currentSearch = searchInput.value.trim();
                    currentPage = 1;
                    loadCatalog();
                }, 320);
            });

            const reserveDateInput = document.getElementById('reserveDate');
            reserveDateInput?.addEventListener('change', () => {
                if (reserveDateInput.value && isSunday(reserveDateInput.value)) {
                    reserveDateInput.value = '';
                    window.websiteNotify?.('error', 'No se agendan reservas en domingo.');
                }
            });
        })();
    </script>
@endpush
