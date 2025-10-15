<x-app-layout>
    <x-slot name="header">
        Staff Dashboard
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Message -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-600 dark:text-gray-400">Here's an overview of your transactions.</p>
            </div>
        </div>

        <!-- My Transaction Statistics -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">My Transactions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Purchase Orders -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Purchase Orders</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $myPurchaseOrders }}</p>
                                </div>
                                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('staff.purchase-orders.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block">View All →</a>
                        </div>
                    </div>

                    <!-- Sales Orders -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Sales Orders</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $mySalesOrders }}</p>
                                </div>
                                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('staff.sales-orders.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline mt-2 inline-block">View All →</a>
                        </div>
                    </div>

                    <!-- Good Receipt Notes -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Good Receipt Notes</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $myGoodReceiptNotes }}</p>
                                </div>
                                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('staff.good-receipt-notes.index') }}" class="text-sm text-yellow-600 dark:text-yellow-400 hover:underline mt-2 inline-block">View All →</a>
                        </div>
                    </div>

                    <!-- Deliveries -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Deliveries</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $myDeliveries }}</p>
                                </div>
                                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('staff.deliveries.index') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:underline mt-2 inline-block">View All →</a>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Pending Transactions -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Pending Approvals</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Pending Purchase Orders -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Purchase Orders</p>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $pendingPurchaseOrders }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Waiting for approval</p>
                        </div>
                    </div>

                    <!-- Pending Sales Orders -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Sales Orders</p>
                            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $pendingSalesOrders }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Waiting for approval</p>
                        </div>
                    </div>

                    <!-- Pending Good Receipt Notes -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Good Receipt Notes</p>
                            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $pendingGoodReceiptNotes }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Waiting for approval</p>
                        </div>
                    </div>

                    <!-- Pending Deliveries -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-pink-500">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Deliveries</p>
                            <p class="text-3xl font-bold text-pink-600 dark:text-pink-400">{{ $pendingDeliveries }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Waiting for approval</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Purchase Orders -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">My Recent Purchase Orders</h4>
                        @if($recentPurchaseOrders->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentPurchaseOrders as $po)
                                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-2">
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $po->po_number }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $po->supplier->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($po->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($po->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @elseif($po->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                                                @endif">
                                                {{ ucfirst($po->status) }}
                                            </span>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('staff.purchase-orders.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-4 inline-block">View All →</a>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No purchase orders yet.</p>
                            <a href="{{ route('staff.purchase-orders.create') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-4 inline-block">Create New →</a>
                        @endif
                    </div>
                </div>

                <!-- Recent Sales Orders -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">My Recent Sales Orders</h4>
                        @if($recentSalesOrders->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentSalesOrders as $so)
                                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-2">
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $so->so_number }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $so->customer->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($so->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($so->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @elseif($so->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                                                @endif">
                                                {{ ucfirst($so->status) }}
                                            </span>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Rp {{ number_format($so->total_amount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('staff.sales-orders.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline mt-4 inline-block">View All →</a>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No sales orders yet.</p>
                            <a href="{{ route('staff.sales-orders.create') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline mt-4 inline-block">Create New →</a>
                        @endif
                    </div>
                </div>
            </div>

        <!-- Quick Actions -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('staff.purchase-orders.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-lg shadow-sm text-center transition duration-200">
                        <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="font-semibold">New Purchase Order</p>
                    </a>
                    <a href="{{ route('staff.sales-orders.create') }}" class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-lg shadow-sm text-center transition duration-200">
                        <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="font-semibold">New Sales Order</p>
                    </a>
                    <a href="{{ route('staff.good-receipt-notes.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white p-6 rounded-lg shadow-sm text-center transition duration-200">
                        <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="font-semibold">New Good Receipt Note</p>
                    </a>
                    <a href="{{ route('staff.deliveries.create') }}" class="bg-purple-500 hover:bg-purple-600 text-white p-6 rounded-lg shadow-sm text-center transition duration-200">
                        <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="font-semibold">New Delivery</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
