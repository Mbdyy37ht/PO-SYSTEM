<x-app-layout>
    <x-slot name="header">
        Edit Delivery
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Edit Delivery</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $delivery->delivery_number }}</p>
            </div>
            <a href="{{ route('staff.deliveries.show', $delivery) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Details
            </a>
        </div>

        <!-- Status Alert -->
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
                            <strong>This Delivery was rejected.</strong><br>
                            Reason: {{ $delivery->approval_notes ?? 'No reason provided' }}<br>
                            <span class="text-xs">You can edit and resubmit this GRN.</span>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Form -->
        <form action="{{ route('staff.deliveries.update', $delivery) }}" method="POST" id="grnEditForm">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Delivery Information Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Delivery Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Delivery Number (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Delivery Number
                            </label>
                            <input type="text" value="{{ $delivery->delivery_number }}" readonly
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-gray-100 shadow-sm">
                        </div>

                        <!-- Delivery Date -->
                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Delivery Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="delivery_date" id="delivery_date"
                                value="{{ old('delivery_date', $delivery->delivery_date->format('Y-m-d')) }}" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('delivery_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sales Order (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Sales Order
                            </label>
                            <input type="hidden" name="sales_order_id" value="{{ $delivery->purchase_order_id }}">
                            <input type="text" value="{{ $delivery->salesOrder->so_number }} - {{ $delivery->salesOrder->customer->name }}" readonly
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-gray-100 shadow-sm">
                        </div>

                        <!-- Warehouse -->
                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" id="warehouse_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"
                                        {{ old('warehouse_id', $delivery->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} - {{ $warehouse->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('notes', $delivery->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Items Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Items</h3>
                        <button type="button" onclick="resetToOriginal()"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            Reset to Original
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty Ordered</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty Delivered</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Notes</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($delivery->details as $index => $detail)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-4">
                                            <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $detail->item_id }}">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $detail->item->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $detail->item->code }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <input type="number" name="items[{{ $index }}][quantity_ordered]" value="{{ $detail->quantity_ordered }}" readonly
                                                class="w-24 text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-gray-100">
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <input type="number" name="items[{{ $index }}][quantity_delivered]"
                                                value="{{ old('items.'.$index.'.quantity_delivered', $detail->quantity_delivered) }}"
                                                min="0" required
                                                class="w-24 text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="text" name="items[{{ $index }}][notes]"
                                                value="{{ old('items.'.$index.'.notes', $detail->notes) }}"
                                                placeholder="Optional notes..."
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('staff.deliveries.show', $delivery) }}"
                        class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" name="action" value="draft"
                        class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Save as Draft
                    </button>
                    <button type="submit" name="action" value="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Submit for Approval
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (Separate) -->
        <div class="mt-6">
            <form action="{{ route('staff.deliveries.destroy', $delivery) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this Delivery? This action cannot be undone.');">
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
    </div>

    @push('scripts')
    <script>
        // Store original data for reset functionality
        const originalData = [
            @foreach($delivery->details as $detail)
            {
                item_id: {{ $detail->item_id }},
                item_name: "{{ addslashes($detail->item->name) }}",
                item_code: "{{ addslashes($detail->item->code) }}",
                quantity_ordered: {{ $detail->quantity_ordered }},
                quantity_delivered: {{ $detail->quantity_delivered }},
                notes: "{{ addslashes($detail->notes ?? '') }}"
            }{{ $loop->last ? '' : ',' }}
            @endforeach
        ];

        function resetToOriginal() {
            if (!confirm('Reset all items to original values?')) {
                return;
            }

            renderItems(originalData);
        }

        function renderItems(items) {
            const tbody = document.getElementById('itemsTableBody');
            tbody.innerHTML = '';

            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No items added</td></tr>';
                return;
            }

            items.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                row.innerHTML = `
                    <td class="px-4 py-4">
                        <input type="hidden" name="items[${index}][item_id]" value="${item.item_id}">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${item.item_name}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${item.item_code}</div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <input type="number" name="items[${index}][quantity_ordered]" value="${item.quantity_ordered}" readonly
                            class="w-24 text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-gray-100">
                    </td>
                    <td class="px-4 py-4 text-center">
                        <input type="number" name="items[${index}][quantity_delivered]" value="${item.quantity_delivered}" min="0" required
                            class="w-24 text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500">
                    </td>
                    <td class="px-4 py-4">
                        <input type="text" name="items[${index}][notes]" value="${item.notes || ''}" placeholder="Optional notes..."
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                    </td>
                    <td class="px-4 py-4 text-center">
                        <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function removeItem(button) {
            if (!confirm('Remove this item from the GRN?')) {
                return;
            }

            const row = button.closest('tr');
            row.remove();

            // Re-index remaining items
            reindexItems();

            // Check if any rows left
            const tbody = document.getElementById('itemsTableBody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No items added. Click "Reset to Original" to restore items.</td></tr>';
            }
        }

        function reindexItems() {
            const tbody = document.getElementById('itemsTableBody');
            const rows = tbody.querySelectorAll('tr');

            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }

        // Form validation
        document.getElementById('grnEditForm').addEventListener('submit', function(e) {
            const tbody = document.getElementById('itemsTableBody');
            const hasItems = tbody.querySelector('input[name^="items"]');

            if (!hasItems) {
                e.preventDefault();
                alert('Please add at least one item to the GRN. Click "Reset to Original" to restore items.');
                return false;
            }
        });
    </script>
    @endpush
</x-app-layout>
