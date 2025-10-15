<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600 dark:text-gray-400">Here's what's happening with your warehouse management system today.</p>
                </div>
            </div>

            <!-- Master Data Statistics -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Master Data</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Items -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Items</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalItems }}</p>
                                </div>
                                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('admin.items.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block">View All →</a>
                        </div>
                    </div>

                    <!-- Suppliers -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Suppliers</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalSuppliers }}</p>
                                </div>
                                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('admin.suppliers.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline mt-2 inline-block">View All →</a>
                        </div>
                    </div>

                    <!-- Warehouses -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Warehouses</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalWarehouses }}</p>
                                </div>
                                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('admin.warehouses.index') }}" class="text-sm text-yellow-600 dark:text-yellow-400 hover:underline mt-2 inline-block">View All →</a>
                        </div>
                    </div>

                    <!-- Customers -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Customers</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalCustomers }}</p>
                                </div>
                                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('admin.customers.index') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:underline mt-2 inline-block">View All →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Pending Approvals</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Purchase Orders -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Purchase Orders</p>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $pendingPurchaseOrders }}</p>
                            <a href="{{ route('admin.purchase-orders.approval') }}" class="text-sm text-red-600 dark:text-red-400 hover:underline mt-2 inline-block">Review →</a>
                        </div>
                    </div>

                    <!-- Sales Orders -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Sales Orders</p>
                            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $pendingSalesOrders }}</p>
                            <a href="{{ route('admin.sales-orders.approval') }}" class="text-sm text-orange-600 dark:text-orange-400 hover:underline mt-2 inline-block">Review →</a>
                        </div>
                    </div>

                    <!-- Good Receipt Notes -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Good Receipt Notes</p>
                            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $pendingGoodReceiptNotes }}</p>
                            <a href="{{ route('admin.good-receipt-notes.approval') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline mt-2 inline-block">Review →</a>
                        </div>
                    </div>

                    <!-- Deliveries -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-pink-500">
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Deliveries</p>
                            <p class="text-3xl font-bold text-pink-600 dark:text-pink-400">{{ $pendingDeliveries }}</p>
                            <a href="{{ route('admin.deliveries.approval') }}" class="text-sm text-pink-600 dark:text-pink-400 hover:underline mt-2 inline-block">Review →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Purchase Orders -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Recent Purchase Orders</h4>
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
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No purchase orders yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Sales Orders -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Recent Sales Orders</h4>
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
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No sales orders yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
