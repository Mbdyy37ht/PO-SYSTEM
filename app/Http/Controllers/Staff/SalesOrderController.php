<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\ItemStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SalesOrder::with(['customer', 'warehouse', 'creator']);

            // Apply status filter
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            // Apply date range filter
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('so_date', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('so_date', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($po) {
                    $viewBtn = '<a href="' . route('staff.sales-orders.show', $po) . '" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>';

                    if ($po->status === 'draft') {
                        $editBtn = '<a href="' . route('staff.sales-orders.edit', $po) . '" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>';

                        $deleteBtn = '<form action="' . route('staff.sales-orders.destroy', $po) . '" method="POST" class="inline delete-form">
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
                ->editColumn('so_date', function ($po) {
                    return \Carbon\Carbon::parse($po->so_date)->format('d M Y');
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
                ->addColumn('customer_name', function ($po) {
                    return $po->customer->name;
                })
                ->addColumn('warehouse_name', function ($po) {
                    return $po->warehouse->name;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('staff.sales-orders.index');
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        return view('staff.sales-orders.create', compact('customers', 'warehouses', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'so_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
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
            // Generate SO Number
            $lastSO = SalesOrder::whereYear('created_at', date('Y'))->latest()->first();
            $number = $lastSO ? intval(substr($lastSO->so_number, -5)) + 1 : 1;
            $so_number = 'SO-' . date('Y') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);

            // Calculate total amount AND VALIDATE STOCK
            $total_amount = 0;
            $stockErrors = [];
            
            foreach ($validated['items'] as $index => $item) {
                $total_amount += $item['quantity'] * $item['unit_price'];
                
                // VALIDASI STOCK: qty tidak boleh lebih besar dari available stock
                // Hanya validasi jika bukan draft
                if ($validated['action'] !== 'draft') {
                    $stock = ItemStock::where('item_id', $item['item_id'])
                        ->where('warehouse_id', $validated['warehouse_id'])
                        ->first();
                    
                    $availableStock = $stock ? $stock->quantity : 0;
                    
                    if ($item['quantity'] > $availableStock) {
                        $itemName = Item::find($item['item_id'])->name;
                        $stockErrors[] = "{$itemName}: Requested {$item['quantity']}, Available {$availableStock}";
                    }
                }
            }
            
            // If stock validation fails, rollback and return error
            if (!empty($stockErrors)) {
                DB::rollBack();
                return back()->withInput()
                    ->with('error', 'Insufficient stock for the following items: ' . implode('; ', $stockErrors));
            }

            // Determine status based on action
            if ($validated['action'] === 'draft') {
                // Save as draft - can be edited later
                $status = 'draft';
                $message = 'Sales Order saved as draft successfully.';
            } else {
                // Submit - sales orders always need approval
                $status = 'pending';
                $message = 'Sales Order submitted for approval successfully.';
            }

            // Create SO
            $salesOrder = SalesOrder::create([
                'so_number' => $so_number,
                'so_date' => $validated['so_date'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'total_amount' => $total_amount,
                'status' => $status,
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create SO Details
            foreach ($validated['items'] as $item) {
                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.sales-orders.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create Sales Order: ' . $e->getMessage());
        }
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'warehouse', 'creator', 'approver', 'details.item']);
        return view('staff.sales-orders.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        // Only allow edit if status is draft
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'Only draft Sales Orders can be edited.');
        }

        $customers = Customer::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $salesOrder->load('details.item');
        
        return view('staff.sales-orders.edit', compact('salesOrder', 'customers', 'warehouses', 'items'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        // Only allow update if status is draft
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'Only draft Sales Orders can be updated.');
        }

        $validated = $request->validate([
            'so_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
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
            // Calculate total amount AND VALIDATE STOCK
            $total_amount = 0;
            $stockErrors = [];
            
            foreach ($validated['items'] as $index => $item) {
                $total_amount += $item['quantity'] * $item['unit_price'];
                
                // VALIDASI STOCK: qty tidak boleh lebih besar dari available stock
                // Hanya validasi jika bukan draft
                if ($validated['action'] !== 'draft') {
                    $stock = ItemStock::where('item_id', $item['item_id'])
                        ->where('warehouse_id', $validated['warehouse_id'])
                        ->first();
                    
                    $availableStock = $stock ? $stock->quantity : 0;
                    
                    if ($item['quantity'] > $availableStock) {
                        $itemName = Item::find($item['item_id'])->name;
                        $stockErrors[] = "{$itemName}: Requested {$item['quantity']}, Available {$availableStock}";
                    }
                }
            }
            
            // If stock validation fails, rollback and return error
            if (!empty($stockErrors)) {
                DB::rollBack();
                return back()->withInput()
                    ->with('error', 'Insufficient stock for the following items: ' . implode('; ', $stockErrors));
            }

            // Determine status based on action
            if ($validated['action'] === 'draft') {
                // Keep as draft
                $status = 'draft';
                $message = 'Sales Order updated successfully.';
            } else {
                // Submit - sales orders always need approval
                $status = 'pending';
                $message = 'Sales Order submitted for approval successfully.';
            }

            // Update SO
            $salesOrder->update([
                'so_date' => $validated['so_date'],
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'total_amount' => $total_amount,
                'status' => $status,
                'notes' => $validated['notes'],
            ]);

            // Delete old details
            $salesOrder->details()->delete();

            // Create new details
            foreach ($validated['items'] as $item) {
                SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('staff.sales-orders.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update Sales Order: ' . $e->getMessage());
        }
    }

    public function destroy(SalesOrder $salesOrder)
    {
        // Only allow delete if status is draft
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'Only draft Sales Orders can be deleted.');
        }

        $salesOrder->delete();

        return redirect()->route('staff.sales-orders.index')
            ->with('success', 'Sales Order deleted successfully.');
    }
}
