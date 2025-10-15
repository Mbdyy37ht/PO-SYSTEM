<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\GoodReceiptNote;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // My Transaction Statistics
        $myPurchaseOrders = PurchaseOrder::where('created_by', $userId)->count();
        $mySalesOrders = SalesOrder::where('created_by', $userId)->count();
        $myGoodReceiptNotes = GoodReceiptNote::where('created_by', $userId)->count();
        $myDeliveries = Delivery::where('created_by', $userId)->count();

        // Pending Transactions
        $pendingPurchaseOrders = PurchaseOrder::where('created_by', $userId)
            ->where('status', 'pending')
            ->count();
        $pendingSalesOrders = SalesOrder::where('created_by', $userId)
            ->where('status', 'pending')
            ->count();
        $pendingGoodReceiptNotes = GoodReceiptNote::where('created_by', $userId)
            ->where('status', 'pending')
            ->count();
        $pendingDeliveries = Delivery::where('created_by', $userId)
            ->where('status', 'pending')
            ->count();

        // Recent My Activities
        $recentPurchaseOrders = PurchaseOrder::with(['supplier', 'warehouse'])
            ->where('created_by', $userId)
            ->latest()
            ->take(5)
            ->get();
        
        $recentSalesOrders = SalesOrder::with(['customer', 'warehouse'])
            ->where('created_by', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('staff.dashboard', compact(
            'myPurchaseOrders',
            'mySalesOrders',
            'myGoodReceiptNotes',
            'myDeliveries',
            'pendingPurchaseOrders',
            'pendingSalesOrders',
            'pendingGoodReceiptNotes',
            'pendingDeliveries',
            'recentPurchaseOrders',
            'recentSalesOrders'
        ));
    }
}
