<x-app-layout>
    <x-slot name="header">
        Review Good Receipt Note
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $goodReceiptNote->grn_number }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Review Good Receipt Note</p>
            </div>
            <a href="{{ route('admin.good-receipt-notes.approval') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>

        <!-- Status Badge -->
        <div class="flex items-center space-x-4">
            @if($goodReceiptNote->status === 'pending')
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                    Pending Approval
                </span>
            @elseif($goodReceiptNote->status === 'approved')
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                    Approved
                </span>
            @elseif($goodReceiptNote->status === 'rejected')
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                    Rejected
                </span>
            @endif

            @if($goodReceiptNote->approved_at)
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $goodReceiptNote->status === 'approved' ? 'Approved' : 'Rejected' }} by {{ $goodReceiptNote->approver->name }}
                    on {{ $goodReceiptNote->approved_at->format('d M Y H:i') }}
                </span>
            @endif
        </div>

        <!-- GRN Information Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">GRN Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">GRN Number</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $goodReceiptNote->grn_number }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">GRN Date</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($goodReceiptNote->grn_date)->format('d M Y') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Order</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $goodReceiptNote->purchaseOrder->po_number }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        PO Date: {{ \Carbon\Carbon::parse($goodReceiptNote->purchaseOrder->po_date)->format('d M Y') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $goodReceiptNote->purchaseOrder->supplier->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $goodReceiptNote->purchaseOrder->supplier->code }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Warehouse</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $goodReceiptNote->warehouse->name }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created By</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $goodReceiptNote->creator->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $goodReceiptNote->created_at->format('d M Y H:i') }}</p>
                </div>

                @if($goodReceiptNote->notes)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $goodReceiptNote->notes }}</p>
                    </div>
                @endif

                @if($goodReceiptNote->approval_notes)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Approval Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $goodReceiptNote->approval_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Items Received</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Item
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Qty Ordered
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Qty Received
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Notes
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($goodReceiptNote->details as $detail)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $detail->item->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $detail->item->code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($detail->quantity_ordered) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($detail->quantity_received) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($detail->quantity_received == $detail->quantity_ordered)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Complete
                                        </span>
                                    @elseif($detail->quantity_received < $detail->quantity_ordered)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                            Under Delivery
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            Over Delivery
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $detail->notes ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <td class="px-6 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Total
                            </td>
                            <td class="px-6 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ number_format($goodReceiptNote->details->sum('quantity_ordered')) }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ number_format($goodReceiptNote->details->sum('quantity_received')) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Approval Actions (Only for Pending) -->
        @if($goodReceiptNote->status === 'pending')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Approve Form -->
                <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-300 mb-4">Approve GRN</h3>
                    <form action="{{ route('admin.good-receipt-notes.approve', $goodReceiptNote) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this GRN? Stock will be updated automatically.');">
                        @csrf
                        <div class="mb-4">
                            <label for="approve_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Approval Notes (Optional)
                            </label>
                            <textarea name="approval_notes" id="approve_notes" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                placeholder="Add any approval notes..."></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve & Update Stock
                        </button>
                    </form>
                </div>

                <!-- Reject Form -->
                <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-300 mb-4">Reject GRN</h3>
                    <form action="{{ route('admin.good-receipt-notes.reject', $goodReceiptNote) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this GRN?');">
                        @csrf
                        <div class="mb-4">
                            <label for="reject_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rejection Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="approval_notes" id="reject_notes" rows="3" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject GRN
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
