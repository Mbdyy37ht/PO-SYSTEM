<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with(['customer', 'warehouse', 'creator'])
            ->latest()
            ->paginate(10);
        return view('staff.sales-orders.index', compact('salesOrders'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        return view('staff.sales-orders.create', compact('customers', 'warehouses', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'so_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Generate SO Number
            $lastSO = SalesOrder::whereYear('created_at', date('Y'))->latest()->first();
            $number = $lastSO ? intval(substr($lastSO->so_number, -5)) + 1 : 1;
            $so_number = 'SO-' . date('Y') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);

            // Calculate total amount
            $total_amount = 0;
            foreach ($validated['items'] as $item) {
                $total_amount += $item['quantity'] * $item['unit_price'];
            }

            // Create SO with pending status (needs approval)
            $salesOrder = SalesOrder::create([
                'so_number' => $so_number,
                'so_date' => $validated['so_date'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'total_amount' => $total_amount,
                'status' => 'pending',
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create SO Details
            foreach ($validated['items'] as $item) {
                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.sales-orders.index')
                ->with('success', 'Sales Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create Sales Order: ' . $e->getMessage());
        }
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'warehouse', 'creator', 'approver', 'details.item']);
        return view('staff.sales-orders.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        // Only allow edit if status is draft
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'Only draft Sales Orders can be edited.');
        }

        $customers = Customer::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $salesOrder->load('details.item');
        
        return view('staff.sales-orders.edit', compact('salesOrder', 'customers', 'warehouses', 'items'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        // Only allow update if status is draft
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'Only draft Sales Orders can be updated.');
        }

        $validated = $request->validate([
            'so_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total amount
            $total_amount = 0;
            foreach ($validated['items'] as $item) {
                $total_amount += $item['quantity'] * $item['unit_price'];
            }

            // Update SO
            $salesOrder->update([
                'so_date' => $validated['so_date'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'total_amount' => $total_amount,
                'notes' => $validated['notes'],
            ]);

            // Delete old details
            $salesOrder->details()->delete();

            // Create new details
            foreach ($validated['items'] as $item) {
                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.sales-orders.index')
                ->with('success', 'Sales Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update Sales Order: ' . $e->getMessage());
        }
    }

    public function destroy(SalesOrder $salesOrder)
    {
        // Only allow delete if status is draft
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'Only draft Sales Orders can be deleted.');
        }

        $salesOrder->delete();

        return redirect()->route('staff.sales-orders.index')
            ->with('success', 'Sales Order deleted successfully.');
    }
}
