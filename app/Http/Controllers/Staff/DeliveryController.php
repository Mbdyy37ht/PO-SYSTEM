<?php

namespace App\Http\Controllers\Staff;

use AppModelsItem;
use App\Models\Item;
use AppModelsItemStock;
use App\Models\Delivery;
use App\Models\ItemStock;
use App\Models\Warehouse;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use App\Models\DeliveryDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Delivery::with(['salesOrder.customer', 'warehouse', 'creator']);

            // Apply status filter
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
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
                    $viewBtn = '<a href="' . route('staff.deliveries.show', $grn) . '" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>';

                    // Only draft can be edited/deleted (NOT rejected)
                    if ($grn->status === 'draft') {
                        $editBtn = '<a href="' . route('staff.deliveries.edit', $grn) . '" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>';

                        $deleteBtn = '<form action="' . route('staff.deliveries.destroy', $grn) . '" method="POST" class="inline delete-form">
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
                ->editColumn('delivery_date', function ($grn) {
                    return \Carbon\Carbon::parse($grn->delivery_date)->format('d M Y');
                })
                ->addColumn('so_number', function ($grn) {
                    return $grn->salesOrder->so_number ?? '-';
                })
                ->addColumn('customer_name', function ($grn) {
                    return $grn->salesOrder->customer->name ?? '-';
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

        return view('staff.deliveries.index');
    }

    public function create()
    {
        // Show approved POs that:
        // 1. Don't have any Delivery yet, OR
        // 2. Only have rejected GRNs (can create new GRN)
        // Exclude POs with approved or pending GRNs
        $salesOrders = SalesOrder::where('status', 'approved')
            ->whereDoesntHave('deliveries', function($query) {
                $query->whereIn('status', ['approved', 'pending']);
            })
            ->with(['customer', 'details.item'])
            ->get();

        $warehouses = Warehouse::where('is_active', true)->get();
        return view('staff.deliveries.create', compact('salesOrders', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'sales_order_id' => 'required|exists:sales_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity_ordered' => 'required|integer|min:0',
            'items.*.quantity_delivered' => 'required|integer|min:0',
            'action' => 'required|in:draft,submit',
        ]);

        // Validate SO is approved
        $salesOrder = SalesOrder::findOrFail($validated['sales_order_id']);
        if ($salesOrder->status !== 'approved') {
            return back()->with('error', 'Only approved Sales Orders can create Delivery.');
        }

        DB::beginTransaction();
        try {
            // Generate Delivery Number
            $lastDelivery = Delivery::whereYear('created_at', date('Y'))->latest()->first();
            $number = $lastDelivery ? intval(substr($lastDelivery->delivery_number, -5)) + 1 : 1;
            $delivery_number = 'DEL-' . date('Y') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);

            // VALIDASI STOCK - qty delivered tidak boleh melebihi available stock
            $stockErrors = [];
            if ($validated['action'] !== 'draft') {
                foreach ($validated['items'] as $item) {
                    if ($item['quantity_delivered'] > 0) {
                        $stock = ItemStock::where('item_id', $item['item_id'])
                            ->where('warehouse_id', $validated['warehouse_id'])
                            ->first();

                        $availableStock = $stock ? $stock->quantity : 0;

                        if ($item['quantity_delivered'] > $availableStock) {
                            $itemName = Item::find($item['item_id'])->name;
                            $stockErrors[] = "{$itemName}: Requested {$item['quantity_delivered']}, Available {$availableStock}";
                        }
                    }
                }
            }

            // If stock validation fails, rollback and return error
            if (!empty($stockErrors)) {
                DB::rollBack();
                return back()->withInput()
                    ->with('error', 'Insufficient stock for delivery: ' . implode('; ', $stockErrors));
            }

            // Determine status based on action
            if ($validated['action'] === 'draft') {
                $status = 'draft';
                $message = 'Delivery saved as draft successfully.';
            } else {
                $status = 'pending';
                $message = 'Delivery submitted for approval successfully.';
            }

            // Create Delivery
            $delivery = Delivery::create([
                'delivery_number' => $delivery_number,
                'delivery_date' => $validated['delivery_date'],
                'sales_order_id' => $validated['sales_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'status' => $status,
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create Delivery Details
            foreach ($validated['items'] as $item) {
                DeliveryDetail::create([
                    'delivery_id' => $delivery->id,
                    'item_id' => $item['item_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_delivered' => $item['quantity_delivered'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.deliveries.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create Delivery: ' . $e->getMessage());
        }
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['salesOrder.customer', 'warehouse', 'creator', 'approver', 'details.item']);
        return view('staff.deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        // Only allow edit if status is draft (NOT rejected)
        if ($delivery->status !== 'draft') {
            return back()->with('error', 'Only draft Deliverys can be edited. Rejected GRNs are read-only - please create a new Delivery instead.');
        }

        $salesOrders = SalesOrder::where('status', 'approved')
            ->with('customer')
            ->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $delivery->load('details.item', 'salesOrder.details.item');

        return view('staff.deliveries.edit', compact('delivery', 'salesOrders', 'warehouses'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        // Only allow update if status is draft (NOT rejected)
        if ($delivery->status !== 'draft') {
            return back()->with('error', 'Only draft Deliverys can be updated. Rejected GRNs are read-only - please create a new Delivery instead.');
        }

        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'sales_order_id' => 'required|exists:sales_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity_ordered' => 'required|integer|min:0',
            'items.*.quantity_delivered' => 'required|integer|min:0',
            'action' => 'required|in:draft,submit',
        ]);

        DB::beginTransaction();
        try {
            // Determine status based on action
            if ($validated['action'] === 'draft') {
                $status = 'draft';
                $message = 'Delivery saved as draft successfully.';
            } else {
                $status = 'pending';
                $message = 'Delivery submitted for approval successfully.';
            }

            // Update GRN
            $delivery->update([
                'delivery_date' => $validated['delivery_date'],
                'sales_order_id' => $validated['sales_order_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'status' => $status,
                'notes' => $validated['notes'],
            ]);

            // Delete old details
            $delivery->details()->delete();

            // Create new details
            foreach ($validated['items'] as $item) {
                DeliveryDetail::create([
                    'delivery_id' => $delivery->id,
                    'item_id' => $item['item_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_delivered' => $item['quantity_delivered'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.deliveries.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update Delivery: ' . $e->getMessage());
        }
    }

    public function destroy(Delivery $delivery)
    {
        // Only allow delete if status is draft (NOT rejected)
        if ($delivery->status !== 'draft') {
            return back()->with('error', 'Only draft Deliverys can be deleted. Rejected GRNs are read-only - please create a new Delivery instead.');
        }

        $delivery->delete();

        return redirect()->route('staff.deliveries.index')
            ->with('success', 'Delivery deleted successfully.');
    }
}
