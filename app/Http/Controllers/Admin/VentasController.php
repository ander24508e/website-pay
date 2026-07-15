<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Data\SaleData;
use App\Http\Requests\Admin\StoreSaleRequest;
use App\Http\Requests\Admin\UpdateSaleRequest;
use App\Models\CatalogItem;
use App\Models\Order;
use App\Models\Sale;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\Sales\CreateSaleService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class VentasController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $origin = trim((string) $request->query('origin', ''));
        $sort = 'oldest';
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($origin === 'sistema') {
            $origin = 'internal';
        }

        $orders = collect();
        $sales = collect();

        if ($origin === '' || $origin === 'web') {
            $orders = Order::query()
                ->with(['user', 'items', 'transaction'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($sub) use ($search) {
                        $sub->where('id', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($user) use ($search) {
                                $user->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
                ->latest()
                ->get()
                ->map(fn (Order $order) => $this->mapOrderRow($order));
        }

        if ($origin === '' || $origin === 'internal') {
            $sales = Sale::query()
                ->with(['user', 'items', 'attendedBy'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($sub) use ($search) {
                        $sub->where('id', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhere('payment_method', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($user) use ($search) {
                                $user->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
                ->latest()
                ->get()
                ->map(fn (Sale $sale) => $this->mapSaleRow($sale));
        }

        $rows = $orders
            ->merge($sales)
            ->sortBy('created_at')
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $ventas = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'total_ventas' => (float) Order::query()->where('status', 'paid')->sum('total')
                + (float) Sale::query()->where('status', 'paid')->sum('total'),
            'total_ordenes' => (int) Order::query()->count(),
            'ventas_internas' => (int) Sale::query()->count(),
            'ticket_promedio' => (float) $this->paidAverageTicket(),
        ];

        return view('admin.ventas.index', compact('ventas', 'stats', 'search', 'status', 'origin', 'sort', 'dateFrom', 'dateTo'));
    }

    public function show(string $venta)
    {
        [$origin, $id] = $this->parseVentaKey($venta);

        if ($origin === 'web') {
            $record = Order::query()
                ->with(['user', 'items.itemable', 'items.vehicle.brand', 'items.vehicle.model', 'items.vehicleType', 'transaction'])
                ->findOrFail($id);

            return view('admin.ventas.show', [
                'origin' => 'web',
                'record' => $record,
                'items' => $record->items,
            ]);
        }

        $record = Sale::query()
            ->with(['user', 'vehicle.brand', 'vehicle.model', 'vehicle.type', 'attendedBy', 'items.catalogItem', 'items.variant', 'items.vehicle', 'items.vehicleType', 'payments'])
            ->findOrFail($id);

        return view('admin.ventas.show', [
            'origin' => 'internal',
            'record' => $record,
            'items' => $record->items,
        ]);
    }

    public function create()
    {
        return view('admin.ventas.create', $this->formData());
    }

    public function store(StoreSaleRequest $request, CreateSaleService $createSaleService)
    {
        $sale = $createSaleService->create(SaleData::fromArray($request->validated()));

        return redirect()->route('admin.ventas.show', 'internal-' . $sale->id)
            ->with('success', 'Venta del sistema creada correctamente.');
    }

    public function edit(string $venta)
    {
        [$origin, $id] = $this->parseVentaKey($venta);

        if ($origin === 'web') {
            return redirect()->route('admin.ventas.show', 'web-' . $id)
                ->with('error', 'Las compras web se gestionan desde Ordenes.');
        }

        $sale = Sale::query()->with(['items'])->findOrFail($id);

        if ($sale->status === Sale::STATUS_PAID) {
            return redirect()->route('admin.ventas.show', 'internal-' . $sale->id)
                ->with('error', 'Una venta pagada no debe editarse. Puedes cancelarla si corresponde.');
        }

        return view('admin.ventas.edit', array_merge($this->formData(), compact('sale')));
    }

    public function update(UpdateSaleRequest $request, string $venta)
    {
        [$origin, $id] = $this->parseVentaKey($venta);

        if ($origin === 'web') {
            return redirect()->route('admin.ventas.show', 'web-' . $id)
                ->with('error', 'Las compras web se gestionan desde Ordenes.');
        }

        $sale = Sale::findOrFail($id);

        if ($sale->status === Sale::STATUS_PAID) {
            return redirect()->route('admin.ventas.show', 'internal-' . $sale->id)
                ->with('error', 'Una venta pagada no debe editarse.');
        }

        $data = $request->validated();

        $sale->update([
            'user_id' => $data['user_id'] ?? null,
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'attended_by' => $data['attended_by'] ?? null,
            'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
        ]);

        return redirect()->route('admin.ventas.show', 'internal-' . $sale->id)
            ->with('success', 'Venta actualizada correctamente.');
    }

    public function destroy(string $venta)
    {
        [$origin, $id] = $this->parseVentaKey($venta);

        if ($origin === 'web') {
            return redirect()->route('admin.ventas.index')
                ->with('error', 'Las compras web no se eliminan desde Ventas.');
        }

        $sale = Sale::findOrFail($id);

        if ($sale->status === Sale::STATUS_PAID) {
            return redirect()->route('admin.ventas.show', 'internal-' . $sale->id)
                ->with('error', 'Una venta pagada no se elimina. Mantén el historial.');
        }

        $sale->delete();

        return redirect()->route('admin.ventas.index')->with('success', 'Venta eliminada correctamente.');
    }

    private function formData(): array
    {
        return [
            'clientes' => User::query()->role('cliente')->orderBy('name')->get(['id', 'name', 'email']),
            'usuarios' => User::query()->orderBy('name')->get(['id', 'name', 'email']),
            'vehicles' => Vehicle::query()
                ->with(['client:id,name,email', 'brand:id,name', 'model:id,name', 'type:id,name', 'specification.brand:id,name', 'specification.model:id,name', 'specification.type:id,name'])
                ->where('active', true)
                ->orderBy('plate')
                ->get(),
            'vehicleTypes' => VehicleType::query()->where('active', true)->ordered()->get(['id', 'name']),
            'catalogItems' => CatalogItem::query()
                ->with([
                    'type:id,name,business_model',
                    'activeVariants:id,catalog_item_id,name,presentation,specification,sku,price,stock,active,is_default',
                    'vehicleTypePrices:id,catalog_item_id,vehicle_type_id,price',
                ])
                ->where('active', true)
                ->where('purchasable', true)
                ->orderBy('name')
                ->get(),
        ];
    }

    private function parseVentaKey(string $key): array
    {
        if (str_starts_with($key, 'web-')) {
            return ['web', (int) substr($key, 4)];
        }

        if (str_starts_with($key, 'internal-')) {
            return ['internal', (int) substr($key, 9)];
        }

        return ['web', (int) $key];
    }

    private function mapOrderRow(Order $order): object
    {
        return (object) [
            'key' => 'web-' . $order->id,
            'id' => $order->id,
            'origin' => 'Web',
            'origin_key' => 'web',
            'client' => $order->user?->name ?? 'Invitado',
            'type' => ($order->order_type ?? 'purchase') === 'reservation' ? 'Reserva' : 'Compra',
            'status' => $order->status,
            'items_count' => $order->items->count(),
            'total' => (float) $order->total,
            'created_at' => $order->created_at,
            'editable' => false,
            'deletable' => false,
        ];
    }

    private function mapSaleRow(Sale $sale): object
    {
        return (object) [
            'key' => 'internal-' . $sale->id,
            'id' => $sale->id,
            'origin' => 'Sistema',
            'origin_key' => 'internal',
            'client' => $sale->user?->name ?? 'Invitado',
            'type' => 'Venta directa',
            'status' => $sale->status,
            'items_count' => $sale->items->count(),
            'total' => (float) $sale->total,
            'created_at' => $sale->created_at,
            'editable' => $sale->status !== Sale::STATUS_PAID,
            'deletable' => $sale->status !== Sale::STATUS_PAID,
        ];
    }

    private function paidAverageTicket(): float
    {
        $total = (float) Order::query()->where('status', 'paid')->sum('total')
            + (float) Sale::query()->where('status', 'paid')->sum('total');
        $count = (int) Order::query()->where('status', 'paid')->count()
            + (int) Sale::query()->where('status', 'paid')->count();

        return $count > 0 ? $total / $count : 0;
    }
}

