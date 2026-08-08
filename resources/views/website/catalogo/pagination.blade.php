@if($pagination['last_page'] > 1)
    @for($i = 1; $i <= $pagination['last_page']; $i++)
        <button type="button" data-page="{{ $i }}" class="paginacion-btn {{ $i == $pagination['current_page'] ? 'active' : '' }}">
            {{ $i }}
        </button>
    @endfor
@endif
