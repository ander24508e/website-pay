<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\VehicleSpecification;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VehicleSpecificationsController extends Controller
{
    public function index()
    {
        $vehicleSpecifications = VehicleSpecification::query()
            ->with(['brand:id,name,active', 'model:id,name,vehicle_brand_id,active', 'type:id,name,active'])
            ->withCount('vehicles')
            ->ordered()
            ->get();

        $vehicleTypes = VehicleType::query()
            ->withCount('vehicles')
            ->ordered()
            ->get();

        $brands = VehicleBrand::query()
            ->withCount(['vehicles', 'models'])
            ->orderBy('name')
            ->get();

        $models = VehicleModel::query()
            ->with(['brand:id,name'])
            ->withCount('vehicles')
            ->orderBy('name')
            ->get();

        return view('admin.vehiculos.specifications.index', compact('vehicleSpecifications', 'vehicleTypes', 'brands', 'models'));
    }

    public function storeSpecification(Request $request)
    {
        $resolved = $this->resolveSpecificationFields($request);

        $specification = VehicleSpecification::updateOrCreate([
            'vehicle_brand_id' => $resolved['vehicle_brand_id'],
            'vehicle_model_id' => $resolved['vehicle_model_id'],
            'vehicle_type_id' => $resolved['vehicle_type_id'],
        ], ['active' => $resolved['active']]);

        return back()->with('success', $specification->wasRecentlyCreated
            ? 'Especificación creada correctamente.'
            : 'La especificación ya existía y fue actualizada.');
    }

    public function updateSpecification(Request $request, VehicleSpecification $vehicleSpecification)
    {
        $data = $request->validate([
            'vehicle_brand_id' => ['required', 'integer', 'exists:vehicle_brands,id'],
            'vehicle_model_id' => ['required', 'integer', 'exists:vehicle_models,id'],
            'vehicle_type_id' => ['required', 'integer', 'exists:vehicle_types,id'],
            'active' => ['nullable', 'boolean'],
        ]);

        $model = VehicleModel::query()
            ->where('vehicle_brand_id', $data['vehicle_brand_id'])
            ->whereKey($data['vehicle_model_id'])
            ->firstOrFail();

        $duplicate = VehicleSpecification::query()
            ->where('vehicle_brand_id', $data['vehicle_brand_id'])
            ->where('vehicle_model_id', $model->id)
            ->where('vehicle_type_id', $data['vehicle_type_id'])
            ->whereKeyNot($vehicleSpecification->id)
            ->exists();

        if ($duplicate) {
            return back()->with('error', 'Ya existe una especificación con esa marca, modelo y tipo.');
        }

        $vehicleSpecification->update([
            'vehicle_brand_id' => $data['vehicle_brand_id'],
            'vehicle_model_id' => $model->id,
            'vehicle_type_id' => $data['vehicle_type_id'],
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Especificación actualizada correctamente.');
    }

    public function destroySpecification(VehicleSpecification $vehicleSpecification)
    {
        if ($vehicleSpecification->vehicles()->exists()) {
            return back()->with('error', 'No se puede eliminar una especificación usada por vehículos.');
        }

        $vehicleSpecification->delete();

        return back()->with('success', 'Especificación eliminada correctamente.');
    }

    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:vehicle_types,name'],
            'active' => ['nullable', 'boolean'],
        ]);

        VehicleType::create([
            'name' => trim($data['name']),
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Tipo de vehículo creado correctamente.');
    }

    public function updateType(Request $request, VehicleType $vehicleType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('vehicle_types', 'name')->ignore($vehicleType->id)],
            'active' => ['nullable', 'boolean'],
        ]);

        $vehicleType->update([
            'name' => trim($data['name']),
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Tipo de vehículo actualizado correctamente.');
    }

    public function destroyType(VehicleType $vehicleType)
    {
        if ($vehicleType->vehicles()->exists() || $vehicleType->servicePrices()->exists() || $vehicleType->specifications()->exists()) {
            return back()->with('error', 'No se puede eliminar un tipo de vehículo que está en uso.');
        }

        $vehicleType->delete();

        return back()->with('success', 'Tipo de vehículo eliminado correctamente.');
    }

    public function storeBrand(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:vehicle_brands,name'],
            'active' => ['nullable', 'boolean'],
        ]);

        VehicleBrand::create([
            'name' => trim($data['name']),
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Marca creada correctamente.');
    }

    public function updateBrand(Request $request, VehicleBrand $vehicleBrand)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('vehicle_brands', 'name')->ignore($vehicleBrand->id)],
            'active' => ['nullable', 'boolean'],
        ]);

        $vehicleBrand->update([
            'name' => trim($data['name']),
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Marca actualizada correctamente.');
    }

    public function destroyBrand(VehicleBrand $vehicleBrand)
    {
        if ($vehicleBrand->vehicles()->exists() || $vehicleBrand->models()->exists() || $vehicleBrand->specifications()->exists()) {
            return back()->with('error', 'No se puede eliminar una marca que está en uso.');
        }

        $vehicleBrand->delete();

        return back()->with('success', 'Marca eliminada correctamente.');
    }

    public function storeModel(Request $request)
    {
        $data = $request->validate([
            'vehicle_brand_id' => ['required', 'integer', 'exists:vehicle_brands,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vehicle_models', 'name')->where('vehicle_brand_id', $request->integer('vehicle_brand_id')),
            ],
            'active' => ['nullable', 'boolean'],
        ]);

        VehicleModel::create([
            'vehicle_brand_id' => $data['vehicle_brand_id'],
            'name' => trim($data['name']),
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Modelo creado correctamente.');
    }

    public function updateModel(Request $request, VehicleModel $vehicleModel)
    {
        $data = $request->validate([
            'vehicle_brand_id' => ['required', 'integer', 'exists:vehicle_brands,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vehicle_models', 'name')
                    ->where('vehicle_brand_id', $request->integer('vehicle_brand_id'))
                    ->ignore($vehicleModel->id),
            ],
            'active' => ['nullable', 'boolean'],
        ]);

        $vehicleModel->update([
            'vehicle_brand_id' => $data['vehicle_brand_id'],
            'name' => trim($data['name']),
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Modelo actualizado correctamente.');
    }

    public function destroyModel(VehicleModel $vehicleModel)
    {
        if ($vehicleModel->vehicles()->exists() || $vehicleModel->specifications()->exists()) {
            return back()->with('error', 'No se puede eliminar un modelo que está en uso.');
        }

        $vehicleModel->delete();

        return back()->with('success', 'Modelo eliminado correctamente.');
    }

    private function resolveSpecificationFields(Request $request): array
    {
        $data = $request->validate([
            'vehicle_brand_id' => ['nullable', 'integer', 'exists:vehicle_brands,id'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'vehicle_model_id' => ['nullable', 'integer', 'exists:vehicle_models,id'],
            'model_name' => ['nullable', 'string', 'max:255'],
            'vehicle_type_id' => ['nullable', 'integer', 'exists:vehicle_types,id'],
            'vehicle_type_name' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
        ]);

        $brandName = trim((string) ($data['brand_name'] ?? ''));
        $modelName = trim((string) ($data['model_name'] ?? ''));
        $vehicleTypeName = trim((string) ($data['vehicle_type_name'] ?? ''));

        if ($brandName !== '') {
            $brand = VehicleBrand::firstOrCreate(['name' => $brandName], ['active' => true]);
        } elseif (!empty($data['vehicle_brand_id'])) {
            $brand = VehicleBrand::findOrFail($data['vehicle_brand_id']);
        } else {
            throw ValidationException::withMessages(['vehicle_brand_id' => 'Selecciona una marca o escribe una nueva.']);
        }

        if ($modelName !== '') {
            $model = VehicleModel::firstOrCreate([
                'vehicle_brand_id' => $brand->id,
                'name' => $modelName,
            ], ['active' => true]);
        } elseif (!empty($data['vehicle_model_id'])) {
            $model = VehicleModel::query()
                ->where('vehicle_brand_id', $brand->id)
                ->whereKey($data['vehicle_model_id'])
                ->firstOrFail();
        } else {
            throw ValidationException::withMessages(['vehicle_model_id' => 'Selecciona un modelo o escribe uno nuevo.']);
        }

        if ($vehicleTypeName !== '') {
            $vehicleType = VehicleType::firstOrCreate([
                'name' => $vehicleTypeName,
            ], [
                'description' => null,
                'active' => true,
            ]);
        } elseif (!empty($data['vehicle_type_id'])) {
            $vehicleType = VehicleType::findOrFail($data['vehicle_type_id']);
        } else {
            throw ValidationException::withMessages(['vehicle_type_id' => 'Selecciona un tipo o escribe uno nuevo.']);
        }

        return [
            'vehicle_brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'vehicle_type_id' => $vehicleType->id,
            'active' => $request->boolean('active'),
        ];
    }
}
