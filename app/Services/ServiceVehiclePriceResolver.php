<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\CatalogType;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Validation\ValidationException;

class ServiceVehiclePriceResolver
{
    public function resolve(
        CatalogItem $service,
        ?int $vehicleId,
        ?int $vehicleTypeId,
        ?int $userId
    ): array {
        $service->loadMissing(['type', 'vehicleTypePrices.vehicleType']);

        if (($service->type?->business_model ?? CatalogType::BUSINESS_MODEL_SERVICES) !== CatalogType::BUSINESS_MODEL_SERVICES) {
            return [
                'price' => (float) $service->display_price,
                'vehicle_id' => null,
                'vehicle_type_id' => null,
                'vehicle_label' => null,
                'vehicle_type_label' => null,
            ];
        }

        $hasConfiguredPrices = $service->vehicleTypePrices->isNotEmpty();
        $vehicle = null;
        $vehicleType = null;

        if ($vehicleId) {
            if (!$userId) {
                throw ValidationException::withMessages([
                    'vehicle_id' => 'Inicia sesión para utilizar un vehículo registrado.',
                ]);
            }

            $vehicle = Vehicle::query()
                ->whereKey($vehicleId)
                ->where('user_id', $userId)
                ->where('active', true)
                ->with([
                    'specification.brand:id,name',
                    'specification.model:id,name',
                    'specification.type:id,name,active',
                ])
                ->first();

            $vehicleType = $vehicle?->resolvedType();

            if (!$vehicle || !$vehicleType || !$vehicleType->active) {
                throw ValidationException::withMessages([
                    'vehicle_id' => 'El vehículo seleccionado no está disponible o no te pertenece.',
                ]);
            }
        } elseif ($vehicleTypeId) {
            $vehicleType = VehicleType::query()
                ->whereKey($vehicleTypeId)
                ->where('active', true)
                ->first();

            if (!$vehicleType) {
                throw ValidationException::withMessages([
                    'vehicle_type_id' => 'El tipo de vehículo seleccionado no está disponible.',
                ]);
            }
        } elseif ($hasConfiguredPrices) {
            throw ValidationException::withMessages([
                'vehicle_type_id' => 'Selecciona tu vehículo o un tipo de vehículo para calcular el precio.',
            ]);
        }

        $configuredPrice = $vehicleType
            ? $service->vehicleTypePrices->firstWhere('vehicle_type_id', $vehicleType->id)
            : null;

        return [
            'price' => (float) ($configuredPrice?->price ?? $service->base_price ?? $service->display_price),
            'vehicle_id' => $vehicle?->id,
            'vehicle_type_id' => $vehicleType?->id,
            'vehicle_label' => $vehicle
                ? trim(sprintf('%s - %s %s', $vehicle->plate, $vehicle->resolvedBrand()?->name ?? '', $vehicle->resolvedModel()?->name ?? ''))
                : null,
            'vehicle_type_label' => $vehicleType?->name,
        ];
    }
}
