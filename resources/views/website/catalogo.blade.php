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
</section>

@push('scripts')
<script>
(() => {
    let currentTipo = @json($tipo ?? 'todos');
    let currentSearch = @json($search ?? '');
    let currentPage = Number(@json(($pagination['current_page'] ?? 1))) || 1;

    const gridContainer = document.getElementById('catalogo-grid');
    const paginationContainer = document.getElementById('catalogo-pagination');
    const searchInput = document.getElementById('search-catalogo');
    const filtroBtns = Array.from(document.querySelectorAll('.filtro-btn'));
    const searchRoute = @json(route('catalogo.buscar'));

    if (!gridContainer || !paginationContainer || !searchInput || !filtroBtns.length) {
        return;
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
        const unidad = tipo === 'product' ? 'unidad' : 'servicio';
        const placeholder = tipo === 'product' ? 'PRD' : 'SRV';

        const imageHtml = item.imagen
            ? `<img src="/storage/${item.imagen}" alt="${escapeHtml(item.nombre)}" class="card-image">`
            : `<div class="card-placeholder">${placeholder}</div>`;

        const descripcion = item.descripcion ? String(item.descripcion) : '';
        const shortDescription = descripcion.length > 80 ? `${descripcion.substring(0, 80)}...` : descripcion;

        card.innerHTML = `
            ${imageHtml}
            <div class="card-body">
                <div class="card-category">${escapeHtml(item.categoria)}</div>
                <div class="card-name">${escapeHtml(item.nombre)}</div>
                <p class="card-desc">${escapeHtml(shortDescription)}</p>
                <div class="card-footer">
                    <div class="card-price">$${Number(item.precio).toFixed(2)}<span>/ ${unidad}</span></div>
                    <button class="btn-card" onclick="addToCart(${Number(item.id)}, '${tipo}')">Agregar</button>
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
})();
</script>
@endpush
