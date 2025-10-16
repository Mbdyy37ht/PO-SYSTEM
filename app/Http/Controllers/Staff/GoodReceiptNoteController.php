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
use Yajra\DataTables\Facades\DataTables;

class GoodReceiptNoteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = GoodReceiptNote::with(['purchaseOrder.supplier', 'warehouse', 'creator']);

            // Apply status filter
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            // Apply PO number filter
            if ($request->has('po_number') && $request->po_number != '') {
                $query->whereHas('purchaseOrder', function($q) use ($request) {
                    $q->where('po_number', 'like', '%' . $request->po_number . '%');
                });
            }

            // Apply date range filter
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('grn_date', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('grn_date', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($grn) {
                    $viewBtn = '<a href="' . route('staff.good-receipt-notes.show', $grn) . '" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>';

                    // Only draft can be edited/deleted (NOT rejected)
                    if ($grn->status === 'draft') {
                        $editBtn = '<a href="' . route('staff.good-receipt-notes.edit', $grn) . '" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>';

                        $deleteBtn = '<form action="' . route('staff.good-receipt-notes.destroy', $grn) . '" method="POST" class="inline delete-form">
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
                ->editColumn('grn_date', function ($grn) {
                    return \Carbon\Carbon::parse($grn->grn_date)->format('d M Y');
                })
                ->addColumn('po_number', function ($grn) {
                    return $grn->purchaseOrder->po_number ?? '-';
                })
                ->addColumn('supplier_name', function ($grn) {
                    return $grn->purchaseOrder->supplier->name ?? '-';
                })
                ->addColumn('warehouse_name', function ($grn) {
                    return $grn->warehouse->name;
                })
                ->editColumn('status', function ($grn) {
                    $badges = [
                        'draft' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Draft</span>',
                        'pending' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>',
                        'approved' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Approved</span>',
                        'rejected' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Rejected</span>',
                    ];
                    return $badges[$grn->status] ?? $grn->status;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('staff.good-receipt-notes.index');
    }

    public function create()
    {
        // Show approved POs that:
        // 1. Don't have any GRN yet, OR
        // 2. Only have rejected GRNs (can create new GRN)
        // Exclude POs with approved or pending GRNs
        $purchaseOrders = PurchaseOrder::where('status', 'approved')
            ->whereDoesntHave('goodReceiptNotes', function($query) {
                $query->whereIn('status', ['approved', 'pending']);
            })
            ->with(['supplier', 'details.item'])
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
            'action' => 'required|in:draft,submit',
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

            // Determine status based on action
            if ($validated['action'] === 'draft') {
                $status = 'draft';
                $message = 'Good Receipt Note saved as draft successfully.';
            } else {
                $status = 'pending';
                $message = 'Good Receipt Note submitted for approval successfully.';
            }

            // Create GRN
            $goodReceiptNote = GoodReceiptNote::create([
                'grn_number' => $grn_number,
                'grn_date' => $validated['grn_date'],
                'purchase_order_id' => $validated['purchase_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'status' => $status,
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
                ->with('success', $message);
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
        // Only allow edit if status is draft (NOT rejected)
        if ($goodReceiptNote->status !== 'draft') {
            return back()->with('error', 'Only draft Good Receipt Notes can be edited. Rejected GRNs are read-only - please create a new GRN instead.');
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
        // Only allow update if status is draft (NOT rejected)
        if ($goodReceiptNote->status !== 'draft') {
            return back()->with('error', 'Only draft Good Receipt Notes can be updated. Rejected GRNs are read-only - please create a new GRN instead.');
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
            'action' => 'required|in:draft,submit',
        ]);

        DB::beginTransaction();
        try {
            // Determine status based on action
            if ($validated['action'] === 'draft') {
                $status = 'draft';
                $message = 'Good Receipt Note saved as draft successfully.';
            } else {
                $status = 'pending';
                $message = 'Good Receipt Note submitted for approval successfully.';
            }

            // Update GRN
            $goodReceiptNote->update([
                'grn_date' => $validated['grn_date'],
                'purchase_order_id' => $validated['purchase_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'status' => $status,
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
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update Good Receipt Note: ' . $e->getMessage());
        }
    }

    public function destroy(GoodReceiptNote $goodReceiptNote)
    {
        // Only allow delete if status is draft (NOT rejected)
        if ($goodReceiptNote->status !== 'draft') {
            return back()->with('error', 'Only draft Good Receipt Notes can be deleted. Rejected GRNs are read-only - please create a new GRN instead.');
        }

        $goodReceiptNote->delete();

        return redirect()->route('staff.good-receipt-notes.index')
            ->with('success', 'Good Receipt Note deleted successfully.');
    }
}
