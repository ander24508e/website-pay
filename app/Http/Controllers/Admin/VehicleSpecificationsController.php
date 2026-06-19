<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleSpecificationsController extends Controller
{
    public function index()
    {
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

        return view('admin.vehiculos.specifications.index', compact('vehicleTypes', 'brands', 'models'));
    }

    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:vehicle_types,name'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
        ]);

        VehicleType::create([
            'name' => trim($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Tipo de vehículo creado correctamente.');
    }

    public function updateType(Request $request, VehicleType $vehicleType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('vehicle_types', 'name')->ignore($vehicleType->id)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
        ]);

        $vehicleType->update([
            'name' => trim($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'active' => $request->boolean('active'),
        ]);

        return back()->with('success', 'Tipo de vehículo actualizado correctamente.');
    }

    public function destroyType(VehicleType $vehicleType)
    {
        if ($vehicleType->vehicles()->exists() || $vehicleType->servicePrices()->exists()) {
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
        if ($vehicleBrand->vehicles()->exists() || $vehicleBrand->models()->exists()) {
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
        if ($vehicleModel->vehicles()->exists()) {
            return back()->with('error', 'No se puede eliminar un modelo que está en uso.');
        }

        $vehicleModel->delete();

        return back()->with('success', 'Modelo eliminado correctamente.');
    }
}
