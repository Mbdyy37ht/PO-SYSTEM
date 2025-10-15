<x-app-layout>
    <x-slot name="header">
        Purchase Order Details
    </x-slot>

    <div class="space-y-6">
        <!-- Header with Actions -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Purchase Order Details</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">View purchase order information</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('staff.purchase-orders.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>

                @if($purchaseOrder->status === 'draft')
                    <a href="{{ route('staff.purchase-orders.edit', $purchaseOrder) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit & Submit
                    </a>
                @endif

                <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <!-- PO Header Information -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">PO Number</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $purchaseOrder->po_number }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">PO Date</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($purchaseOrder->po_date)->format('d F Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $purchaseOrder->supplier->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $purchaseOrder->supplier->phone }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Warehouse</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $purchaseOrder->warehouse->name }}</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                            <div class="mt-1">
                                @if($purchaseOrder->status === 'draft')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Draft
                                    </span>
                                @elseif($purchaseOrder->status === 'pending')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                        Pending Approval
                                    </span>
                                @elseif($purchaseOrder->status === 'approved')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        Approved
                                    </span>
                                @elseif($purchaseOrder->status === 'rejected')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                        Rejected
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created By</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $purchaseOrder->creator->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $purchaseOrder->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        @if($purchaseOrder->approver)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ $purchaseOrder->status === 'approved' ? 'Approved By' : 'Rejected By' }}
                                </label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $purchaseOrder->approver->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $purchaseOrder->approval_date ? \Carbon\Carbon::parse($purchaseOrder->approval_date)->format('d M Y, H:i') : '-' }}</p>
                            </div>
                        @endif

                        @if($purchaseOrder->rejection_reason)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Rejection Reason</label>
                                <p class="mt-1 text-red-600 dark:text-red-400">{{ $purchaseOrder->rejection_reason }}</p>
                            </div>
                        @endif

                        @if($purchaseOrder->notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Notes</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $purchaseOrder->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Item Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Item Code
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Quantity
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Unit
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Unit Price
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($purchaseOrder->details as $index => $detail)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $detail->item->name }}
                                        @if($detail->notes)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">{{ $detail->notes }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $detail->item->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                                        {{ number_format($detail->quantity, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $detail->item->unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    Total Amount:
                                </td>
                                <td class="px-6 py-4 text-right text-lg font-bold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @if($purchaseOrder->total_amount >= 10000000)
                                <tr>
                                    <td colspan="7" class="px-6 py-2 text-right text-sm text-yellow-600 dark:text-yellow-400">
                                        ⚠️ This PO requires admin approval (≥ Rp 10,000,000)
                                    </td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Timeline/History (if approved/rejected) -->
        @if($purchaseOrder->status !== 'draft')
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900">
                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Created</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $purchaseOrder->created_at->format('d M Y, H:i') }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">by {{ $purchaseOrder->creator->name }}</p>
                            </div>
                        </div>

                        @if($purchaseOrder->approval_date)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-10 w-10 rounded-full {{ $purchaseOrder->status === 'approved' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                        <svg class="h-6 w-6 {{ $purchaseOrder->status === 'approved' ? 'text-green-600 dark:text-green-300' : 'text-red-600 dark:text-red-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($purchaseOrder->status === 'approved')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            @endif
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($purchaseOrder->status) }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($purchaseOrder->approval_date)->format('d M Y, H:i') }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">by {{ $purchaseOrder->approver->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            button, .no-print {
                display: none !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
