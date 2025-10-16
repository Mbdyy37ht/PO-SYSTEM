<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\GoodReceiptNote;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\ItemStock;
use App\Models\StockMovement;
use App\Models\DeliveryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Master Data Statistics
        $totalItems = Item::count();
        $totalSuppliers = Supplier::count();
        $totalWarehouses = Warehouse::count();
        $totalCustomers = Customer::count();

        // Pending Approvals
        $pendingPurchaseOrders = PurchaseOrder::where('status', 'pending')->count();
        $pendingSalesOrders = SalesOrder::where('status', 'pending')->count();
        $pendingGoodReceiptNotes = GoodReceiptNote::where('status', 'pending')->count();
        $pendingDeliveries = Delivery::where('status', 'pending')->count();

        // 1. LAPORAN STOK SAAT INI (per warehouse)
        $currentStock = ItemStock::with(['item', 'warehouse'])
            ->select('item_id', 'warehouse_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('item_id', 'warehouse_id')
            ->having('total_quantity', '>', 0)
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Stock Summary per Warehouse
        $stockPerWarehouse = ItemStock::with('warehouse')
            ->select('warehouse_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('COUNT(DISTINCT item_id) as total_items'))
            ->groupBy('warehouse_id')
            ->get();

        // 2. STOCK MOVEMENTS (last 7 days) - untuk stock card
        $recentMovements = StockMovement::with(['item', 'warehouse'])
            ->where('movement_date', '>=', Carbon::now()->subDays(7))
            ->orderBy('movement_date', 'desc')
            ->limit(10)
            ->get();

        // 3. TOP 5 PRODUK TERJUAL (30 hari terakhir)
        $topSellingProducts = DeliveryDetail::join('deliveries', 'delivery_details.delivery_id', '=', 'deliveries.id')
            ->join('items', 'delivery_details.item_id', '=', 'items.id')
            ->where('deliveries.status', 'approved')
            ->where('deliveries.approved_at', '>=', Carbon::now()->subDays(30))
            ->select(
                'items.id',
                'items.name',
                'items.code',
                DB::raw('SUM(delivery_details.quantity_delivered) as total_sold'),
                DB::raw('COUNT(DISTINCT deliveries.id) as transaction_count')
            )
            ->groupBy('items.id', 'items.name', 'items.code')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // 4. AGING STOCK (tidak keluar dalam 90 hari)
        $agingStock = ItemStock::with(['item', 'warehouse'])
            ->whereDoesntHave('item.deliveryDetails', function($query) {
                $query->whereHas('delivery', function($q) {
                    $q->where('status', 'approved')
                      ->where('approved_at', '>=', Carbon::now()->subDays(90));
                });
            })
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'desc')
            ->limit(10)
            ->get();

        // Stock Statistics
        $totalStockItems = ItemStock::sum('quantity');
        $lowStockItems = ItemStock::where('quantity', '<=', 10)->count();
        $outOfStockItems = ItemStock::where('quantity', '=', 0)->count();

        // Monthly Transaction Trends (last 6 months)
        $monthlyPurchases = PurchaseOrder::where('status', 'approved')
            ->where('approved_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(approved_at) as month, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('month')
            ->get();

        $monthlySales = SalesOrder::where('status', 'approved')
            ->where('approved_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(approved_at) as month, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'totalItems',
            'totalSuppliers',
            'totalWarehouses',
            'totalCustomers',
            'pendingPurchaseOrders',
            'pendingSalesOrders',
            'pendingGoodReceiptNotes',
            'pendingDeliveries',
            'currentStock',
            'stockPerWarehouse',
            'recentMovements',
            'topSellingProducts',
            'agingStock',
            'totalStockItems',
            'lowStockItems',
            'outOfStockItems',
            'monthlyPurchases',
            'monthlySales'
        ));
    }
}
