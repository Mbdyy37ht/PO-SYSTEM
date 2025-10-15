<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodReceiptNote;
use App\Models\ItemStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoodReceiptNoteApprovalController extends Controller
{
    public function index()
    {
        $goodReceiptNotes = GoodReceiptNote::with(['purchaseOrder', 'warehouse', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
        return view('admin.good-receipt-notes.approval', compact('goodReceiptNotes'));
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

                // Increase stock
                $itemStock->increment('quantity', $detail->quantity_received);

                // Create stock movement record
                StockMovement::create([
                    'item_id' => $detail->item_id,
                    'warehouse_id' => $goodReceiptNote->warehouse_id,
                    'reference_type' => 'GoodReceiptNote',
                    'reference_id' => $goodReceiptNote->id,
                    'movement_type' => 'in',
                    'quantity' => $detail->quantity_received,
                    'notes' => 'Stock in from GRN: ' . $goodReceiptNote->grn_number,
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
