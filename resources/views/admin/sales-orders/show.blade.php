<x-app-layout>
    <x-slot name="header">
        Review Sales Order
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Header with Actions -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200">Review Sales Order</h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Review and approve/reject this sales order</p>
            </div>
            <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
                <a href="{{ route('admin.sales-orders.approval') }}"
                    class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>

                @if($salesOrder->status === 'pending')
                    <button onclick="showApproveModal()" 
                        class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Approve
                    </button>

                    <button onclick="showRejectModal()" 
                        class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Reject
                    </button>
                @endif

                <button onclick="window.print()"
                    class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <!-- Alert for High Value PO -->
        @if($salesOrder->total_amount >= 50000000)
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 dark:text-red-300">
                            <strong>⚠️ CRITICAL VALUE ALERT!</strong> This sales order has a very high value (≥ Rp 50,000,000). Please review carefully before approval.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($salesOrder->total_amount >= 25000000)
            <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-orange-700 dark:text-orange-300">
                            <strong>⚡ HIGH VALUE PO:</strong> This sales order has a high value (≥ Rp 25,000,000). Please verify all details.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Status Alert -->
        @if($salesOrder->status !== 'pending')
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            This sales order has already been <strong>{{ $salesOrder->status }}</strong>. No further action is required.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- PO Header Information -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">Sales Order Information</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">SO Number</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $salesOrder->so_number }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">SO Date</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($salesOrder->so_date)->format('d F Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Customer</label>
                            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-gray-100">{{ $salesOrder->customer->name }}</p>
                            <div class="mt-1 space-y-1">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Code:</span> {{ $salesOrder->customer->code }}
                                </p>
                                @if($salesOrder->customer->phone)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Phone:</span> {{ $salesOrder->customer->phone }}
                                    </p>
                                @endif
                                @if($salesOrder->customer->email)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Email:</span> {{ $salesOrder->customer->email }}
                                    </p>
                                @endif
                                @if($salesOrder->customer->contact_person)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Contact:</span> {{ $salesOrder->customer->contact_person }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Warehouse</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $salesOrder->warehouse->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $salesOrder->warehouse->code }}</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                            <div class="mt-1">
                                @if($salesOrder->status === 'draft')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Draft
                                    </span>
                                @elseif($salesOrder->status === 'pending')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                        ⏳ Pending Approval
                                    </span>
                                @elseif($salesOrder->status === 'approved')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        ✓ Approved
                                    </span>
                                @elseif($salesOrder->status === 'rejected')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                        ✗ Rejected
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created By</label>
                            <div class="mt-1 flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                        {{ strtoupper(substr($salesOrder->creator->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $salesOrder->creator->name }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $salesOrder->creator->email }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $salesOrder->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        @if($salesOrder->approver)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ $salesOrder->status === 'approved' ? 'Approved By' : 'Rejected By' }}
                                </label>
                                <div class="mt-1 flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 {{ $salesOrder->status === 'approved' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium {{ $salesOrder->status === 'approved' ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                                            {{ strtoupper(substr($salesOrder->approver->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $salesOrder->approver->name }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $salesOrder->approver->email }}</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $salesOrder->approved_at ? \Carbon\Carbon::parse($salesOrder->approved_at)->format('d M Y, H:i') : '-' }}
                                </p>
                            </div>
                        @endif

                        @if($salesOrder->approval_notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ $salesOrder->status === 'approved' ? 'Approval Notes' : 'Rejection Reason' }}
                                </label>
                                <p class="mt-1 text-sm {{ $salesOrder->status === 'rejected' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                    {{ $salesOrder->approval_notes }}
                                </p>
                            </div>
                        @endif

                        @if($salesOrder->notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Notes</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $salesOrder->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Order Items</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Item Code
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Item Name
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
                            @foreach($salesOrder->details as $index => $detail)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $detail->item->code }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        <div class="font-medium">{{ $detail->item->name }}</div>
                                        @if($detail->notes)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 italic mt-1">
                                                Note: {{ $detail->notes }}
                                            </div>
                                        @endif
                                        @if($detail->item->description)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ Str::limit($detail->item->description, 100) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-gray-100">
                                        {{ number_format($detail->quantity, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $detail->item->unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-right text-base font-bold text-gray-900 dark:text-gray-100">
                                    Total Amount:
                                </td>
                                <td class="px-6 py-4 text-right text-xl font-bold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($salesOrder->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @if($salesOrder->total_amount >= 10000000)
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

        <!-- Timeline -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Timeline</h3>
            </div>
            <div class="p-6">
                <div class="flow-root">
                    <ul class="-mb-8">
                        <!-- Created -->
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                                Sales Order <span class="font-medium">created</span> by 
                                                <span class="font-medium">{{ $salesOrder->creator->name }}</span>
                                            </p>
                                        </div>
                                        <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                            <time datetime="{{ $salesOrder->created_at }}">{{ $salesOrder->created_at->format('d M Y, H:i') }}</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- Submitted for Approval -->
                        @if($salesOrder->status !== 'draft')
                            <li>
                                <div class="relative pb-8">
                                    @if($salesOrder->approved_at)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-900 dark:text-gray-100">
                                                    Submitted for <span class="font-medium">approval</span>
                                                </p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                <time>{{ $salesOrder->created_at->format('d M Y, H:i') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endif

                        <!-- Approved/Rejected -->
                        @if($salesOrder->approved_at)
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ $salesOrder->status === 'approved' ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                @if($salesOrder->status === 'approved')
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-900 dark:text-gray-100">
                                                    Sales Order <span class="font-medium {{ $salesOrder->status === 'approved' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ $salesOrder->status }}</span> by 
                                                    <span class="font-medium">{{ $salesOrder->approver->name }}</span>
                                                </p>
                                                @if($salesOrder->approval_notes)
                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                        "{{ $salesOrder->approval_notes }}"
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                <time datetime="{{ $salesOrder->approved_at }}">{{ \Carbon\Carbon::parse($salesOrder->approved_at)->format('d M Y, H:i') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Approve Sales Order</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Are you sure you want to approve this sales order?
                        </p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $salesOrder->so_number }} - Rp {{ number_format($salesOrder->total_amount, 0, ',', '.') }}
                        </p>
                        
                        <form action="{{ route('admin.sales-orders.approve', $salesOrder) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="text-left">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Approval Notes (Optional)</label>
                                <textarea name="approval_notes" rows="3" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                    placeholder="Add any notes for this approval..."></textarea>
                            </div>
                            
                            <div class="flex items-center justify-center gap-3 mt-6">
                                <button type="button" onclick="hideApproveModal()"
                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium rounded-lg transition-colors duration-200">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    Yes, Approve
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Reject Sales Order</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Are you sure you want to reject this sales order?
                        </p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $salesOrder->so_number }} - Rp {{ number_format($salesOrder->total_amount, 0, ',', '.') }}
                        </p>
                        
                        <form action="{{ route('admin.sales-orders.reject', $salesOrder) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="text-left">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Rejection Reason <span class="text-red-500">*</span>
                                </label>
                                <textarea name="approval_notes" rows="3" required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Please provide a reason for rejection..."></textarea>
                            </div>
                            
                            <div class="flex items-center justify-center gap-3 mt-6">
                                <button type="button" onclick="hideRejectModal()"
                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium rounded-lg transition-colors duration-200">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    Yes, Reject
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
    </script>
    @endpush

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
            button, .no-print, #approveModal, #rejectModal {
                display: none !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
