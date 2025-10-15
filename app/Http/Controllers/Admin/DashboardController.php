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
use Illuminate\Http\Request;

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

        // Recent Activities
        $recentPurchaseOrders = PurchaseOrder::with(['supplier', 'creator'])
            ->latest()
            ->take(5)
            ->get();
        
        $recentSalesOrders = SalesOrder::with(['customer', 'creator'])
            ->latest()
            ->take(5)
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
            'recentPurchaseOrders',
            'recentSalesOrders'
        ));
    }
}
