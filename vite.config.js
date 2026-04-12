import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/scss/website.scss',
                'resources/scss/checkout.scss',
                'resources/scss/auth.scss',
                'resources/scss/profile/profile-edit.scss',
                'resources/scss/profile/customer-perfil.scss',
                'resources/scss/profile/customer-compras.scss',
                'resources/scss/carrito.scss',
                'resources/scss/admin/layout.scss',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
