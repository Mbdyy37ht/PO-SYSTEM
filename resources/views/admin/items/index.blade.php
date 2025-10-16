<x-app-layout>
    <x-slot name="header">
        Items Management
    </x-slot>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Items</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your inventory items</p>
            </div>
            <a href="{{ route('admin.items.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Item
            </a>
        </div>

        <!-- Items Table Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table id="items-table"
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 dark:text-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Item Code
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Name
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Description
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Unit
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Min Stock
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- DataTables will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Delete Item</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to delete item <strong id="deleteItemName" class="text-gray-900 dark:text-gray-100"></strong>?
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            This action cannot be undone.
                        </p>
                    </div>
                    <div class="flex gap-4 px-4 py-3">
                        <button id="cancelDelete" type="button"
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-900 text-base font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                        <button id="confirmDelete" type="button"
                            class="flex-1 px-4 py-2 bg-red-600 text-white text-base font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

        <script>
            $(document).ready(function() {
                let deleteForm = null;

                // Check if DataTable already exists, destroy it first
                if ($.fn.DataTable.isDataTable('#items-table')) {
                    $('#items-table').DataTable().destroy();
                }

                $('#items-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.items.index') }}',
                    columns: [{
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false
                        },
                        {
                            data: 'unit',
                            name: 'unit'
                        },
                        {
                            data: 'minimum_stock',
                            name: 'minimum_stock'
                        },
                        {
                            data: 'status',
                            name: 'is_active',
                            orderable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-right'
                        }
                    ],

                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    language: {
                        processing: '<div class="text-blue-600 dark:text-blue-400">Loading...</div>',
                        emptyTable: '<div class="text-gray-500 dark:text-gray-400 py-8"><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg><p class="mt-2">No items found. Click "Add New Item" to create one.</p></div>'
                    },
                    dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4"<"flex items-center"l><"flex items-center"f>>rtip',
                    drawCallback: function() {
                        // Handle delete confirmation with modal
                        $('.delete-form').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            deleteForm = this;

                            // Get item name from the row
                            const itemName = $(this).closest('tr').find('td:eq(1)').text();
                            $('#deleteItemName').text(itemName);

                            // Show modal
                            $('#deleteModal').removeClass('hidden');
                        });
                    }
                });

                // Modal cancel button (remove existing handler first to prevent duplicates)
                $('#cancelDelete').off('click').on('click', function() {
                    $('#deleteModal').addClass('hidden');
                    deleteForm = null;
                });

                // Modal confirm button
                $('#confirmDelete').off('click').on('click', function() {
                    if (deleteForm) {
                        deleteForm.submit();
                    }
                });

                // Close modal when clicking outside
                $('#deleteModal').off('click').on('click', function(e) {
                    if (e.target === this) {
                        $(this).addClass('hidden');
                        deleteForm = null;
                    }
                });

                // Close modal with Escape key (use namespaced event to prevent duplicates)
                $(document).off('keydown.deleteModal').on('keydown.deleteModal', function(e) {
                    if (e.key === 'Escape' && !$('#deleteModal').hasClass('hidden')) {
                        $('#deleteModal').addClass('hidden');
                        deleteForm = null;
                    }
                });

                // Custom styling for DataTables elements
                $('.dataTables_length select').addClass(
                    'px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100'
                );
                $('.dataTables_filter input').addClass(
                    'px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-100 ml-2'
                );
                $('.dataTables_length label, .dataTables_filter label').addClass(
                    'text-sm text-gray-700 dark:text-gray-300');
            });
        </script>
    @endpush
</x-app-layout>
