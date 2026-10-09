<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')
            ->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        $products = Product::where('is_available', true)
            ->where('stock', '>', 0)
            ->orderBy('name')->get();

        return view('orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $order = DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'order_number' => 'ORD-' . Str::upper(Str::random(12)),
                'ordered_at' => now(),
                'total' => 0,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ]);

            $total = $this->replaceItems($order, $data['items'], false);

            $order->update(['total' => $total]);

            return $order;
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load('items');

        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_available', true)
            ->orWhereIn('id', $order->items->pluck('product_id'))
            ->orderBy('name')->get();

        return view('orders.edit', compact('order', 'customers', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data, $order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            // Restore stock from the previous order before replacing its items.
            foreach ($order->items as $oldItem) {
                $product = Product::whereKey($oldItem->product_id)
                    ->lockForUpdate()->firstOrFail();

                $product->increment('stock', $oldItem->quantity);
            }

            $order->items()->delete();

            $order->update([
                'customer_id' => $data['customer_id'],
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ]);

            $total = $this->replaceItems($order, $data['items'], true);

            $order->update(['total' => $total]);
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)
                ->lockForUpdate()->firstOrFail();

            foreach ($order->items as $item) {
                $product = Product::whereKey($item->product_id)
                    ->lockForUpdate()->firstOrFail();

                $product->increment('stock', $item->quantity);
            }

            $order->delete();
        });

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted and stock restored.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'status' => [
                'required',
                Rule::in(['pending', 'processing', 'completed', 'cancelled']),
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],
        ]);
    }

    private function replaceItems(
        Order $order,
        array $items,
        bool $allowExistingProducts
    ): float {
        $totalCents = 0;

        foreach ($items as $item) {
            $product = Product::whereKey($item['product_id'])
                ->lockForUpdate()->firstOrFail();

            if (!$product->is_available && !$allowExistingProducts) {
                throw ValidationException::withMessages([
                    'items' => "Product {$product->name} is unavailable.",
                ]);
            }

            $quantity = (int) $item['quantity'];

            if ($product->stock < $quantity) {
                throw ValidationException::withMessages([
                    'items' => "Insufficient stock for {$product->name}.",
                ]);
            }

            // Work in cents to avoid accumulating floating-point errors.
            $unitCents = (int) round(((float) $product->price) * 100);
            $subtotalCents = $unitCents * $quantity;
            $totalCents += $subtotalCents;

            $product->decrement('stock', $quantity);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => number_format($unitCents / 100, 2, '.', ''),
                'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
            ]);
        }

        return $totalCents / 100;
    }
}