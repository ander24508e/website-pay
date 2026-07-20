<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with([
                'user:id,name,email,telefono',
                'assignedTo:id,name',
                'items.itemable',
                'items.vehicle.specification.brand',
                'items.vehicle.specification.model',
                'items.vehicle.specification.type',
                'items.vehicleType',
                'sale:id',
            ])
            ->when($request->filled('work_status'), fn ($query) => $query->where('work_status', (string) $request->string('work_status')))
            ->when($request->filled('assigned_to'), fn ($query) => $query->where('assigned_to', $request->integer('assigned_to')))
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->date('date')?->toDateString();

                if ($date) {
                    $query->where(function ($dateQuery) use ($date) {
                        $dateQuery->whereDate('scheduled_at', $date)
                            ->orWhere(function ($fallbackQuery) use ($date) {
                                $fallbackQuery->whereNull('scheduled_at')
                                    ->whereDate('created_at', $date);
                            });
                    });
                }
            })
            ->orderBy('scheduled_at')
            ->orderBy('id')
            ->paginate((int) $request->query('per_page', 20));

        return response()->json($orders);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load([
            'user:id,name,email,telefono',
            'assignedTo:id,name',
            'items.itemable',
            'items.vehicle.specification.brand',
            'items.vehicle.specification.model',
            'items.vehicle.specification.type',
            'items.vehicleType',
            'transaction',
            'sale:id',
        ]);

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'work_status' => $order->work_status,
            'work_status_label' => $order->work_status_label,
            'order_type' => $order->order_type,
            'scheduled_at' => $order->scheduled_at,
            'created_at' => $order->created_at,
            'total' => $order->total,
            'client' => $order->user,
            'assigned_to' => $order->assignedTo,
            'sale_id' => $order->sale_id,
            'work_notes' => $order->work_notes,
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->item_display_name,
                'type' => $item->item_type_label,
                'vehicle' => $item->vehicle_display,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => (float) $item->unit_price * (int) $item->quantity,
            ])->values(),
            'available_transitions' => $order->workTransitions(),
        ]);
    }

    public function updateWorkStatus(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'work_status' => ['required', 'string'],
        ]);

        $status = $data['work_status'];
        $allowed = $order->workTransitions();

        if (!array_key_exists($status, $allowed)) {
            return response()->json([
                'message' => 'No se puede aplicar ese cambio de estado a esta orden.',
            ], 422);
        }

        $timestampColumn = match ($status) {
            Order::WORK_ARRIVED => 'arrived_at',
            Order::WORK_IN_PROGRESS => 'started_at',
            Order::WORK_READY => 'ready_at',
            Order::WORK_COMPLETED => 'completed_at',
            Order::WORK_CANCELLED => 'cancelled_at',
            default => null,
        };

        $payload = ['work_status' => $status];

        if ($timestampColumn) {
            $payload[$timestampColumn] = now();
        }

        if ($status === Order::WORK_IN_PROGRESS && !$order->assigned_to) {
            $payload['assigned_to'] = $request->user()?->id;
        }

        $order->update($payload);

        return response()->json([
            'message' => 'Estado operativo actualizado.',
            'order' => [
                'id' => $order->id,
                'work_status' => $order->work_status,
                'work_status_label' => $order->work_status_label,
            ],
        ]);
    }
}
