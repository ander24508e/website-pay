<div class="mb-6 overflow-x-auto rounded-lg border border-gray-200/70 bg-white p-2 shadow-sm">
    <div class="flex min-w-max items-center gap-2">
        <a href="{{ route('admin.inventario.index', array_filter(['q' => request('q')])) }}"
            class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold transition {{ $selectedTypeId === 0 ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            Todos
        </a>
        @foreach ($productTypes as $type)
            <a href="{{ route('admin.inventario.index', array_filter(['catalog_type_id' => $type->id, 'q' => request('q')])) }}"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold transition {{ $selectedTypeId === $type->id ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ $type->name }}
            </a>
        @endforeach
    </div>
</div>
