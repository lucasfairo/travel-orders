<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelOrder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class TravelOrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'requester_name' => 'required|string|max:255',
                'destination' => 'required|string|max:255',
                'departure_date' => 'required|date',
                'return_date' => 'required|date|after:departure_date',
            ]);

            $order = TravelOrder::create(array_merge($data, ['user_id' => Auth::id()]));

            return response()->json($order, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $order = TravelOrder::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            $request->validate(['status' => 'required|in:approved,cancelled']);

            if (!empty($order->status) && $order->status === 'approved' && $request->status === 'cancelled') {
                if (!$this->canCancelApprovedOrder($order)) {
                    return response()->json(['error' => 'Cannot cancel an approved order.'], 400);
                }
            }

            $order->update(['status' => $request->status]);

            return response()->json($order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = TravelOrder::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            return response()->json($order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = TravelOrder::where('user_id', Auth::id());

            if (!empty($request->status)) {
                $query->where('status', $request->status);
            }

            if (!empty($request->destination)) {
                $query->where('destination', $request->destination);
            }

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $query->whereBetween('departure_date', [$request->start_date, $request->end_date]);
            }

            return response()->json($query->get());
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function notify(int $id): JsonResponse
    {
        try {
            $order = TravelOrder::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            if (!empty($order->status) && !in_array($order->status, ['approved', 'cancelled'])) {
                return response()->json([
                    'error' => 'Notification can only be sent for approved or cancelled orders.'
                ], 400);
            }

            $notification = [
                'message' => "The order #{$order->id} has been {$order->status}.",
                'order_id' => $order->id,
                'status' => $order->status,
                'sent_at' => now()->toDateTimeString(),
            ];

            return response()->json([
                'success' => true,
                'notification' => $notification,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage(),], 500);
        }
    }

    protected function canCancelApprovedOrder(TravelOrder $order): bool
    {
        return !empty($order->updated_at) && $order->updated_at->diffInHours(now()) <= 24;
    }
}
