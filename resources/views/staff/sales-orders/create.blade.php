<x-app-layout>
    <x-slot name="header">
        Create Sales Order
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200">Create Sales Order</h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Fill in the details to create a new sales order</p>
            </div>
            <a href="{{ route('staff.sales-orders.index') }}"
                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-6">
                <form action="{{ route('staff.sales-orders.store') }}" method="POST" id="poForm">
                    @csrf

                    <!-- PO Header Information -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                        <!-- SO Date -->
                        <div>
                            <label for="so_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                SO Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="so_date" id="so_date" required
                                value="{{ old('so_date', date('Y-m-d')) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base @error('so_date') border-red-500 @enderror">
                            @error('so_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customer -->
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Customer <span class="text-red-500">*</span>
                            </label>
                            <select name="customer_id" id="customer_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base @error('customer_id') border-red-500 @enderror">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Warehouse -->
                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" id="warehouse_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base @error('warehouse_id') border-red-500 @enderror">
                                <option value="">-- Select Warehouse --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base"
                                placeholder="Additional notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 sm:pt-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">Items</h3>
                            <button type="button" onclick="addItem()"
                                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div id="items-container" class="space-y-3 sm:space-y-4">
                            <!-- Items will be added here dynamically -->
                        </div>

                        @error('items')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-4 sm:mt-6 pt-4 sm:pt-6">
                        <div class="flex justify-end">
                            <div class="w-full space-y-2">
                                <div class="flex justify-between items-center text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    <span>Total Amount:</span>
                                    <span id="totalAmount" class="text-lg sm:text-xl">Rp 0</span>
                                </div>
                                <div class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 text-right space-y-1">
                                    <p id="approvalInfo" class="hidden">
                                        ℹ️ <strong>Approval Required:</strong> This PO (≥ Rp 10,000,000) will be sent to admin for approval when submitted.
                                    </p>
                                    <p id="autoApproveInfo" class="hidden">
                                        ✓ <strong>Auto-Approve:</strong> This PO (< Rp 10,000,000) will be automatically approved when submitted.
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        💡 <em>Tip: Use "Save as Draft" to review later before submitting.</em>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row sm:justify-end gap-2 sm:gap-3 mt-4 sm:mt-6">
                        <a href="{{ route('staff.sales-orders.index') }}"
                            class="px-4 sm:px-6 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors duration-200 text-center">
                            Cancel
                        </a>
                        <button type="submit" name="action" value="draft"
                            class="px-4 sm:px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <span class="inline-flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Save as Draft
                            </span>
                        </button>
                        <button type="submit" name="action" value="submit"
                            class="px-4 sm:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <span class="inline-flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Submit PO
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let itemIndex = 0;
        const items = [
            @foreach($items as $item)
            {
                id: {{ $item->id }},
                name: "{{ addslashes($item->name) }}",
                unit: "{{ addslashes($item->unit) }}"
            }{{ $loop->last ? '' : ',' }}
            @endforeach
        ];

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addItem();
        });

        function addItem() {
            const container = document.getElementById('items-container');
            const itemRow = `
                <div class="item-row bg-gray-50 dark:bg-gray-700 p-3 sm:p-4 rounded-lg" data-index="${itemIndex}">
                    <div class="grid grid-cols-1 gap-3 sm:gap-4">
                        <!-- Row 1: Item Selection -->
                        <div class="col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Item <span class="text-red-500">*</span>
                            </label>
                            <select name="items[${itemIndex}][item_id]" required onchange="updateItemInfo(this, ${itemIndex})"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">-- Select Item --</option>
                                ${items.map(item => `<option value="${item.id}" data-unit="${item.unit}">${item.name}</option>`).join('')}
                            </select>
                        </div>

                        <!-- Row 2: Quantity, Unit, Price Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                            <!-- Quantity -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Qty <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="1"
                                    onchange="calculateSubtotal(${itemIndex})"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>

                            <!-- Unit -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Unit
                                </label>
                                <input type="text" id="unit_${itemIndex}" readonly
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm bg-gray-100 dark:bg-gray-800 text-sm">
                            </div>

                            <!-- Unit Price -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Price <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="items[${itemIndex}][unit_price]" required min="0" step="0.01"
                                    onchange="calculateSubtotal(${itemIndex})"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>

                            <!-- Subtotal -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Subtotal
                                </label>
                                <input type="text" id="subtotal_${itemIndex}" readonly
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm bg-gray-100 dark:bg-gray-800 text-sm">
                            </div>
                        </div>

                        <!-- Row 3: Notes and Delete Button -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-3">
                            <div class="sm:col-span-11">
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Item Notes
                                </label>
                                <input type="text" name="items[${itemIndex}][notes]" placeholder="Additional notes for this item..."
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>

                            <!-- Delete Button -->
                            <div class="sm:col-span-1 flex items-end">
                                <button type="button" onclick="removeItem(${itemIndex})"
                                    class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', itemRow);
            itemIndex++;
        }

        function removeItem(index) {
            const itemRow = document.querySelector(`.item-row[data-index="${index}"]`);
            if (document.querySelectorAll('.item-row').length > 1) {
                itemRow.remove();
                calculateTotal();
            } else {
                alert('At least one item is required!');
            }
        }

        function updateItemInfo(select, index) {
            const selectedOption = select.options[select.selectedIndex];
            const unit = selectedOption.getAttribute('data-unit');
            document.getElementById(`unit_${index}`).value = unit || '';
        }

        function calculateSubtotal(index) {
            const itemRow = document.querySelector(`.item-row[data-index="${index}"]`);
            const quantity = parseFloat(itemRow.querySelector('input[name*="[quantity]"]').value) || 0;
            const unitPrice = parseFloat(itemRow.querySelector('input[name*="[unit_price]"]').value) || 0;
            const subtotal = quantity * unitPrice;

            document.getElementById(`subtotal_${index}`).value = 'Rp ' + subtotal.toLocaleString('id-ID');
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
                const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
                total += quantity * unitPrice;
            });

            document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID');

            // Show approval info based on amount
            const approvalInfo = document.getElementById('approvalInfo');
            const autoApproveInfo = document.getElementById('autoApproveInfo');

            if (total >= 10000000) {
                approvalInfo.classList.remove('hidden');
                autoApproveInfo.classList.add('hidden');
            } else if (total > 0) {
                approvalInfo.classList.add('hidden');
                autoApproveInfo.classList.remove('hidden');
            } else {
                approvalInfo.classList.add('hidden');
                autoApproveInfo.classList.add('hidden');
            }
        }
    </script>
    @endpush
</x-app-layout>
