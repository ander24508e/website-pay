<section class="section section-darker" id="catalogo">
    <div class="section-header fade-up">
        <div class="section-tag">Todo en un solo lugar</div>
        <h2 class="section-title">NUESTRO <span>CATÁLOGO</span></h2>
        <div class="divider"></div>
        <p class="section-sub">Productos y servicios para el cuidado de tu vehículo</p>
    </div>

    {{-- Filtros y búsqueda --}}
    <div class="filtros-container fade-up" style="max-width: 1000px; margin: 0 auto 2rem auto; display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
        <div class="filtros-tipo" style="display: flex; gap: 0.5rem; background: rgba(0,0,0,0.3); padding: 0.3rem; border-radius: 50px;">
            <button data-tipo="todos" class="filtro-btn {{ $tipo == 'todos' ? 'active' : '' }}" style="background: {{ $tipo == 'todos' ? 'var(--red)' : 'transparent' }}; border: none; padding: 0.5rem 1.2rem; border-radius: 40px; color: white; font-weight: 600; cursor: pointer; transition: all 0.2s;">Todos</button>
            <button data-tipo="productos" class="filtro-btn {{ $tipo == 'productos' ? 'active' : '' }}" style="background: {{ $tipo == 'productos' ? 'var(--red)' : 'transparent' }}; border: none; padding: 0.5rem 1.2rem; border-radius: 40px; color: rgba(255,255,255,0.7); font-weight: 600; cursor: pointer; transition: all 0.2s;">📦 Productos</button>
            <button data-tipo="servicios" class="filtro-btn {{ $tipo == 'servicios' ? 'active' : '' }}" style="background: {{ $tipo == 'servicios' ? 'var(--red)' : 'transparent' }}; border: none; padding: 0.5rem 1.2rem; border-radius: 40px; color: rgba(255,255,255,0.7); font-weight: 600; cursor: pointer; transition: all 0.2s;">🛠️ Servicios</button>
        </div>
        <div class="buscador" style="flex: 1; max-width: 300px;">
            <input type="text" id="search-catalogo" placeholder="🔍 Buscar..." value="{{ $search }}" 
                   style="width: 100%; padding: 0.5rem 1rem; border-radius: 50px; border: none; background: rgba(255,255,255,0.1); color: white; font-size: 0.85rem; outline: none;">
        </div>
    </div>

    {{-- Grid de resultados (se llena con AJAX, pero tiene carga inicial) --}}
    <div id="catalogo-grid" class="catalogo-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; max-width: 1200px; margin: 0 auto;">
        @include('website.catalogo-items', ['items' => $catalogo])
    </div>

    {{-- Paginación --}}
    <div id="catalogo-pagination" class="pagination-wrapper" style="display: flex; justify-content: center; margin-top: 2rem; gap: 0.5rem;">
        @include('website.catalogo-pagination', ['pagination' => $pagination])
    </div>
</section>

@push('scripts')
<script>
    let currentTipo = '{{ $tipo }}';
    let currentSearch = '{{ $search }}';
    let currentPage = 1;

    const gridContainer = document.getElementById('catalogo-grid');
    const paginationContainer = document.getElementById('catalogo-pagination');
    const searchInput = document.getElementById('search-catalogo');
    const filtroBtns = document.querySelectorAll('.filtro-btn');

    function loadCatalog() {
        const url = `{{ route('catalogo.buscar') }}?tipo=${currentTipo}&search=${encodeURIComponent(currentSearch)}&page=${currentPage}`;
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            // Actualizar grid
            gridContainer.innerHTML = '';
            if (data.items.length === 0) {
                gridContainer.innerHTML = '<p style="text-align:center; width:100%; color:var(--muted);">No se encontraron resultados.</p>';
                paginationContainer.innerHTML = '';
                return;
            }
            data.items.forEach(item => {
                const card = createCard(item);
                gridContainer.appendChild(card);
            });
            // Actualizar paginación
            paginationContainer.innerHTML = renderPagination(data.pagination);
            attachPaginationEvents();
        })
        .catch(err => console.error('Error al cargar catálogo:', err));
    }

    function createCard(item) {
        const div = document.createElement('div');
        div.className = 'card item-catalogo';

        let imageHtml = '';
        if (item.imagen) {
            imageHtml = `<img src="/storage/${item.imagen}" alt="${item.nombre}" class="card-image">`;
        } else {
            imageHtml = `<div class="card-placeholder">${item.tipo === 'producto' ? '🛢️' : '🛠️'}</div>`;
        }

        const descripcionCorta = item.descripcion ? (item.descripcion.length > 80 ? item.descripcion.substring(0, 80) + '...' : item.descripcion) : '';

        div.innerHTML = `
            ${imageHtml}
            <div class="card-body">
                <div class="card-category">${escapeHtml(item.categoria)}</div>
                <div class="card-name">${escapeHtml(item.nombre)}</div>
                <p class="card-desc">${escapeHtml(descripcionCorta)}</p>
                <div class="card-footer">
                    <div class="card-price">$${parseFloat(item.precio).toFixed(2)}<span>/ ${item.tipo === 'producto' ? 'unidad' : 'servicio'}</span></div>
                    <button class="btn-card" onclick="addToCart(${item.id}, '${item.tipo}')">Agregar</button>
                </div>
            </div>
        `;
        return div;
    }

    function renderPagination(pagination) {
        if (pagination.last_page <= 1) return '';
        let html = '';
        for (let i = 1; i <= pagination.last_page; i++) {
            const activeClass = i === pagination.current_page ? 'active' : '';
            html += `<button data-page="${i}" class="paginacion-btn ${activeClass}" style="background: ${i === pagination.current_page ? 'var(--red)' : 'rgba(255,255,255,0.1)'}; border: none; color: white; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;">${i}</button>`;
        }
        return html;
    }

    function attachPaginationEvents() {
        document.querySelectorAll('.paginacion-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                currentPage = parseInt(btn.getAttribute('data-page'));
                loadCatalog();
            });
        });
    }

    // Escuchar cambios en los filtros
    filtroBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Actualizar estilos de los botones
            filtroBtns.forEach(b => {
                b.classList.remove('active');
                b.style.background = 'transparent';
                b.style.color = 'rgba(255,255,255,0.7)';
            });
            btn.classList.add('active');
            btn.style.background = 'var(--red)';
            btn.style.color = 'white';
            // Actualizar variable y recargar
            currentTipo = btn.getAttribute('data-tipo');
            currentPage = 1;
            loadCatalog();
        });
    });

    // Búsqueda con debounce
    let debounceTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            currentSearch = searchInput.value;
            currentPage = 1;
            loadCatalog();
        }, 400);
    });

    // Función para escapar HTML (seguridad)
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
</script>
@endpush