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
            ->with(['client', 'specification.brand', 'specification.model', 'specification.type'])
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
                        ->orWhereHas('specification.brand', fn ($brand) => $brand->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('specification.model', fn ($model) => $model->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('specification.type', fn ($type) => $type->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($brandId, function ($query) use ($brandId) {
                $query->whereHas('specification', fn ($specification) => $specification->where('vehicle_brand_id', $brandId));
            })
            ->when($vehicleTypeId, function ($query) use ($vehicleTypeId) {
                $query->whereHas('specification', fn ($specification) => $specification->where('vehicle_type_id', $vehicleTypeId));
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

    public function quickStore(Request $request)
    {
        $vehicle = Vehicle::create($this->validatedQuickData($request))
            ->load(['client:id,name,email', 'specification.brand:id,name', 'specification.model:id,name', 'specification.type:id,name']);

        return response()->json([
            'message' => 'Vehiculo creado correctamente.',
            'vehicle' => [
                'id' => $vehicle->id,
                'user_id' => $vehicle->user_id,
                'vehicle_specification_id' => $vehicle->vehicle_specification_id,
                'vehicle_type_id' => $vehicle->resolvedType()?->id,
                'specification_label' => $vehicle->specification?->label,
                'plate' => $vehicle->plate,
                'label' => trim($vehicle->plate . ' - ' . $vehicle->resolvedBrand()?->name . ' ' . $vehicle->resolvedModel()?->name),
            ],
        ]);
    }

    public function show(Vehicle $vehiculo)
    {
        $vehiculo->load([
            'client',
            'specification.brand',
            'specification.model',
            'specification.type',
            'orderItems' => fn ($query) => $query->with(['order', 'itemable'])->latest()->limit(30),
        ]);

        return view('admin.vehiculos.show', compact('vehiculo'));
    }

    public function edit(Vehicle $vehiculo)
    {
        $vehiculo->load(['client', 'specification.brand', 'specification.model', 'specification.type']);

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
                ->get(['id', 'vehicle_brand_id', 'vehicle_model_id', 'vehicle_type_id', 'active']),
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
            'vehicle_specification_id' => ['required', 'integer', 'exists:vehicle_specifications,id'],
            'plate' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate')->ignore($vehicle?->id)],
            'color' => ['nullable', 'string', 'max:80'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'observations' => ['nullable', 'string', 'max:2000'],
            'active' => ['nullable', 'boolean'],
        ]);

        if (!User::query()->role('cliente')->whereKey($data['user_id'])->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'El usuario seleccionado debe tener rol de cliente.',
            ]);
        }

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
            'vehicle_specification_id' => $specification->id,
            'plate' => mb_strtoupper(trim((string) $data['plate'])),
            'color' => trim((string) ($data['color'] ?? '')) ?: null,
            'year' => $data['year'] ?? null,
            'observations' => trim((string) ($data['observations'] ?? '')) ?: null,
            'active' => $request->boolean('active'),
        ];
    }

    private function validatedQuickData(Request $request): array
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'specification_mode' => ['required', Rule::in(['existing', 'new'])],
            'vehicle_specification_id' => ['nullable', 'integer', 'exists:vehicle_specifications,id'],
            'new_vehicle_brand_name' => ['nullable', 'string', 'max:255'],
            'new_vehicle_model_name' => ['nullable', 'string', 'max:255'],
            'new_vehicle_type_name' => ['nullable', 'string', 'max:255'],
            'plate' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate')],
            'color' => ['nullable', 'string', 'max:80'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'observations' => ['nullable', 'string', 'max:2000'],
            'active' => ['nullable', 'boolean'],
        ]);

        if (!User::query()->role('cliente')->whereKey($data['user_id'])->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'El usuario seleccionado debe tener rol de cliente.',
            ]);
        }

        $specification = $this->resolveQuickSpecification($data);

        return [
            'user_id' => $data['user_id'],
            'vehicle_specification_id' => $specification->id,
            'plate' => mb_strtoupper(trim((string) $data['plate'])),
            'color' => trim((string) ($data['color'] ?? '')) ?: null,
            'year' => $data['year'] ?? null,
            'observations' => trim((string) ($data['observations'] ?? '')) ?: null,
            'active' => $request->boolean('active'),
        ];
    }

    private function resolveQuickSpecification(array $data): VehicleSpecification
    {
        if (($data['specification_mode'] ?? 'existing') === 'existing') {
            $specification = VehicleSpecification::query()
                ->whereKey($data['vehicle_specification_id'] ?? null)
                ->where('active', true)
                ->with(['brand', 'model', 'type'])
                ->first();

            if (!$specification || !$specification->brand?->active || !$specification->model?->active || !$specification->type?->active) {
                throw ValidationException::withMessages([
                    'vehicle_specification_id' => 'Selecciona una especificación de vehículo disponible.',
                ]);
            }

            return $specification;
        }

        $brandName = $this->cleanSpecificationName($data['new_vehicle_brand_name'] ?? null);
        $modelName = $this->cleanSpecificationName($data['new_vehicle_model_name'] ?? null);
        $typeName = $this->cleanSpecificationName($data['new_vehicle_type_name'] ?? null);

        $errors = [];
        if (!$brandName) {
            $errors['new_vehicle_brand_name'] = 'Ingresa la marca del vehículo.';
        }
        if (!$modelName) {
            $errors['new_vehicle_model_name'] = 'Ingresa el modelo del vehículo.';
        }
        if (!$typeName) {
            $errors['new_vehicle_type_name'] = 'Ingresa el tipo de vehículo.';
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $brand = VehicleBrand::firstOrCreate(['name' => $brandName], ['active' => true]);
        $model = VehicleModel::firstOrCreate([
            'vehicle_brand_id' => $brand->id,
            'name' => $modelName,
        ], ['active' => true]);
        $type = VehicleType::firstOrCreate(['name' => $typeName], ['active' => true]);

        return VehicleSpecification::firstOrCreate([
            'vehicle_brand_id' => $brand->id,
            'vehicle_model_id' => $model->id,
            'vehicle_type_id' => $type->id,
        ], ['active' => true]);
    }

    private function cleanSpecificationName(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
