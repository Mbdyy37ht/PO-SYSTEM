<x-app-layout>
    <x-slot name="header">
        Edit Purchase Order
    </x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Edit Purchase Order</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update purchase order details - {{ $purchaseOrder->po_number }}</p>
            </div>
            <a href="{{ route('staff.purchase-orders.show', $purchaseOrder) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Details
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('staff.purchase-orders.update', $purchaseOrder) }}" method="POST" id="poForm">
                    @csrf
                    @method('PUT')

                    <!-- PO Header Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- PO Number (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                PO Number
                            </label>
                            <input type="text" value="{{ $purchaseOrder->po_number }}" readonly
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm bg-gray-100 dark:bg-gray-900">
                        </div>

                        <!-- PO Date -->
                        <div>
                            <label for="po_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                PO Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="po_date" id="po_date" required
                                value="{{ old('po_date', $purchaseOrder->po_date) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('po_date') border-red-500 @enderror">
                            @error('po_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier -->
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Supplier <span class="text-red-500">*</span>
                            </label>
                            <select name="supplier_id" id="supplier_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('supplier_id') border-red-500 @enderror">
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" 
                                        {{ (old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id) ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Warehouse -->
                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" id="warehouse_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('warehouse_id') border-red-500 @enderror">
                                <option value="">-- Select Warehouse --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" 
                                        {{ (old('warehouse_id', $purchaseOrder->warehouse_id) == $warehouse->id) ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Additional notes...">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Items</h3>
                            <button type="button" onclick="addItem()" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div id="items-container" class="space-y-4">
                            <!-- Existing items will be loaded here -->
                        </div>

                        @error('items')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6">
                        <div class="flex justify-end">
                            <div class="w-full md:w-1/2 space-y-2">
                                <div class="flex justify-between text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    <span>Total Amount:</span>
                                    <span id="totalAmount">Rp 0</span>
                                </div>
                                <div class="text-sm text-blue-600 dark:text-blue-400 text-right space-y-1">
                                    <p id="approvalInfo" class="hidden">
                                        ℹ️ <strong>Approval Required:</strong> This PO (≥ Rp 10,000,000) will be sent to admin for approval when submitted.
                                    </p>
                                    <p id="autoApproveInfo" class="hidden">
                                        ✓ <strong>Auto-Approve:</strong> This PO (< Rp 10,000,000) will be automatically approved when submitted.
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        💡 <em>Tip: Use "Update as Draft" to save changes without submitting.</em>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('staff.purchase-orders.show', $purchaseOrder) }}"
                            class="px-6 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium rounded-lg transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" name="action" value="draft"
                            class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <span class="inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Update as Draft
                            </span>
                        </button>
                        <button type="submit" name="action" value="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <span class="inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        const items = @json($items);
        const existingItems = @json($purchaseOrder->details);

        // Load existing items on page load
        document.addEventListener('DOMContentLoaded', function() {
            existingItems.forEach(detail => {
                addItem(detail);
            });
            calculateTotal();
        });

        function addItem(existingData = null) {
            const container = document.getElementById('items-container');
            const selectedItemId = existingData ? existingData.item_id : '';
            const selectedItem = items.find(item => item.id == selectedItemId);
            const quantity = existingData ? existingData.quantity : 1;
            const unitPrice = existingData ? existingData.unit_price : 0;
            const itemNotes = existingData ? (existingData.notes || '') : '';
            
            const itemRow = `
                <div class="item-row bg-gray-50 dark:bg-gray-700 p-4 rounded-lg" data-index="${itemIndex}">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- Item Selection -->
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Item <span class="text-red-500">*</span>
                            </label>
                            <select name="items[${itemIndex}][item_id]" required onchange="updateItemInfo(this, ${itemIndex})"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select Item --</option>
                                ${items.map(item => `<option value="${item.id}" data-unit="${item.unit}" ${item.id == selectedItemId ? 'selected' : ''}>${item.name}</option>`).join('')}
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="${quantity}"
                                onchange="calculateSubtotal(${itemIndex})"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Unit -->
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unit
                            </label>
                            <input type="text" id="unit_${itemIndex}" readonly value="${selectedItem ? selectedItem.unit : ''}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm bg-gray-100 dark:bg-gray-800">
                        </div>

                        <!-- Unit Price -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unit Price <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="items[${itemIndex}][unit_price]" required min="0" step="0.01" value="${unitPrice}"
                                onchange="calculateSubtotal(${itemIndex})"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Subtotal -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subtotal
                            </label>
                            <input type="text" id="subtotal_${itemIndex}" readonly
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm bg-gray-100 dark:bg-gray-800">
                        </div>

                        <!-- Delete Button -->
                        <div class="md:col-span-1 flex items-end">
                            <button type="button" onclick="removeItem(${itemIndex})"
                                class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Item Notes -->
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Item Notes
                        </label>
                        <input type="text" name="items[${itemIndex}][notes]" placeholder="Additional notes for this item..." value="${itemNotes}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', itemRow);
            
            // Calculate subtotal for this item
            if (existingData) {
                calculateSubtotal(itemIndex);
            }
            
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
