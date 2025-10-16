<x-app-layout>
    <x-slot name="header">
        Sales Order Approval
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200">Sales Order Approval</h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Review and approve/reject pending sales orders</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="px-4 py-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                        <span class="font-bold text-2xl" id="pendingCount">{{ $pendingCount }}</span> Pending
                    </p>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select id="filterStatus" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base">
                        <option value="pending" selected>Pending Only</option>
                        <option value="">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SO Number</label>
                    <input type="text" id="filterPONumber" placeholder="SO-2024-00001"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer</label>
                    <input type="text" id="filterCustomer" placeholder="Customer name..."
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Min Amount</label>
                    <input type="number" id="filterMinAmount" placeholder="10000000"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base">
                </div>
                <div class="flex items-end">
                    <button onclick="applyFilters()"
                        class="w-full px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Apply Filters
                    </button>
                </div>
                <div class="flex items-end">
                    <button onclick="resetFilters()"
                        class="w-full px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Reset
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
                        <strong>Approval Policy:</strong> Sales Orders with amount ≥ Rp 10,000,000 require admin/manager approval before processing.
                    </p>
                </div>
            </div>
        </div>

        <!-- Sales Orders Table Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="overflow-x-auto">
                    <table id="approvalTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    SO Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Customer
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
                                    Status
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Priority
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        let table;

        $(document).ready(function() {
            // Initialize DataTable
            table = $('#approvalTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.sales-orders.approval') }}",
                    data: function(d) {
                        d.status = $('#filterStatus').val();
                        d.so_number = $('#filterPONumber').val();
                        d.customer = $('#filterCustomer').val();
                        d.min_amount = $('#filterMinAmount').val();
                    }
                },
                columns: [
                    { data: 'po_info', name: 'so_number' },
                    { data: 'so_date', name: 'so_date' },
                    { data: 'customer_info', name: 'customer.name' },
                    { data: 'warehouse_name', name: 'warehouse.name' },
                    { data: 'creator_info', name: 'creator.name' },
                    { data: 'total_amount', name: 'total_amount', className: 'text-right' },
                    { data: 'status', name: 'status', className: 'text-center' },
                    { data: 'priority', name: 'priority', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
                ],
                order: [[5, 'desc']], // Order by amount descending (high value first)
                pageLength: 10,
                language: {
                    emptyTable: `
                        <div class="py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">No sales orders found</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters</p>
                            </div>
                        </div>
                    `
                },
                rowCallback: function(row, data) {
                    // Highlight high-value POs
                    if (data.total_amount && data.total_amount.includes('50,000,000')) {
                        $(row).addClass('bg-red-50 dark:bg-red-900/10');
                    }
                }
            });
        });

        // Filter function
        function applyFilters() {
            table.ajax.reload();
        }

        // Reset filters
        function resetFilters() {
            $('#filterStatus').val('pending');
            $('#filterPONumber').val('');
            $('#filterCustomer').val('');
            $('#filterMinAmount').val('');
            table.ajax.reload();
        }

        // Auto-refresh every 5 minutes to catch new approvals
        setInterval(function() {
            table.ajax.reload(null, false); // Reload without resetting pagination
        }, 300000);
    </script>
    @endpush
</x-app-layout>
