@if($pagination['last_page'] > 1)
    @for($i = 1; $i <= $pagination['last_page']; $i++)
        <button data-page="{{ $i }}" class="paginacion-btn {{ $i == $pagination['current_page'] ? 'active' : '' }}" 
                style="background: {{ $i == $pagination['current_page'] ? 'var(--red)' : 'rgba(255,255,255,0.1)' }}; border: none; color: white; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;">
            {{ $i }}
        </button>
    @endfor
@endif