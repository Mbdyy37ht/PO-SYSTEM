<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodReceiptNote;
use App\Models\ItemStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GoodReceiptNoteApprovalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = GoodReceiptNote::with(['purchaseOrder.supplier', 'warehouse', 'creator']);

            // Apply status filter - default to pending
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            } else {
                $query->where('status', 'pending');
            }

            // Apply GRN number filter
            if ($request->has('grn_number') && $request->grn_number != '') {
                $query->where('grn_number', 'like', '%' . $request->grn_number . '%');
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
                    $reviewBtn = '<a href="' . route('admin.good-receipt-notes.show', $grn) . '" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Review
                    </a>';

                    return '<div class="flex justify-end space-x-2">' . $reviewBtn . '</div>';
                })
                ->addColumn('grn_info', function ($grn) {
                    return '<div class="flex items-center">
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">' . $grn->grn_number . '</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">' . $grn->created_at->diffForHumans() . '</div>
                        </div>
                    </div>';
                })
                ->editColumn('grn_date', function ($grn) {
                    return \Carbon\Carbon::parse($grn->grn_date)->format('d M Y');
                })
                ->addColumn('po_info', function ($grn) {
                    return '<div class="text-sm text-gray-900 dark:text-gray-100">' . ($grn->purchaseOrder->po_number ?? '-') . '</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">' . \Carbon\Carbon::parse($grn->purchaseOrder->po_date)->format('d M Y') . '</div>';
                })
                ->addColumn('supplier_name', function ($grn) {
                    return $grn->purchaseOrder->supplier->name ?? '-';
                })
                ->addColumn('warehouse_name', function ($grn) {
                    return $grn->warehouse->name;
                })
                ->addColumn('creator_info', function ($grn) {
                    return '<div class="text-sm font-medium text-gray-900 dark:text-gray-100">' . $grn->creator->name . '</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">' . $grn->creator->email . '</div>';
                })
                ->addColumn('total_items', function ($grn) {
                    $totalOrdered = $grn->details->sum('quantity_ordered');
                    $totalReceived = $grn->details->sum('quantity_received');
                    return '<div class="text-sm font-medium text-gray-900 dark:text-gray-100">Ordered: ' . number_format($totalOrdered) . '</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Received: ' . number_format($totalReceived) . '</div>';
                })
                ->editColumn('status', function ($grn) {
                    $badges = [
                        'pending' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>',
                        'approved' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Approved</span>',
                        'rejected' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Rejected</span>',
                    ];
                    return $badges[$grn->status] ?? $grn->status;
                })
                ->rawColumns(['action', 'grn_info', 'po_info', 'creator_info', 'total_items', 'status'])
                ->make(true);
        }

        // Statistics
        $pendingCount = GoodReceiptNote::where('status', 'pending')->count();
        
        return view('admin.good-receipt-notes.approval', compact('pendingCount'));
    }

    public function show(GoodReceiptNote $goodReceiptNote)
    {
        $goodReceiptNote->load(['purchaseOrder.supplier', 'warehouse', 'creator', 'details.item']);
        return view('admin.good-receipt-notes.show', compact('goodReceiptNote'));
    }

    public function approve(Request $request, GoodReceiptNote $goodReceiptNote)
    {
        if ($goodReceiptNote->status !== 'pending') {
            return back()->with('error', 'Only pending Good Receipt Notes can be approved.');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update GRN status
            $goodReceiptNote->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            // Update stock for each item
            foreach ($goodReceiptNote->details as $detail) {
                // Find or create item stock
                $itemStock = ItemStock::firstOrCreate(
                    [
                        'item_id' => $detail->item_id,
                        'warehouse_id' => $goodReceiptNote->warehouse_id,
                    ],
                    [
                        'quantity' => 0,
                    ]
                );

                // Get stock before
                $stockBefore = $itemStock->quantity;

                // Increase stock
                $itemStock->increment('quantity', $detail->quantity_received);

                // Get stock after
                $stockAfter = $itemStock->quantity;

                // Create stock movement record
                StockMovement::create([
                    'item_id' => $detail->item_id,
                    'warehouse_id' => $goodReceiptNote->warehouse_id,
                    'reference_type' => 'GoodReceiptNote',
                    'reference_id' => $goodReceiptNote->id,
                    'reference_number' => $goodReceiptNote->grn_number,
                    'movement_type' => 'in',
                    'quantity' => $detail->quantity_received,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => 'Stock in from GRN: ' . $goodReceiptNote->grn_number,
                    'created_by' => Auth::id(),
                    'movement_date' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.good-receipt-notes.approval')
                ->with('success', 'Good Receipt Note approved and stock updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve Good Receipt Note: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, GoodReceiptNote $goodReceiptNote)
    {
        if ($goodReceiptNote->status !== 'pending') {
            return back()->with('error', 'Only pending Good Receipt Notes can be rejected.');
        }

        $validated = $request->validate([
            'approval_notes' => 'required|string',
        ]);

        $goodReceiptNote->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return redirect()->route('admin.good-receipt-notes.approval')
            ->with('success', 'Good Receipt Note rejected.');
    }
}
