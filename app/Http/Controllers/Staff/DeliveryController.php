<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryDetail;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\ItemStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['salesOrder', 'warehouse', 'creator'])
            ->latest()
            ->paginate(10);
        return view('staff.deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        // Only show approved SOs that don't have Delivery yet or have rejected Delivery
        $salesOrders = SalesOrder::where('status', 'approved')
            ->whereDoesntHave('deliveries', function($query) {
                $query->where('status', 'approved');
            })
            ->with('customer')
            ->get();
        
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('staff.deliveries.create', compact('salesOrders', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'sales_order_id' => 'required|exists:sales_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity_ordered' => 'required|integer|min:0',
            'items.*.quantity_delivered' => 'required|integer|min:0',
        ]);

        // Validate SO is approved
        $salesOrder = SalesOrder::findOrFail($validated['sales_order_id']);
        if ($salesOrder->status !== 'approved') {
            return back()->with('error', 'Only approved Sales Orders can create Delivery.');
        }

        // Validate stock availability
        foreach ($validated['items'] as $item) {
            $stock = ItemStock::where('item_id', $item['item_id'])
                ->where('warehouse_id', $validated['warehouse_id'])
                ->first();
            
            if (!$stock || $stock->quantity < $item['quantity_delivered']) {
                return back()->with('error', 'Insufficient stock for item ID: ' . $item['item_id']);
            }
        }

        DB::beginTransaction();
        try {
            // Generate Delivery Number
            $lastDelivery = Delivery::whereYear('created_at', date('Y'))->latest()->first();
            $number = $lastDelivery ? intval(substr($lastDelivery->delivery_number, -5)) + 1 : 1;
            $delivery_number = 'DEL-' . date('Y') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);

            // Create Delivery
            $delivery = Delivery::create([
                'delivery_number' => $delivery_number,
                'delivery_date' => $validated['delivery_date'],
                'sales_order_id' => $validated['sales_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'status' => 'pending',
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create Delivery Details
            foreach ($validated['items'] as $item) {
                DeliveryDetail::create([
                    'delivery_id' => $delivery->id,
                    'item_id' => $item['item_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_delivered' => $item['quantity_delivered'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.deliveries.index')
                ->with('success', 'Delivery created successfully and pending approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create Delivery: ' . $e->getMessage());
        }
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['salesOrder.customer', 'warehouse', 'creator', 'approver', 'details.item']);
        return view('staff.deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        // Only allow edit if status is draft or rejected
        if (!in_array($delivery->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected Deliveries can be edited.');
        }

        $salesOrders = SalesOrder::where('status', 'approved')
            ->with('customer')
            ->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $delivery->load('details.item', 'salesOrder.details.item');
        
        return view('staff.deliveries.edit', compact('delivery', 'salesOrders', 'warehouses'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        // Only allow update if status is draft or rejected
        if (!in_array($delivery->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected Deliveries can be updated.');
        }

        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'sales_order_id' => 'required|exists:sales_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity_ordered' => 'required|integer|min:0',
            'items.*.quantity_delivered' => 'required|integer|min:0',
        ]);

        // Validate stock availability
        foreach ($validated['items'] as $item) {
            $stock = ItemStock::where('item_id', $item['item_id'])
                ->where('warehouse_id', $validated['warehouse_id'])
                ->first();
            
            if (!$stock || $stock->quantity < $item['quantity_delivered']) {
                return back()->with('error', 'Insufficient stock for item ID: ' . $item['item_id']);
            }
        }

        DB::beginTransaction();
        try {
            // Update Delivery
            $delivery->update([
                'delivery_date' => $validated['delivery_date'],
                'sales_order_id' => $validated['sales_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]);

            // Delete old details
            $delivery->details()->delete();

            // Create new details
            foreach ($validated['items'] as $item) {
                DeliveryDetail::create([
                    'delivery_id' => $delivery->id,
                    'item_id' => $item['item_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_delivered' => $item['quantity_delivered'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.deliveries.index')
                ->with('success', 'Delivery updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update Delivery: ' . $e->getMessage());
        }
    }

    public function destroy(Delivery $delivery)
    {
        // Only allow delete if status is draft or rejected
        if (!in_array($delivery->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected Deliveries can be deleted.');
        }

        $delivery->delete();

        return redirect()->route('staff.deliveries.index')
            ->with('success', 'Delivery deleted successfully.');
    }
}
