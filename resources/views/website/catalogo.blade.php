<section class="section section-darker" id="catalogo">
    <div class="section-header fade-up">
        <div class="section-tag">Todo en un solo lugar</div>
        <h2 class="section-title">NUESTRO <span>CATALOGO</span></h2>
        <div class="divider"></div>
        <p class="section-sub">Productos y servicios para el cuidado de tu vehiculo</p>
    </div>

    <div class="filtros-container fade-up">
        <div class="filtros-tipo" role="tablist" aria-label="Filtrar catalogo por tipo">
            <button type="button" data-tipo="todos" class="filtro-btn {{ $tipo === 'todos' ? 'active' : '' }}">Todos</button>
            <button type="button" data-tipo="productos" class="filtro-btn {{ $tipo === 'productos' ? 'active' : '' }}">
                <x-heroicon-o-cube class="w-4 h-4" />
                Productos
            </button>
            <button type="button" data-tipo="servicios" class="filtro-btn {{ $tipo === 'servicios' ? 'active' : '' }}">
                <x-heroicon-o-wrench class="w-4 h-4" />
                Servicios
            </button>
        </div>

        <div class="buscador">
            <label for="search-catalogo" class="sr-only">Buscar en catalogo</label>
            <input
                type="text"
                id="search-catalogo"
                placeholder="Buscar por nombre, descripcion o categoria"
                value="{{ $search }}"
            >
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
            <button type="button" class="catalogo-modal-close" id="catalogoDetailClose" aria-label="Cerrar detalle">&times;</button>
            <div class="catalogo-modal-media" id="catalogoDetailMedia"></div>
            <div class="catalogo-modal-body">
                <p class="catalogo-modal-category" id="catalogoDetailCategory"></p>
                <h3 class="catalogo-modal-title" id="catalogoDetailTitle"></h3>
                <p class="catalogo-modal-price" id="catalogoDetailPrice"></p>
                <p class="catalogo-modal-desc" id="catalogoDetailDescription"></p>
            </div>
        </div>
    </div>

    <div class="catalogo-modal-overlay" id="catalogoReserveOverlay" hidden>
        <div class="catalogo-modal catalogo-modal-reserve" role="dialog" aria-modal="true" aria-labelledby="catalogoReserveTitle">
            <button type="button" class="catalogo-modal-close" id="catalogoReserveClose" aria-label="Cerrar reserva">&times;</button>
            <div class="catalogo-modal-body">
                <h3 class="catalogo-modal-title" id="catalogoReserveTitle">Reservar</h3>
                <p class="catalogo-modal-desc" id="catalogoReserveSubtitle"></p>

                <form id="catalogoReserveForm" class="catalogo-reserve-form" novalidate>
                    <input type="hidden" id="reserveItemId">
                    <input type="hidden" id="reserveItemType">
                    <input type="hidden" id="reserveItemName">

                    <label>Producto o servicio seleccionado
                        <input type="text" id="reserveSelectedItem" readonly>
                    </label>
                    <label>Nombre
                        <input type="text" id="reserveName" required minlength="3" placeholder="Tu nombre completo">
                    </label>
                    <label>Telefono
                        <input type="tel" id="reservePhone" required pattern="[0-9+\s()\-]{10,15}" placeholder="+593 98 123 4546">
                    </label>
                    <div class="catalogo-reserve-grid">
                        <label>Fecha
                            <input type="date" id="reserveDate" required>
                        </label>
                        <label>Hora
                            <select id="reserveTime" class="contact-hour-select" required>
                                <option value="">Selecciona hora</option>
                                @for($h = 8; $h <= 17; $h++)
                                    @php($hour = str_pad((string)$h, 2, '0', STR_PAD_LEFT).':00')
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
    let currentPage = Number(@json(($pagination['current_page'] ?? 1))) || 1;

    const gridContainer = document.getElementById('catalogo-grid');
    const paginationContainer = document.getElementById('catalogo-pagination');
    const detailOverlay = document.getElementById('catalogoDetailOverlay');
    const detailClose = document.getElementById('catalogoDetailClose');
    const detailMedia = document.getElementById('catalogoDetailMedia');
    const detailCategory = document.getElementById('catalogoDetailCategory');
    const detailTitle = document.getElementById('catalogoDetailTitle');
    const detailPrice = document.getElementById('catalogoDetailPrice');
    const detailDescription = document.getElementById('catalogoDetailDescription');
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

    function createCard(item) {
        const card = document.createElement('div');
        card.className = 'card item-catalogo';

        const tipo = item.tipo === 'product' ? 'product' : 'service';
        const placeholder = tipo === 'product' ? 'PRD' : 'SRV';
        const safeName = escapeHtml(item.nombre);
        const safeCategory = escapeHtml(item.categoria);
        const safeDesc = escapeHtml(item.descripcion || '');
        const safeImg = item.imagen ? `/storage/${item.imagen}` : '';
        const imageHtml = item.imagen
            ? `<button type="button" class="card-image-wrap js-open-detail" data-id="${Number(item.id)}" data-tipo="${tipo}" data-nombre="${safeName}" data-categoria="${safeCategory}" data-precio="${Number(item.precio)}" data-descripcion="${safeDesc}" data-imagen="${safeImg}" aria-label="Ver detalle de ${safeName}"><img src="${safeImg}" alt="${safeName}" class="card-image"></button>`
            : `<button type="button" class="card-image-wrap js-open-detail" data-id="${Number(item.id)}" data-tipo="${tipo}" data-nombre="${safeName}" data-categoria="${safeCategory}" data-precio="${Number(item.precio)}" data-descripcion="${safeDesc}" data-imagen="" aria-label="Ver detalle de ${safeName}"><div class="card-placeholder">${placeholder}</div></button>`;

        const descripcion = item.descripcion ? String(item.descripcion) : '';
        const shortDescription = descripcion.length > 80 ? `${descripcion.substring(0, 80)}...` : descripcion;

        card.innerHTML = `
            ${imageHtml}
            <div class="card-body">
                <div class="card-category">${escapeHtml(item.categoria)}</div>
                <div class="card-top">
                    <div class="card-name-row">
                        <div class="card-name">${escapeHtml(item.nombre)}</div>
                        <button class="card-name-cart-btn" onclick="addToCart(${Number(item.id)}, '${tipo}')" title="Agregar al carrito" aria-label="Agregar al carrito">
                            ${cartIconSvg}
                        </button>
                    </div>
                </div>
                <p class="card-desc">${escapeHtml(shortDescription)}</p>
                <div class="card-footer">
                    <button type="button" class="btn-reservar btn-reservar-main js-open-reserve" data-id="${Number(item.id)}" data-tipo="${tipo}" data-nombre="${safeName}" data-precio="${Number(item.precio)}" title="Reservar" aria-label="Reservar">Reservar</button>
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
            html += `<button type="button" data-page="${i}" class="paginacion-btn ${activeClass}">${i}</button>`;
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
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            const items = Array.isArray(data.items) ? data.items : [];

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
            gridContainer.innerHTML = '<p class="catalogo-empty">No se pudo cargar el catalogo. Intenta nuevamente.</p>';
            paginationContainer.innerHTML = '';
        }
    }

    updateActiveTipoButtons();
    attachPaginationEvents();

    function openDetailModal(data) {
        if (!detailOverlay || !detailMedia || !detailCategory || !detailTitle || !detailPrice || !detailDescription) return;
        detailMedia.innerHTML = data.imagen
            ? `<img src="${data.imagen}" alt="${data.nombre}" class="catalogo-modal-image">`
            : `<div class="catalogo-modal-placeholder">${data.tipo === 'product' ? 'PRD' : 'SRV'}</div>`;
        detailCategory.textContent = data.categoria || '';
        detailTitle.textContent = data.nombre || '';
        detailPrice.textContent = `Desde $${Number(data.precio || 0).toFixed(2)}`;
        detailDescription.textContent = data.descripcion || 'Sin descripcion adicional.';
        detailOverlay.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        if (!detailOverlay) return;
        detailOverlay.hidden = true;
        document.body.style.overflow = '';
    }

    function openReserveModal(data) {
        if (!reserveOverlay || !reserveForm || !reserveSubtitle) return;
        reserveForm.reset();
        const reserveItemId = document.getElementById('reserveItemId');
        const reserveItemType = document.getElementById('reserveItemType');
        const reserveItemName = document.getElementById('reserveItemName');
        const reserveSelectedItem = document.getElementById('reserveSelectedItem');
        const dateInput = document.getElementById('reserveDate');
        if (!reserveItemId || !reserveItemType || !reserveItemName || !reserveSelectedItem || !dateInput) return;

        reserveItemId.value = data.id || '';
        reserveItemType.value = data.tipo || '';
        reserveItemName.value = data.nombre || '';
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
        }
    });

    detailClose?.addEventListener('click', closeDetailModal);
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
        if (!reserveName || !reservePhone || !reserveDate || !reserveTime || !reserveItemName || !reserveItemId || !reserveItemType) return;

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
