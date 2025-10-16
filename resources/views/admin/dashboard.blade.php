<x-app-layout>
    <x-slot name="header">
        Admin Dashboard
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Welcome Message -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-6 text-white">
                <h3 class="text-xl sm:text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h3>
                <p class="text-sm sm:text-base text-blue-100">Here's your warehouse management overview</p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Stock -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Total Stock</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalStockItems) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Units</p>
                    </div>
                    <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Low Stock</p>
                        <p class="text-xl sm:text-2xl font-bold text-yellow-600 dark:text-yellow-500">{{ $lowStockItems }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Items ≤ 10</p>
                    </div>
                    <div class="p-2 sm:p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Pending</p>
                        <p class="text-xl sm:text-2xl font-bold text-orange-600 dark:text-orange-500">{{ $pendingPurchaseOrders + $pendingSalesOrders + $pendingGoodReceiptNotes + $pendingDeliveries }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Approvals</p>
                    </div>
                    <div class="p-2 sm:p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Out of Stock -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Out of Stock</p>
                        <p class="text-xl sm:text-2xl font-bold text-red-600 dark:text-red-500">{{ $outOfStockItems }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Items = 0</p>
                    </div>
                    <div class="p-2 sm:p-3 bg-red-100 dark:bg-red-900 rounded-full">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Summary per Warehouse -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">📊 Stock Summary per Warehouse</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $totalAllStock = 0;
                @endphp
                @forelse($stockPerWarehouse as $warehouseStock)
                    @php
                        $totalAllStock += $warehouseStock->total_quantity;
                    @endphp
                    <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-blue-500 dark:hover:border-blue-500 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ $warehouseStock->warehouse->name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $warehouseStock->warehouse->address ?? 'N/A' }}</p>
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600 dark:text-gray-400">Total Stock:</span>
                                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ number_format($warehouseStock->total_quantity) }} units</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600 dark:text-gray-400">Total Items:</span>
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ number_format($warehouseStock->total_items) }} types</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full ml-3">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                        No warehouse stock data available
                    </div>
                @endforelse
                
                <!-- Total All Warehouses -->
                <div class="col-span-full border-2 border-green-500 dark:border-green-600 bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-green-900 dark:text-green-100 text-lg">Total All Warehouses</h4>
                            <p class="text-xs text-green-700 dark:text-green-300">Combined stock across all locations</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalAllStock) }}</p>
                            <p class="text-xs text-green-700 dark:text-green-300">Total Units</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Reports Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            
            <!-- 1. LAPORAN STOK SAAT INI -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">📦 Current Stock Report</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Top 10</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase pb-2">Item</th>
                                <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase pb-2">Warehouse</th>
                                <th class="text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase pb-2">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($currentStock as $stock)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="py-2 text-xs sm:text-sm">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $stock->item->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stock->item->code }}</div>
                                </td>
                                <td class="py-2 text-xs sm:text-sm text-gray-700 dark:text-gray-300">{{ $stock->warehouse->name }}</td>
                                <td class="py-2 text-xs sm:text-sm text-right">
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ number_format($stock->total_quantity) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No stock data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. STOCK CARD (Recent Movements) -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">📋 Stock Card (7 Days)</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Recent</span>
                </div>
                <div class="space-y-3">
                    @forelse($recentMovements as $movement)
                    <div class="flex items-start gap-3 pb-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                        <div class="flex-shrink-0 mt-1">
                            @if($movement->movement_type === 'in')
                                <div class="p-1.5 bg-green-100 dark:bg-green-900 rounded-full">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="p-1.5 bg-red-100 dark:bg-red-900 rounded-full">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $movement->item->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $movement->reference_type }} - {{ $movement->reference_number }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $movement->movement_date->format('d M Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs sm:text-sm font-semibold {{ $movement->movement_type === 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $movement->movement_type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">= {{ number_format($movement->stock_after) }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">No recent movements</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Bottom Reports -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            
            <!-- 3. TOP 5 PRODUK TERJUAL -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">🏆 Top 5 Selling Products</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Last 30 days</span>
                </div>
                <div class="space-y-3">
                    @forelse($topSellingProducts as $index => $product)
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full {{ $index === 0 ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->code }} • {{ $product->transaction_count }} transactions</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm sm:text-base font-bold text-green-600 dark:text-green-400">{{ number_format($product->total_sold) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">sold</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-4">No sales data available</p>
                    @endforelse
                </div>
            </div>

            <!-- 4. AGING STOCK -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">⏱️ Aging Stock Report</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">No movement 90+ days</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase pb-2">Item</th>
                                <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase pb-2">Warehouse</th>
                                <th class="text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase pb-2">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($agingStock as $stock)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="py-2 text-xs sm:text-sm">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $stock->item->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stock->item->code }}</div>
                                </td>
                                <td class="py-2 text-xs sm:text-sm text-gray-700 dark:text-gray-300">{{ $stock->warehouse->name }}</td>
                                <td class="py-2 text-xs sm:text-sm text-right">
                                    <span class="font-semibold text-orange-600 dark:text-orange-400">{{ number_format($stock->quantity) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">All stock moving well! 🎉</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Pending Approvals Detail -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">⚡ Pending Approvals</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <a href="{{ route('admin.purchase-orders.approval') }}" class="block p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-500 transition-colors">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Purchase Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingPurchaseOrders }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Review →</p>
                </a>
                <a href="{{ route('admin.sales-orders.approval') }}" class="block p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-500 transition-colors">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Sales Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingSalesOrders }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Review →</p>
                </a>
                <a href="{{ route('admin.good-receipt-notes.approval') }}" class="block p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-500 transition-colors">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Good Receipts</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingGoodReceiptNotes }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Review →</p>
                </a>
                <a href="{{ route('admin.deliveries.approval') }}" class="block p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-500 transition-colors">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Deliveries</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingDeliveries }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Review →</p>
                </a>
            </div>
        </div>

    </div>
</x-app-layout>
