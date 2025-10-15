<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'warehouse', 'creator'])
            ->latest()
            ->paginate(10);
        return view('staff.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        return view('staff.purchase-orders.create', compact('suppliers', 'warehouses', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'po_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'action' => 'required|in:draft,submit',
        ]);

        DB::beginTransaction();
        try {
            // Generate PO Number
            $lastPO = PurchaseOrder::whereYear('created_at', date('Y'))->latest()->first();
            $number = $lastPO ? intval(substr($lastPO->po_number, -5)) + 1 : 1;
            $po_number = 'PO-' . date('Y') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);

            // Calculate total amount
            $total_amount = 0;
            foreach ($validated['items'] as $item) {
                $total_amount += $item['quantity'] * $item['unit_price'];
            }

            // Determine status based on action
            if ($validated['action'] === 'draft') {
                // Save as draft - can be edited later
                $status = 'draft';
                $message = 'Purchase Order saved as draft successfully.';
            } else {
                // Submit - determine status based on amount
                // PO >= 10.000.000 harus disetujui Admin/Manager
                // PO < 10.000.000 auto-approved
                if ($total_amount >= 10000000) {
                    $status = 'pending';
                    $message = 'Purchase Order submitted for approval successfully.';
                } else {
                    $status = 'approved';
                    $message = 'Purchase Order approved automatically (< Rp 10,000,000).';
                }
            }

            // Create PO
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $po_number,
                'po_date' => $validated['po_date'],
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'total_amount' => $total_amount,
                'status' => $status,
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
                'approved_by' => $status === 'approved' ? Auth::id() : null,
                'approved_at' => $status === 'approved' ? now() : null,
            ]);

            // Create PO Details
            foreach ($validated['items'] as $item) {
                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.purchase-orders.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create Purchase Order: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'warehouse', 'creator', 'approver', 'details.item']);
        return view('staff.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        // Only allow edit if status is draft
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft Purchase Orders can be edited.');
        }

        $suppliers = Supplier::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $purchaseOrder->load('details.item');
        
        return view('staff.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'warehouses', 'items'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow update if status is draft
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft Purchase Orders can be updated.');
        }

        $validated = $request->validate([
            'po_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'action' => 'required|in:draft,submit',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total amount
            $total_amount = 0;
            foreach ($validated['items'] as $item) {
                $total_amount += $item['quantity'] * $item['unit_price'];
            }

            // Determine status based on action
            if ($validated['action'] === 'draft') {
                // Keep as draft
                $status = 'draft';
                $message = 'Purchase Order updated successfully.';
                $approved_by = null;
                $approved_at = null;
            } else {
                // Submit - determine status based on amount
                if ($total_amount >= 10000000) {
                    $status = 'pending';
                    $message = 'Purchase Order submitted for approval successfully.';
                    $approved_by = null;
                    $approved_at = null;
                } else {
                    $status = 'approved';
                    $message = 'Purchase Order approved automatically (< Rp 10,000,000).';
                    $approved_by = Auth::id();
                    $approved_at = now();
                }
            }

            // Update PO
            $purchaseOrder->update([
                'po_date' => $validated['po_date'],
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'total_amount' => $total_amount,
                'status' => $status,
                'notes' => $validated['notes'],
                'approved_by' => $approved_by,
                'approved_at' => $approved_at,
            ]);

            // Delete old details
            $purchaseOrder->details()->delete();

            // Create new details
            foreach ($validated['items'] as $item) {
                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.purchase-orders.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update Purchase Order: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Only allow delete if status is draft
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft Purchase Orders can be deleted.');
        }

        $purchaseOrder->delete();

        return redirect()->route('staff.purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully.');
    }
}
