<x-app-layout>
    <x-slot name="header">
        Delivery Details
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $delivery->delivery_number }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Delivery Details</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('staff.deliveries.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
                
                @if($delivery->status === 'draft')
                    <a href="{{ route('staff.deliveries.edit', $delivery) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                @endif
            </div>
        </div>

        <!-- Status Badge -->
        <div class="flex items-center space-x-4">
            @if($delivery->status === 'draft')
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                    Draft
                </span>
            @elseif($delivery->status === 'pending')
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                    Pending Approval
                </span>
            @elseif($delivery->status === 'approved')
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                    Approved
                </span>
            @elseif($delivery->status === 'rejected')
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                    Rejected
                </span>
            @endif

            @if($delivery->approved_at)
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $delivery->status === 'approved' ? 'Approved' : 'Rejected' }} by {{ $delivery->approver->name }}
                    on {{ $delivery->approved_at->format('d M Y H:i') }}
                </span>
            @endif
        </div>

        <!-- Rejection Alert -->
        @if($delivery->status === 'rejected')
            <div class="bg-red-50 dark:bg-red-900 border-l-4 border-red-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 dark:text-red-300">
                            <strong>This Delivery has been rejected and is now read-only.</strong><br>
                            Reason: {{ $delivery->approval_notes ?? 'No reason provided' }}<br>
                            <span class="text-xs mt-2 block">To fix this issue, please create a new Delivery from the same Sales Order with the correct information.</span>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delivery Information Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Delivery Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Delivery Number</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $delivery->delivery_number }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Delivery Date</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('d M Y') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Sales Order</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $delivery->salesOrder->so_number }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Customer</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $delivery->salesOrder->customer->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $delivery->salesOrder->customer->code }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Warehouse</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $delivery->warehouse->name }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created By</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $delivery->creator->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $delivery->created_at->format('d M Y H:i') }}</p>
                </div>

                @if($delivery->notes)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $delivery->notes }}</p>
                    </div>
                @endif

                @if($delivery->approval_notes)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Approval Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $delivery->approval_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Items Delivered</h3>
            
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
                                Qty Delivered
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
                        @foreach($delivery->details as $detail)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $detail->item->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $detail->item->code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($detail->quantity_ordered) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($detail->quantity_delivered) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($detail->quantity_delivered == $detail->quantity_ordered)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Complete
                                        </span>
                                    @elseif($detail->quantity_delivered < $detail->quantity_ordered)
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
                                {{ number_format($delivery->details->sum('quantity_ordered')) }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ number_format($delivery->details->sum('quantity_delivered')) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Action Buttons for Draft ONLY -->
        @if($delivery->status === 'draft')
            <div class="flex justify-end space-x-4">
                <form action="{{ route('staff.deliveries.destroy', $delivery) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Delivery? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete GRN
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
