@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nameInput = document.getElementById(@json($nameInputId));
        const slugInput = document.getElementById(@json($slugInputId));

        function slugify(value) {
            return value.toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-').replace(/-+/g, '-');
        }

        function syncSlug() {
            if (slugInput) slugInput.value = slugify(nameInput?.value || '');
        }

        nameInput?.addEventListener('input', syncSlug);
        if (nameInput?.value && !slugInput?.value) syncSlug();
    });
</script>
@endpush
