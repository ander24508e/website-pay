<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\CatalogType;
use App\Models\Vehicle;
use App\Models\VehicleSpecification;
use Illuminate\Validation\ValidationException;

class ServiceVehiclePriceResolver
{
    public function resolve(
        CatalogItem $service,
        ?int $vehicleId,
        ?int $vehicleSpecificationId,
        ?int $userId
    ): array {
        $service->loadMissing([
            'type',
            'vehicleTypePrices.vehicleSpecification.brand',
            'vehicleTypePrices.vehicleSpecification.model',
            'vehicleTypePrices.vehicleSpecification.type',
        ]);

        if (($service->type?->business_model ?? CatalogType::BUSINESS_MODEL_SERVICES) !== CatalogType::BUSINESS_MODEL_SERVICES) {
            return [
                'price' => (float) $service->display_price,
                'duration_minutes' => null,
                'vehicle_id' => null,
                'vehicle_type_id' => null,
                'vehicle_specification_id' => null,
                'vehicle_label' => null,
                'vehicle_type_label' => null,
                'vehicle_specification_label' => null,
            ];
        }

        $hasConfiguredPrices = $service->vehicleTypePrices->isNotEmpty();
        $vehicle = null;
        $vehicleSpecification = null;

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
                    'specification.brand:id,name,active',
                    'specification.model:id,name,active',
                    'specification.type:id,name,active',
                ])
                ->first();

            $vehicleSpecification = $vehicle?->specification;

            if (
                !$vehicle ||
                !$vehicleSpecification ||
                !$vehicleSpecification->active ||
                !$vehicleSpecification->brand?->active ||
                !$vehicleSpecification->model?->active ||
                !$vehicleSpecification->type?->active
            ) {
                throw ValidationException::withMessages([
                    'vehicle_id' => 'El vehículo seleccionado no está disponible o no te pertenece.',
                ]);
            }
        } elseif ($vehicleSpecificationId) {
            $vehicleSpecification = VehicleSpecification::query()
                ->whereKey($vehicleSpecificationId)
                ->where('active', true)
                ->with([
                    'brand:id,name,active',
                    'model:id,name,active',
                    'type:id,name,active',
                ])
                ->first();

            if (!$vehicleSpecification) {
                $vehicleSpecification = VehicleSpecification::query()
                    ->where('vehicle_type_id', $vehicleSpecificationId)
                    ->where('active', true)
                    ->with([
                        'brand:id,name,active',
                        'model:id,name,active',
                        'type:id,name,active',
                    ])
                    ->ordered()
                    ->first();
            }

            if (
                !$vehicleSpecification ||
                !$vehicleSpecification->brand?->active ||
                !$vehicleSpecification->model?->active ||
                !$vehicleSpecification->type?->active
            ) {
                throw ValidationException::withMessages([
                    'vehicle_specification_id' => 'La especificación seleccionada no está disponible.',
                ]);
            }
        } elseif ($hasConfiguredPrices) {
            throw ValidationException::withMessages([
                'vehicle_specification_id' => 'Selecciona tu vehículo o una especificación para calcular el precio.',
            ]);
        }

        $configuredPrice = $vehicleSpecification
            ? $service->vehicleTypePrices->firstWhere('vehicle_specification_id', $vehicleSpecification->id)
            : null;

        return [
            'price' => (float) ($configuredPrice?->price ?? $service->base_price ?? $service->display_price),
            'duration_minutes' => $configuredPrice?->duration_minutes ?? $service->duration_minutes,
            'vehicle_id' => $vehicle?->id,
            'vehicle_type_id' => $vehicleSpecification?->type?->id,
            'vehicle_specification_id' => $vehicleSpecification?->id,
            'vehicle_label' => $vehicle
                ? trim(sprintf('%s - %s %s', $vehicle->plate, $vehicle->resolvedBrand()?->name ?? '', $vehicle->resolvedModel()?->name ?? ''))
                : null,
            'vehicle_type_label' => $vehicleSpecification?->type?->name,
            'vehicle_specification_label' => $vehicleSpecification?->label,
        ];
    }
}
