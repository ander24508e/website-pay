<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\CatalogType;
use App\Models\Vehicle;
use App\Models\VehicleSpecification;
use App\Models\VehicleType;
use Illuminate\Validation\ValidationException;

class ServiceVehiclePriceResolver
{
    public function resolve(
        CatalogItem $service,
        ?int $vehicleId,
        ?int $vehicleSpecificationId,
        ?int $userId,
        ?int $vehicleTypeId = null
    ): array {
        $service->loadMissing([
            'type',
            'vehicleTypePrices.vehicleType',
            'vehicleTypePrices.vehicleSpecification.brand',
            'vehicleTypePrices.vehicleSpecification.model',
            'vehicleTypePrices.vehicleSpecification.type',
        ]);

        if (($service->type?->business_model ?? CatalogType::BUSINESS_MODEL_SERVICES) !== CatalogType::BUSINESS_MODEL_SERVICES) {
            return $this->emptyContext((float) $service->display_price);
        }

        $hasConfiguredPrices = $service->vehicleTypePrices->where('active', true)->isNotEmpty();
        $vehicle = null;
        $vehicleSpecification = null;
        $vehicleType = null;

        if ($vehicleId) {
            if (!$userId) {
                throw ValidationException::withMessages([
                    'vehicle_id' => 'Inicia sesion para utilizar un vehiculo registrado.',
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
            $vehicleType = $vehicleSpecification?->type;

            if (
                !$vehicle ||
                !$vehicleSpecification ||
                !$vehicleSpecification->active ||
                !$vehicleSpecification->brand?->active ||
                !$vehicleSpecification->model?->active ||
                !$vehicleType?->active
            ) {
                throw ValidationException::withMessages([
                    'vehicle_id' => 'El vehiculo seleccionado no esta disponible o no te pertenece.',
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

            if ($vehicleSpecification) {
                $vehicleType = $vehicleSpecification->type;
            } else {
                $vehicleType = VehicleType::query()
                    ->whereKey($vehicleSpecificationId)
                    ->where('active', true)
                    ->first();
            }

            if (!$vehicleType?->active) {
                throw ValidationException::withMessages([
                    'vehicle_specification_id' => 'El vehiculo seleccionado no esta disponible.',
                ]);
            }
        } elseif ($vehicleTypeId) {
            $vehicleType = VehicleType::query()
                ->whereKey($vehicleTypeId)
                ->where('active', true)
                ->first();

            if (!$vehicleType?->active) {
                throw ValidationException::withMessages([
                    'vehicle_type_id' => 'El tipo de vehiculo seleccionado no esta disponible.',
                ]);
            }
        } elseif ($hasConfiguredPrices) {
            throw ValidationException::withMessages([
                'vehicle_specification_id' => 'Selecciona tu vehiculo para calcular el precio.',
            ]);
        }

        $activePrices = $service->vehicleTypePrices->where('active', true);
        $configuredPrice = null;

        if ($vehicleSpecification) {
            $configuredPrice = $activePrices->firstWhere('vehicle_specification_id', $vehicleSpecification->id);
        }

        if (!$configuredPrice && $vehicleType) {
            $configuredPrice = $activePrices
                ->whereNull('vehicle_specification_id')
                ->firstWhere('vehicle_type_id', $vehicleType->id);
        }

        if ($hasConfiguredPrices && !$configuredPrice) {
            throw ValidationException::withMessages([
                'vehicle_specification_id' => 'Este servicio no tiene precio configurado para ese vehiculo.',
            ]);
        }

        return [
            'price' => (float) ($configuredPrice?->price ?? $service->base_price ?? $service->display_price),
            'duration_minutes' => $configuredPrice?->duration_minutes ?? $service->duration_minutes,
            'vehicle_id' => $vehicle?->id,
            'vehicle_type_id' => $vehicleType?->id,
            'vehicle_specification_id' => $vehicleSpecification?->id,
            'vehicle_label' => $vehicle
                ? trim(sprintf('%s - %s %s', $vehicle->plate, $vehicle->resolvedBrand()?->name ?? '', $vehicle->resolvedModel()?->name ?? ''))
                : null,
            'vehicle_type_label' => $vehicleType?->name,
            'vehicle_specification_label' => $vehicleSpecification?->label,
        ];
    }

    private function emptyContext(float $price): array
    {
        return [
            'price' => $price,
            'duration_minutes' => null,
            'vehicle_id' => null,
            'vehicle_type_id' => null,
            'vehicle_specification_id' => null,
            'vehicle_label' => null,
            'vehicle_type_label' => null,
            'vehicle_specification_label' => null,
        ];
    }
}
