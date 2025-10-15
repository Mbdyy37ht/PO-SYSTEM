<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\ItemStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryApprovalController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['salesOrder', 'warehouse', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
        return view('admin.deliveries.approval', compact('deliveries'));
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['salesOrder.customer', 'warehouse', 'creator', 'details.item']);
        return view('admin.deliveries.show', compact('delivery'));
    }

    public function approve(Request $request, Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Only pending Deliveries can be approved.');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Validate stock availability again before approval
            foreach ($delivery->details as $detail) {
                $itemStock = ItemStock::where('item_id', $detail->item_id)
                    ->where('warehouse_id', $delivery->warehouse_id)
                    ->first();

                if (!$itemStock || $itemStock->quantity < $detail->quantity_delivered) {
                    DB::rollBack();
                    return back()->with('error', 'Insufficient stock for item: ' . $detail->item->name);
                }
            }

            // Update Delivery status
            $delivery->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            // Update stock for each item
            foreach ($delivery->details as $detail) {
                $itemStock = ItemStock::where('item_id', $detail->item_id)
                    ->where('warehouse_id', $delivery->warehouse_id)
                    ->first();

                // Decrease stock
                $itemStock->decrement('quantity', $detail->quantity_delivered);

                // Create stock movement record
                StockMovement::create([
                    'item_id' => $detail->item_id,
                    'warehouse_id' => $delivery->warehouse_id,
                    'reference_type' => 'Delivery',
                    'reference_id' => $delivery->id,
                    'movement_type' => 'out',
                    'quantity' => $detail->quantity_delivered,
                    'notes' => 'Stock out from Delivery: ' . $delivery->delivery_number,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.deliveries.approval')
                ->with('success', 'Delivery approved and stock updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve Delivery: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Only pending Deliveries can be rejected.');
        }

        $validated = $request->validate([
            'approval_notes' => 'required|string',
        ]);

        $delivery->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return redirect()->route('admin.deliveries.approval')
            ->with('success', 'Delivery rejected.');
    }
}
