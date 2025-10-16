<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\ItemStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DeliveryApprovalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Delivery::with(['salesOrder.customer', 'warehouse', 'creator']);

            // Apply status filter - default to pending
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            } else {
                $query->where('status', 'pending');
            }

            // Apply Delivery number filter
            if ($request->has('delivery_number') && $request->delivery_number != '') {
                $query->where('delivery_number', 'like', '%' . $request->delivery_number . '%');
            }

            // Apply PO number filter
            if ($request->has('so_number') && $request->so_number != '') {
                $query->whereHas('salesOrder', function($q) use ($request) {
                    $q->where('so_number', 'like', '%' . $request->so_number . '%');
                });
            }

            // Apply date range filter
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('delivery_date', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('delivery_date', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($grn) {
                    $reviewBtn = '<a href="' . route('admin.deliveries.show', $grn) . '" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
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
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">' . $grn->delivery_number . '</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">' . $grn->created_at->diffForHumans() . '</div>
                        </div>
                    </div>';
                })
                ->editColumn('delivery_date', function ($grn) {
                    return \Carbon\Carbon::parse($grn->delivery_date)->format('d M Y');
                })
                ->addColumn('po_info', function ($grn) {
                    return '<div class="text-sm text-gray-900 dark:text-gray-100">' . ($grn->salesOrder->so_number ?? '-') . '</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">' . \Carbon\Carbon::parse($grn->salesOrder->po_date)->format('d M Y') . '</div>';
                })
                ->addColumn('customer_name', function ($grn) {
                    return $grn->salesOrder->customer->name ?? '-';
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
                    $totalDelivered = $grn->details->sum('quantity_delivered');
                    return '<div class="text-sm font-medium text-gray-900 dark:text-gray-100">Ordered: ' . number_format($totalOrdered) . '</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Delivered: ' . number_format($totalDelivered) . '</div>';
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
        $pendingCount = Delivery::where('status', 'pending')->count();
        
        return view('admin.deliveries.approval', compact('pendingCount'));
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['salesOrder.customer', 'warehouse', 'creator', 'details.item']);
        return view('admin.deliveries.show', compact('delivery'));
    }

    public function approve(Request $request, Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Only pending Deliverys can be approved.');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // VALIDASI STOCK sebelum approve - qty delivered tidak boleh melebihi stock
            $delivery->load(['details.item', 'warehouse']);
            $stockErrors = [];
            
            foreach ($delivery->details as $detail) {
                if ($detail->quantity_delivered > 0) {
                    $stock = ItemStock::where('item_id', $detail->item_id)
                        ->where('warehouse_id', $delivery->warehouse_id)
                        ->first();
                    
                    $availableStock = $stock ? $stock->quantity : 0;
                    
                    if ($detail->quantity_delivered > $availableStock) {
                        $stockErrors[] = "{$detail->item->name}: Delivering {$detail->quantity_delivered}, Available {$availableStock}";
                    }
                }
            }
            
            // If stock validation fails, return error
            if (!empty($stockErrors)) {
                DB::rollBack();
                return back()->with('error', 'Cannot approve. Insufficient stock: ' . implode('; ', $stockErrors));
            }

            // Update Delivery status
            $delivery->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            // KURANGI stock for each item (DELIVERY = OUT)
            foreach ($delivery->details as $detail) {
                if ($detail->quantity_delivered > 0) {
                    // Find item stock
                    $itemStock = ItemStock::where('item_id', $detail->item_id)
                        ->where('warehouse_id', $delivery->warehouse_id)
                        ->firstOrFail();

                    // Get stock before
                    $stockBefore = $itemStock->quantity;

                    // DECREASE stock (DELIVERY OUT)
                    $itemStock->decrement('quantity', $detail->quantity_delivered);

                    // Get stock after
                    $stockAfter = $itemStock->quantity;

                    // Create stock movement record
                    StockMovement::create([
                        'item_id' => $detail->item_id,
                        'warehouse_id' => $delivery->warehouse_id,
                        'reference_type' => 'Delivery',
                        'reference_id' => $delivery->id,
                        'reference_number' => $delivery->delivery_number,
                        'movement_type' => 'out',
                        'quantity' => $detail->quantity_delivered,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'notes' => 'Stock out from Delivery: ' . $delivery->delivery_number,
                        'created_by' => Auth::id(),
                        'movement_date' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.deliveries.approval')
                ->with('success', 'Delivery approved and stock reduced successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve Delivery: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Delivery $delivery)
    {
        if ($delivery->status !== 'pending') {
            return back()->with('error', 'Only pending Deliverys can be rejected.');
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
