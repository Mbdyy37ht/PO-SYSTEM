<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\GoodReceiptNote;
use App\Models\GoodReceiptNoteDetail;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GoodReceiptNoteController extends Controller
{
    public function index()
    {
        $goodReceiptNotes = GoodReceiptNote::with(['purchaseOrder', 'warehouse', 'creator'])
            ->latest()
            ->paginate(10);
        return view('staff.good-receipt-notes.index', compact('goodReceiptNotes'));
    }

    public function create()
    {
        // Only show approved POs that don't have GRN yet or have rejected GRN
        $purchaseOrders = PurchaseOrder::where('status', 'approved')
            ->whereDoesntHave('goodReceiptNotes', function($query) {
                $query->where('status', 'approved');
            })
            ->with('supplier')
            ->get();
        
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('staff.good-receipt-notes.create', compact('purchaseOrders', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grn_date' => 'required|date',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity_ordered' => 'required|integer|min:0',
            'items.*.quantity_received' => 'required|integer|min:0',
        ]);

        // Validate PO is approved
        $purchaseOrder = PurchaseOrder::findOrFail($validated['purchase_order_id']);
        if ($purchaseOrder->status !== 'approved') {
            return back()->with('error', 'Only approved Purchase Orders can create Good Receipt Note.');
        }

        DB::beginTransaction();
        try {
            // Generate GRN Number
            $lastGRN = GoodReceiptNote::whereYear('created_at', date('Y'))->latest()->first();
            $number = $lastGRN ? intval(substr($lastGRN->grn_number, -5)) + 1 : 1;
            $grn_number = 'GRN-' . date('Y') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);

            // Create GRN
            $goodReceiptNote = GoodReceiptNote::create([
                'grn_number' => $grn_number,
                'grn_date' => $validated['grn_date'],
                'purchase_order_id' => $validated['purchase_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'status' => 'pending',
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create GRN Details
            foreach ($validated['items'] as $item) {
                GoodReceiptNoteDetail::create([
                    'good_receipt_note_id' => $goodReceiptNote->id,
                    'item_id' => $item['item_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => $item['quantity_received'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.good-receipt-notes.index')
                ->with('success', 'Good Receipt Note created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create Good Receipt Note: ' . $e->getMessage());
        }
    }

    public function show(GoodReceiptNote $goodReceiptNote)
    {
        $goodReceiptNote->load(['purchaseOrder.supplier', 'warehouse', 'creator', 'approver', 'details.item']);
        return view('staff.good-receipt-notes.show', compact('goodReceiptNote'));
    }

    public function edit(GoodReceiptNote $goodReceiptNote)
    {
        // Only allow edit if status is draft or rejected
        if (!in_array($goodReceiptNote->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected Good Receipt Notes can be edited.');
        }

        $purchaseOrders = PurchaseOrder::where('status', 'approved')
            ->with('supplier')
            ->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $goodReceiptNote->load('details.item', 'purchaseOrder.details.item');
        
        return view('staff.good-receipt-notes.edit', compact('goodReceiptNote', 'purchaseOrders', 'warehouses'));
    }

    public function update(Request $request, GoodReceiptNote $goodReceiptNote)
    {
        // Only allow update if status is draft or rejected
        if (!in_array($goodReceiptNote->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected Good Receipt Notes can be updated.');
        }

        $validated = $request->validate([
            'grn_date' => 'required|date',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity_ordered' => 'required|integer|min:0',
            'items.*.quantity_received' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update GRN
            $goodReceiptNote->update([
                'grn_date' => $validated['grn_date'],
                'purchase_order_id' => $validated['purchase_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]);

            // Delete old details
            $goodReceiptNote->details()->delete();

            // Create new details
            foreach ($validated['items'] as $item) {
                GoodReceiptNoteDetail::create([
                    'good_receipt_note_id' => $goodReceiptNote->id,
                    'item_id' => $item['item_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => $item['quantity_received'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.good-receipt-notes.index')
                ->with('success', 'Good Receipt Note updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update Good Receipt Note: ' . $e->getMessage());
        }
    }

    public function destroy(GoodReceiptNote $goodReceiptNote)
    {
        // Only allow delete if status is draft or rejected
        if (!in_array($goodReceiptNote->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected Good Receipt Notes can be deleted.');
        }

        $goodReceiptNote->delete();

        return redirect()->route('staff.good-receipt-notes.index')
            ->with('success', 'Good Receipt Note deleted successfully.');
    }
}
