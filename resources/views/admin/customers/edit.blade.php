<x-app-layout>
    <x-slot name="header">
        Edit Customer
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200">Edit Customer</h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Update customer information</p>
            </div>
            <a href="{{ route('admin.customers.index') }}"
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
                <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4 sm:space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Customer Code -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Customer Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="code" 
                                       id="code" 
                                       value="{{ old('code', $customer->code) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base @error('code') border-red-500 @enderror"
                                       placeholder="CUS001">
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Customer Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Customer Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $customer->name) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base @error('name') border-red-500 @enderror"
                                       placeholder="PT. Customer Name">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Person -->
                            <div>
                                <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Contact Person
                                </label>
                                <input type="text"
                                       name="contact_person" 
                                       id="contact_person" 
                                       value="{{ old('contact_person', $customer->contact_person) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base @error('contact_person') border-red-500 @enderror"
                                       placeholder="John Doe">
                                @error('contact_person')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone
                                </label>
                                <input type="text"
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone', $customer->phone) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base @error('phone') border-red-500 @enderror"
                                       placeholder="021-1234567">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email
                                </label>
                                <input type="email"
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email', $customer->email) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base @error('email') border-red-500 @enderror"
                                       placeholder="customer@example.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Status
                                </label>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="is_active" 
                                           id="is_active" 
                                           value="1"
                                           {{ old('is_active', $customer->is_active) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Address
                            </label>
                            <textarea name="address" 
                                      id="address" 
                                      rows="3"
                                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm sm:text-base @error('address') border-red-500 @enderror"
                                      placeholder="Full address...">{{ old('address', $customer->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row sm:justify-end gap-2 sm:gap-3 mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.customers.index') }}"
                            class="px-4 sm:px-6 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors duration-200 text-center">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-4 sm:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Update Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
