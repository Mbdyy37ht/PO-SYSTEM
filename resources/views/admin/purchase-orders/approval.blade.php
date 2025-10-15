<x-app-layout>
    <x-slot name="header">
        Purchase Order Approval
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Purchase Order Approval</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Review and approve/reject pending purchase orders</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="px-4 py-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                        <span class="font-bold text-2xl">{{ $purchaseOrders->total() }}</span> Pending
                    </p>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search PO Number</label>
                    <input type="text" id="filterPONumber" placeholder="PO-2024-00001"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                    <input type="text" id="filterSupplier" placeholder="Supplier name..."
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Min Amount</label>
                    <input type="number" id="filterMinAmount" placeholder="10000000"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button onclick="applyFilters()"
                        class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Alert for High Value POs -->
        <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-400 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <strong>Approval Policy:</strong> Purchase Orders with amount ≥ Rp 10,000,000 require admin/manager approval before processing.
                    </p>
                </div>
            </div>
        </div>

        <!-- Purchase Orders Table Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    PO Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Supplier
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Warehouse
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Created By
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Total Amount
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Priority
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($purchaseOrders as $po)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 {{ $po->total_amount >= 50000000 ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $po->po_number }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $po->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($po->po_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $po->supplier->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $po->supplier->code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $po->warehouse->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                                    {{ strtoupper(substr($po->creator->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $po->creator->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $po->creator->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                            Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                                        </div>
                                        @if($po->total_amount >= 10000000)
                                            <div class="text-xs text-yellow-600 dark:text-yellow-400">
                                                Requires Approval
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($po->total_amount >= 50000000)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                🔥 Critical
                                            </span>
                                        @elseif($po->total_amount >= 25000000)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                ⚡ High
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                📋 Normal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <!-- View/Review Button -->
                                            <a href="{{ route('admin.purchase-orders.show', $po) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Review
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">No pending purchase orders for approval</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">All purchase orders have been processed</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($purchaseOrders->hasPages())
                    <div class="mt-4">
                        {{ $purchaseOrders->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Card -->
        @if($purchaseOrders->total() > 0)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Quick Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Pending Amount</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($purchaseOrders->sum('total_amount'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Average PO Value</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Rp {{ number_format($purchaseOrders->avg('total_amount'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">High Priority (≥ 25M)</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $purchaseOrders->where('total_amount', '>=', 25000000)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function applyFilters() {
            const poNumber = document.getElementById('filterPONumber').value;
            const supplier = document.getElementById('filterSupplier').value;
            const minAmount = document.getElementById('filterMinAmount').value;

            let url = new URL(window.location.href);
            url.searchParams.set('po_number', poNumber);
            url.searchParams.set('supplier', supplier);
            url.searchParams.set('min_amount', minAmount);

            window.location.href = url.toString();
        }

        // Auto-refresh every 5 minutes to catch new approvals
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
    @endpush
</x-app-layout>
