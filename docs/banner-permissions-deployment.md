# Despliegue de permisos granulares de Banners

La migración `2026_08_28_000100_split_banner_permissions` crea `banners.create`,
`banners.update` y `banners.delete`. Toda asignación existente de
`banners.manage`, directa o heredada por rol, se copia a esas tres capacidades y
el permiso anterior se elimina. `banners.view` se conserva sin cambios.

Después de desplegar el código en producción, ejecutar en este orden:

```bash
php artisan route:clear
php artisan cache:clear
php artisan migrate --force
php artisan db:seed --class=RoleSeeder --force
php artisan permission:cache-reset
php artisan route:cache
```

El owner no depende de permisos asignados: `Gate::before` le concede acceso
absoluto cuando `is_owner` es verdadero.
