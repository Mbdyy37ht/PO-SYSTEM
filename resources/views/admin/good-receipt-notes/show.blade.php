<x-app-layout>
    <x-slot name="header">
        Review Good Receipt Note
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $goodReceiptNote->grn_number }}</h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Review Good Receipt Note</p>
            </div>
            <a href="{{ route('admin.good-receipt-notes.approval') }}"
                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">GRN Information</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
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
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $goodReceiptNote->notes }}</p>
                    </div>
                @endif

                @if($goodReceiptNote->approval_notes)
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Approval Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $goodReceiptNote->approval_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Items Received</h3>
            
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
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                <button onclick="showApproveModal()" 
                    class="inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Approve & Update Stock
                </button>
                
                <button onclick="showRejectModal()"
                    class="inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Reject GRN
                </button>
            </div>
        @endif
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Approve Good Receipt Note</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Are you sure you want to approve this GRN? Stock will be updated automatically.
                        </p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $goodReceiptNote->grn_number }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Total Items: {{ $goodReceiptNote->details->sum('quantity_received') }} units
                        </p>
                        
                        <form action="{{ route('admin.good-receipt-notes.approve', $goodReceiptNote) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="text-left">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Approval Notes (Optional)</label>
                                <textarea name="approval_notes" rows="3" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                                    placeholder="Add any notes for this approval..."></textarea>
                            </div>
                            
                            <div class="flex items-center justify-center gap-3 mt-6">
                                <button type="button" onclick="hideApproveModal()"
                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors duration-200">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    Yes, Approve & Update Stock
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Reject Good Receipt Note</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Are you sure you want to reject this GRN?
                        </p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $goodReceiptNote->grn_number }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Staff can edit and resubmit after rejection
                        </p>
                        
                        <form action="{{ route('admin.good-receipt-notes.reject', $goodReceiptNote) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="text-left">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Rejection Reason <span class="text-red-500">*</span>
                                </label>
                                <textarea name="approval_notes" rows="3" required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                    placeholder="Please provide a reason for rejection..."></textarea>
                            </div>
                            
                            <div class="flex items-center justify-center gap-3 mt-6">
                                <button type="button" onclick="hideRejectModal()"
                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors duration-200">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    Yes, Reject GRN
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showApproveModal() {
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function hideApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function showRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function hideRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const approveModal = document.getElementById('approveModal');
            const rejectModal = document.getElementById('rejectModal');
            
            if (event.target == approveModal) {
                hideApproveModal();
            }
            if (event.target == rejectModal) {
                hideRejectModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideApproveModal();
                hideRejectModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
