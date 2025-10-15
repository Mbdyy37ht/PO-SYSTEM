<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderApprovalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PurchaseOrder::with(['supplier', 'warehouse', 'creator']);

            // Apply status filter - default to pending, but allow all if specified
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            } else {
                // Default: show only pending
                $query->where('status', 'pending');
            }

            // Apply PO number filter
            if ($request->has('po_number') && $request->po_number != '') {
                $query->where('po_number', 'like', '%' . $request->po_number . '%');
            }

            // Apply supplier filter
            if ($request->has('supplier') && $request->supplier != '') {
                $query->whereHas('supplier', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->supplier . '%');
                });
            }

            // Apply minimum amount filter
            if ($request->has('min_amount') && $request->min_amount != '') {
                $query->where('total_amount', '>=', $request->min_amount);
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
                    $reviewBtn = '<a href="' . route('admin.purchase-orders.show', $po) . '" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Review
                    </a>';

                    return '<div class="flex justify-end space-x-2">' . $reviewBtn . '</div>';
                })
                ->addColumn('po_info', function ($po) {
                    return '<div class="flex items-center">
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">' . $po->po_number . '</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">' . $po->created_at->diffForHumans() . '</div>
                        </div>
                    </div>';
                })
                ->editColumn('po_date', function ($po) {
                    return \Carbon\Carbon::parse($po->po_date)->format('d M Y');
                })
                ->addColumn('supplier_info', function ($po) {
                    return '<div class="text-sm text-gray-900 dark:text-gray-100">' . $po->supplier->name . '</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">' . $po->supplier->code . '</div>';
                })
                ->addColumn('warehouse_name', function ($po) {
                    return $po->warehouse->name;
                })
                ->addColumn('creator_info', function ($po) {
                    $initials = strtoupper(substr($po->creator->name, 0, 2));
                    return '<div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-800 dark:text-blue-300">' . $initials . '</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">' . $po->creator->name . '</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">' . $po->creator->email . '</div>
                        </div>
                    </div>';
                })
                ->editColumn('total_amount', function ($po) {
                    $amount = '<div class="text-sm font-bold text-gray-900 dark:text-gray-100">Rp ' . number_format($po->total_amount, 0, ',', '.') . '</div>';
                    if ($po->total_amount >= 10000000) {
                        $amount .= '<div class="text-xs text-yellow-600 dark:text-yellow-400">Requires Approval</div>';
                    }
                    return $amount;
                })
                ->addColumn('priority', function ($po) {
                    if ($po->total_amount >= 50000000) {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">🔥 Critical</span>';
                    } elseif ($po->total_amount >= 25000000) {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">⚡ High</span>';
                    } else {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">📋 Normal</span>';
                    }
                })
                ->addColumn('status', function ($po) {
                    $badges = [
                        'pending' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>',
                        'approved' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Approved</span>',
                        'rejected' => '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Rejected</span>',
                    ];
                    return $badges[$po->status] ?? $po->status;
                })
                ->rawColumns(['action', 'po_info', 'supplier_info', 'creator_info', 'total_amount', 'priority', 'status'])
                ->make(true);
        }

        // For statistics (non-AJAX request)
        $pendingCount = PurchaseOrder::where('status', 'pending')->count();
        $totalPendingAmount = PurchaseOrder::where('status', 'pending')->sum('total_amount');
        $avgPendingAmount = PurchaseOrder::where('status', 'pending')->avg('total_amount');
        $highPriorityCount = PurchaseOrder::where('status', 'pending')->where('total_amount', '>=', 25000000)->count();

        return view('admin.purchase-orders.approval', compact('pendingCount', 'totalPendingAmount', 'avgPendingAmount', 'highPriorityCount'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'warehouse', 'creator', 'details.item']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function approve(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return back()->with('error', 'Only pending Purchase Orders can be approved.');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null,
        ]);

        return redirect()->route('admin.purchase-orders.approval')
            ->with('success', 'Purchase Order approved successfully.');
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return back()->with('error', 'Only pending Purchase Orders can be rejected.');
        }

        $validated = $request->validate([
            'approval_notes' => 'required|string',
        ]);

        $purchaseOrder->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return redirect()->route('admin.purchase-orders.approval')
            ->with('success', 'Purchase Order rejected.');
    }
}
