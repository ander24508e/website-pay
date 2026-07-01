<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\VehicleSpecification;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VehiculosController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $brandId = $request->integer('brand_id') ?: null;
        $vehicleTypeId = $request->integer('vehicle_type_id') ?: null;
        $status = trim((string) $request->query('status', ''));

        $vehicles = Vehicle::query()
            ->with(['client', 'brand', 'model', 'type', 'specification.brand', 'specification.model', 'specification.type'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('plate', 'like', "%{$search}%")
                        ->orWhere('color', 'like', "%{$search}%")
                        ->orWhere('year', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($client) use ($search) {
                            $client->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('telefono', 'like', "%{$search}%");
                        })
                        ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('model', fn ($model) => $model->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('specification.brand', fn ($brand) => $brand->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('specification.model', fn ($model) => $model->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('specification.type', fn ($type) => $type->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($brandId, function ($query) use ($brandId) {
                $query->where(function ($sub) use ($brandId) {
                    $sub->where('vehicle_brand_id', $brandId)
                        ->orWhereHas('specification', fn ($specification) => $specification->where('vehicle_brand_id', $brandId));
                });
            })
            ->when($vehicleTypeId, function ($query) use ($vehicleTypeId) {
                $query->where(function ($sub) use ($vehicleTypeId) {
                    $sub->where('vehicle_type_id', $vehicleTypeId)
                        ->orWhereHas('specification', fn ($specification) => $specification->where('vehicle_type_id', $vehicleTypeId));
                });
            })
            ->when($status !== '', fn ($query) => $query->where('active', $status === 'active'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => (int) Vehicle::query()->count(),
            'active' => (int) Vehicle::query()->where('active', true)->count(),
            'brands' => (int) VehicleBrand::query()->count(),
            'clients' => (int) Vehicle::query()->distinct('user_id')->count('user_id'),
        ];

        $brands = VehicleBrand::query()->orderBy('name')->get(['id', 'name']);
        $vehicleTypes = VehicleType::query()->ordered()->get(['id', 'name']);

        return view('admin.vehiculos.index', compact('vehicles', 'stats', 'brands', 'vehicleTypes', 'search', 'brandId', 'vehicleTypeId', 'status'));
    }

    public function create()
    {
        return view('admin.vehiculos.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $vehicle = Vehicle::create($data);

        if ($request->boolean('return_back')) {
            return back()->with('success', 'Vehiculo creado correctamente.');
        }

        return redirect()->route('admin.vehiculos.show', $vehicle)->with('success', 'Vehiculo creado correctamente.');
    }

    public function show(Vehicle $vehiculo)
    {
        $vehiculo->load([
            'client',
            'brand',
            'model',
            'type',
            'specification.brand',
            'specification.model',
            'specification.type',
            'orderItems' => fn ($query) => $query->with(['order', 'itemable'])->latest()->limit(30),
        ]);

        return view('admin.vehiculos.show', compact('vehiculo'));
    }

    public function edit(Vehicle $vehiculo)
    {
        $vehiculo->load(['client', 'brand', 'model', 'type', 'specification.brand', 'specification.model', 'specification.type']);

        return view('admin.vehiculos.edit', array_merge($this->formData(), compact('vehiculo')));
    }

    public function update(Request $request, Vehicle $vehiculo)
    {
        $vehiculo->update($this->validatedData($request, $vehiculo));

        return redirect()->route('admin.vehiculos.show', $vehiculo)->with('success', 'Vehiculo actualizado correctamente.');
    }

    public function destroy(Vehicle $vehiculo)
    {
        $vehiculo->delete();

        return redirect()->route('admin.vehiculos.index')->with('success', 'Vehiculo eliminado correctamente.');
    }

    private function formData(): array
    {
        return [
            'clients' => User::query()->role('cliente')->orderBy('name')->get(['id', 'name', 'email']),
            'brands' => VehicleBrand::query()->where('active', true)->orderBy('name')->get(['id', 'name']),
            'models' => VehicleModel::query()->where('active', true)->with('brand:id,name')->orderBy('name')->get(['id', 'vehicle_brand_id', 'name']),
            'vehicleTypes' => VehicleType::query()->where('active', true)->ordered()->get(['id', 'name']),
            'specifications' => VehicleSpecification::query()
                ->where('active', true)
                ->with(['brand:id,name', 'model:id,name,vehicle_brand_id', 'type:id,name'])
                ->ordered()
                ->get(['id', 'vehicle_brand_id', 'vehicle_model_id', 'vehicle_type_id', 'sort_order', 'active']),
        ];
    }

    private function validatedData(Request $request, ?Vehicle $vehicle = null): array
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'vehicle_brand_id' => ['nullable', 'integer', 'exists:vehicle_brands,id'],
            'vehicle_model_id' => ['nullable', 'integer', 'exists:vehicle_models,id'],
            'vehicle_type_id' => ['nullable', 'integer', 'exists:vehicle_types,id'],
            'vehicle_specification_id' => ['nullable', 'integer', 'exists:vehicle_specifications,id'],
            'vehicle_type_name' => ['nullable', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'model_name' => ['nullable', 'string', 'max:255'],
            'plate' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate')->ignore($vehicle?->id)],
            'color' => ['nullable', 'string', 'max:80'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'observations' => ['nullable', 'string', 'max:2000'],
            'active' => ['nullable', 'boolean'],
        ]);

        $brandName = trim((string) ($data['brand_name'] ?? ''));
        $modelName = trim((string) ($data['model_name'] ?? ''));
        $vehicleTypeName = trim((string) ($data['vehicle_type_name'] ?? ''));

        if (!User::query()->role('cliente')->whereKey($data['user_id'])->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'El usuario seleccionado debe tener rol de cliente.',
            ]);
        }

        if (!empty($data['vehicle_specification_id'])) {
            $specification = VehicleSpecification::query()
                ->whereKey($data['vehicle_specification_id'])
                ->where('active', true)
                ->with(['brand', 'model', 'type'])
                ->first();

            if (!$specification || !$specification->brand?->active || !$specification->model?->active || !$specification->type?->active) {
                throw ValidationException::withMessages([
                    'vehicle_specification_id' => 'La especificación seleccionada no está disponible.',
                ]);
            }

            return [
                'user_id' => $data['user_id'],
                'vehicle_brand_id' => $specification->vehicle_brand_id,
                'vehicle_model_id' => $specification->vehicle_model_id,
                'vehicle_type_id' => $specification->vehicle_type_id,
                'vehicle_specification_id' => $specification->id,
                'plate' => mb_strtoupper(trim((string) $data['plate'])),
                'color' => trim((string) ($data['color'] ?? '')) ?: null,
                'year' => $data['year'] ?? null,
                'observations' => trim((string) ($data['observations'] ?? '')) ?: null,
                'active' => $request->boolean('active'),
            ];
        }

        if ($vehicleTypeName !== '') {
            $vehicleType = VehicleType::firstOrCreate(
                ['name' => $vehicleTypeName],
                ['description' => null, 'sort_order' => 0, 'active' => true]
            );
            if (!$vehicleType->active) {
                $vehicleType->update(['active' => true]);
            }
            $data['vehicle_type_id'] = $vehicleType->id;
        } elseif (empty($data['vehicle_type_id'])) {
            throw ValidationException::withMessages([
                'vehicle_type_id' => 'Selecciona un tipo de vehículo o escribe uno nuevo.',
            ]);
        }

        if ($brandName !== '') {
            $brand = VehicleBrand::firstOrCreate(['name' => $brandName], ['active' => true]);
            $data['vehicle_brand_id'] = $brand->id;
        } elseif (empty($data['vehicle_brand_id'])) {
            throw ValidationException::withMessages([
                'vehicle_brand_id' => 'Selecciona una marca o escribe una nueva.',
            ]);
        } else {
            $brand = VehicleBrand::findOrFail($data['vehicle_brand_id']);
        }

        if ($modelName !== '') {
            $model = VehicleModel::firstOrCreate([
                'vehicle_brand_id' => $brand->id,
                'name' => $modelName,
            ], ['active' => true]);

            $data['vehicle_model_id'] = $model->id;
        } elseif (empty($data['vehicle_model_id'])) {
            throw ValidationException::withMessages([
                'vehicle_model_id' => 'Selecciona un modelo o escribe uno nuevo.',
            ]);
        } else {
            $model = VehicleModel::where('vehicle_brand_id', $brand->id)->findOrFail($data['vehicle_model_id']);
            $data['vehicle_model_id'] = $model->id;
        }

        $specification = VehicleSpecification::firstOrCreate([
            'vehicle_brand_id' => $data['vehicle_brand_id'],
            'vehicle_model_id' => $data['vehicle_model_id'],
            'vehicle_type_id' => $data['vehicle_type_id'],
        ], [
            'sort_order' => 0,
            'active' => true,
        ]);

        return [
            'user_id' => $data['user_id'],
            'vehicle_brand_id' => $data['vehicle_brand_id'],
            'vehicle_model_id' => $data['vehicle_model_id'],
            'vehicle_type_id' => $data['vehicle_type_id'],
            'vehicle_specification_id' => $specification->id,
            'plate' => mb_strtoupper(trim((string) $data['plate'])),
            'color' => trim((string) ($data['color'] ?? '')) ?: null,
            'year' => $data['year'] ?? null,
            'observations' => trim((string) ($data['observations'] ?? '')) ?: null,
            'active' => $request->boolean('active'),
        ];
    }
}
