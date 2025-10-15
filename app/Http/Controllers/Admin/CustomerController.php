<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::query();

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('status', function ($customer) {
                    if ($customer->is_active) {
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>';
                    }
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Inactive</span>';
                })
                ->addColumn('action', function ($customer) {
                    $viewUrl = route('admin.customers.show', $customer);
                    $editUrl = route('admin.customers.edit', $customer);
                    $deleteUrl = route('admin.customers.destroy', $customer);
                    $csrf = csrf_token();

                    return '
                        <div class="flex justify-end space-x-2">
                            <a href="'.$viewUrl.'" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="'.$editUrl.'" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="'.$deleteUrl.'" method="POST" class="inline delete-form">
                                <input type="hidden" name="_token" value="'.$csrf.'">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->editColumn('address', function ($customer) {
                    return $customer->address ? Str::limit($customer->address, 50) : '-';
                })
                ->editColumn('phone', function ($customer) {
                    return $customer->phone ?: '-';
                })
                ->editColumn('email', function ($customer) {
                    return $customer->email ?: '-';
                })
                ->editColumn('contact_person', function ($customer) {
                    return $customer->contact_person ?: '-';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.customers.index');
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:customers,code|max:50',
            'name' => 'required|max:255',
            'address' => 'nullable',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Customer::create($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer '{$validated['name']}' has been created successfully! ✨");
    }

    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code' => 'required|max:50|unique:customers,code,' . $customer->id,
            'name' => 'required|max:255',
            'address' => 'nullable',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer '{$validated['name']}' has been updated successfully! 📝");
    }

    public function destroy(Customer $customer)
    {
        $customerName = $customer->name;
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer '{$customerName}' has been successfully deleted! 🗑️");
    }
}
