<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('orders')
            ->latest()->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string'],
        ]);

        Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'orders' => fn($query) => $query->latest(),
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customer->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string'],
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return back()->with(
                'error',
                'This customer has order history and cannot be deleted.'
            );
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}