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
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PurchaseOrder::with(['supplier', 'warehouse', 'creator']);

            // Apply status filter
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            // Apply date range filter
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('po_date', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('po_date', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($po) {
                    $viewBtn = '<a href="' . route('staff.purchase-orders.show', $po) . '" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>';

                    if ($po->status === 'draft') {
                        $editBtn = '<a href="' . route('staff.purchase-orders.edit', $po) . '" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>';

                        $deleteBtn = '<form action="' . route('staff.purchase-orders.destroy', $po) . '" method="POST" class="inline delete-form">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>';

                        return '<div class="flex justify-end space-x-2">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
                    }

                    return '<div class="flex justify-end space-x-2">' . $viewBtn . '</div>';
                })
                ->editColumn('po_date', function ($po) {
                    return \Carbon\Carbon::parse($po->po_date)->format('d M Y');
                })
                ->editColumn('total_amount', function ($po) {
                    return 'Rp ' . number_format($po->total_amount, 0, ',', '.');
                })
                ->editColumn('status', function ($po) {
                    $badges = [
                        'draft' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Draft</span>',
                        'pending' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>',
                        'approved' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Approved</span>',
                        'rejected' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Rejected</span>',
                    ];
                    return $badges[$po->status] ?? $po->status;
                })
                ->addColumn('supplier_name', function ($po) {
                    return $po->supplier->name;
                })
                ->addColumn('warehouse_name', function ($po) {
                    return $po->warehouse->name;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('staff.purchase-orders.index');
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
