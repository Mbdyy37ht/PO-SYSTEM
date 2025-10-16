<x-app-layout>
    <x-slot name="header">
        Create Good Receipt Note
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200">Create Good Receipt Note</h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Record goods received from supplier</p>
            </div>
            <a href="{{ route('staff.good-receipt-notes.index') }}"
                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors duration-200 w-full sm:w-auto">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>

        <!-- Alert if no approved POs -->
        @if($purchaseOrders->isEmpty())
            <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 p-3 sm:p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs sm:text-sm text-yellow-700 dark:text-yellow-300">
                            <strong>No Approved Purchase Orders Available</strong><br>
                            There are no approved Purchase Orders without Good Receipt Notes. Please create and approve a Purchase Order first before creating a GRN.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Create Form -->
        <form action="{{ route('staff.good-receipt-notes.store') }}" method="POST" id="grnForm">
            @csrf

            <div class="space-y-4 sm:space-y-6">
                <!-- GRN Information Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">GRN Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- GRN Date -->
                        <div>
                            <label for="grn_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                GRN Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="grn_date" id="grn_date" value="{{ old('grn_date', date('Y-m-d')) }}" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base">
                            @error('grn_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purchase Order -->
                        <div>
                            <label for="purchase_order_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Purchase Order <span class="text-red-500">*</span>
                            </label>
                            <select name="purchase_order_id" id="purchase_order_id" required onchange="loadPODetails()"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base"
                                {{ $purchaseOrders->isEmpty() ? 'disabled' : '' }}>
                                <option value="">Select Purchase Order</option>
                                @foreach($purchaseOrders as $po)
                                    <option value="{{ $po->id }}" data-supplier="{{ $po->supplier->name }}"
                                        data-warehouse="{{ $po->warehouse_id }}"
                                        {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                        {{ $po->po_number }} - {{ $po->supplier->name }} ({{ \Carbon\Carbon::parse($po->po_date)->format('d M Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('purchase_order_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Warehouse -->
                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Warehouse <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" id="warehouse_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base">
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} - {{ $warehouse->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm sm:text-base">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Items Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200">Items</h3>
                        <button type="button" onclick="loadPOItems()" id="loadItemsBtn" disabled
                            class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto">
                            Load PO Items
                        </button>
                    </div>

                    <!-- Mobile Card View -->
                    <div id="itemsCardView" class="block lg:hidden space-y-3">
                        <!-- Items will be rendered here for mobile -->
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty Ordered</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty Received</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Notes</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Select a Purchase Order and click "Load PO Items" to add items
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:justify-end gap-2 sm:gap-4">
                    <a href="{{ route('staff.good-receipt-notes.index') }}"
                        class="px-4 sm:px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors duration-200 text-center">
                        Cancel
                    </a>
                    <button type="submit" name="action" value="draft" id="draftBtn"
                        class="px-4 sm:px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Save as Draft
                    </button>
                    <button type="submit" name="action" value="submit" id="submitBtn"
                        class="px-4 sm:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Submit for Approval
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Convert PHP data to JavaScript
        let poDetailsData = {
            @foreach($purchaseOrders as $po)
                '{{ $po->id }}': {
                    warehouse_id: '{{ $po->warehouse_id }}',
                    details: [
                        @foreach($po->details as $detail)
                        {
                            item_id: {{ $detail->item_id }},
                            quantity: {{ $detail->quantity }},
                            item: {
                                name: "{{ addslashes($detail->item->name) }}",
                                code: "{{ addslashes($detail->item->code) }}"
                            }
                        }{{ $loop->last ? '' : ',' }}
                        @endforeach
                    ]
                }{{ $loop->last ? '' : ',' }}
            @endforeach
        };

        function loadPODetails() {
            const select = document.getElementById('purchase_order_id');
            const selectedOption = select.options[select.selectedIndex];

            if (select.value) {
                // Set warehouse from PO
                const warehouseId = selectedOption.getAttribute('data-warehouse');
                document.getElementById('warehouse_id').value = warehouseId;

                // Enable load items button
                document.getElementById('loadItemsBtn').disabled = false;
            } else {
                document.getElementById('loadItemsBtn').disabled = true;
                document.getElementById('warehouse_id').value = '';
                // Clear items
                clearItems();
            }
        }

        function clearItems() {
            const tbody = document.getElementById('itemsTableBody');
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Select a Purchase Order and click "Load PO Items" to add items</td></tr>';

            const cardView = document.getElementById('itemsCardView');
            cardView.innerHTML = '<div class="text-center text-sm text-gray-500 dark:text-gray-400 py-8">Select a Purchase Order and click "Load PO Items" to add items</div>';
        }

        function loadPOItems() {
            const poId = document.getElementById('purchase_order_id').value;
            if (!poId) {
                alert('Please select a Purchase Order first');
                return;
            }

            const poData = poDetailsData[poId];
            if (!poData || !poData.details) {
                alert('No items found for this Purchase Order');
                return;
            }

            renderItems(poData.details);
        }

        function renderItems(items) {
            const tbody = document.getElementById('itemsTableBody');
            const cardView = document.getElementById('itemsCardView');

            tbody.innerHTML = '';
            cardView.innerHTML = '';

            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No items found in the selected Purchase Order</td></tr>';
                cardView.innerHTML = '<div class="text-center text-sm text-gray-500 dark:text-gray-400 py-8">No items found in the selected Purchase Order</div>';
                return;
            }

            items.forEach((item, index) => {
                // Desktop table row
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                row.innerHTML = `
                    <td class="px-4 py-4">
                        <input type="hidden" name="items[${index}][item_id]" value="${item.item_id}">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${item.item.name}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${item.item.code}</div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <input type="number" name="items[${index}][quantity_ordered]" value="${item.quantity}" readonly
                            class="w-24 text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-gray-100 text-sm">
                    </td>
                    <td class="px-4 py-4 text-center">
                        <input type="number" name="items[${index}][quantity_received]" value="${item.quantity}" min="0" required
                            class="w-24 text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                    </td>
                    <td class="px-4 py-4">
                        <input type="text" name="items[${index}][notes]" placeholder="Optional notes..."
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                    </td>
                    <td class="px-4 py-4 text-center">
                        <button type="button" onclick="removeItem(${index})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);

                // Mobile card view
                const card = document.createElement('div');
                card.className = 'bg-gray-50 dark:bg-gray-700 p-4 rounded-lg';
                card.setAttribute('data-item-index', index);
                card.innerHTML = `
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <input type="hidden" name="items_mobile[${index}][item_id]" value="${item.item_id}">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${item.item.name}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${item.item.code}</div>
                        </div>
                        <button type="button" onclick="removeItem(${index})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 ml-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Qty Ordered</label>
                            <input type="number" name="items_mobile[${index}][quantity_ordered]" value="${item.quantity}" readonly
                                class="w-full text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 bg-gray-100 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Qty Received <span class="text-red-500">*</span></label>
                            <input type="number" name="items_mobile[${index}][quantity_received]" value="${item.quantity}" min="0" required
                                class="w-full text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <input type="text" name="items_mobile[${index}][notes]" placeholder="Optional notes..."
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                `;
                cardView.appendChild(card);
            });
        }

        function removeItem(index) {
            // Remove from table
            const tbody = document.getElementById('itemsTableBody');
            const tableRows = tbody.querySelectorAll('tr');
            tableRows.forEach(row => {
                const hiddenInput = row.querySelector(`input[name="items[${index}][item_id]"]`);
                if (hiddenInput) {
                    row.remove();
                }
            });

            // Remove from card view
            const cardView = document.getElementById('itemsCardView');
            const card = cardView.querySelector(`[data-item-index="${index}"]`);
            if (card) {
                card.remove();
            }

            // Re-index remaining items
            reindexItems();

            // Check if any rows left
            if (tbody.children.length === 0) {
                clearItems();
            }
        }

        function reindexItems() {
            const tbody = document.getElementById('itemsTableBody');
            const cardView = document.getElementById('itemsCardView');

            // Reindex table rows
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

            // Reindex cards
            const cards = cardView.querySelectorAll('[data-item-index]');
            cards.forEach((card, index) => {
                card.setAttribute('data-item-index', index);
                const inputs = card.querySelectorAll('input');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/items_mobile\[\d+\]/, `items_mobile[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });

                const button = card.querySelector('button[onclick]');
                if (button) {
                    button.setAttribute('onclick', `removeItem(${index})`);
                }
            });
        }

        // Form validation
        document.getElementById('grnForm').addEventListener('submit', function(e) {
            const tbody = document.getElementById('itemsTableBody');
            const cardView = document.getElementById('itemsCardView');

            const hasTableItems = tbody.querySelector('input[name^="items"]');
            const hasCardItems = cardView.querySelector('input[name^="items_mobile"]');

            if (!hasTableItems && !hasCardItems) {
                e.preventDefault();
                alert('Please add at least one item to the GRN');
                return false;
            }
        });
    </script>
    @endpush
</x-app-layout>
