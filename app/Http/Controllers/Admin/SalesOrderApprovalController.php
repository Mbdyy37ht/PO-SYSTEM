<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesOrderApprovalController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with(['customer', 'warehouse', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
        return view('admin.sales-orders.approval', compact('salesOrders'));
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'warehouse', 'creator', 'details.item']);
        return view('admin.sales-orders.show', compact('salesOrder'));
    }

    public function approve(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'pending') {
            return back()->with('error', 'Only pending Sales Orders can be approved.');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $salesOrder->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null,
        ]);

        return redirect()->route('admin.sales-orders.approval')
            ->with('success', 'Sales Order approved successfully.');
    }

    public function reject(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'pending') {
            return back()->with('error', 'Only pending Sales Orders can be rejected.');
        }

        $validated = $request->validate([
            'approval_notes' => 'required|string',
        ]);

        $salesOrder->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return redirect()->route('admin.sales-orders.approval')
            ->with('success', 'Sales Order rejected.');
    }
}
