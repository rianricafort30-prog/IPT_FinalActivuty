<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $meals = Category::create([
                'name' => 'Meals',
                'description' => 'Rice meals and main dishes',
            ]);

            $drinks = Category::create([
                'name' => 'Beverages',
                'description' => 'Hot and cold drinks',
            ]);

            $snacks = Category::create([
                'name' => 'Snacks',
                'description' => 'Snacks and desserts',
            ]);

            $chicken = Product::create([
                'category_id' => $meals->id,
                'name' => 'Chicken Rice Meal',
                'description' => 'Chicken served with rice',
                'price' => 120,
                'stock' => 30,
                'is_available' => true,
            ]);

            Product::create([
                'category_id' => $meals->id,
                'name' => 'Beef Burger',
                'description' => 'Classic beef burger',
                'price' => 150,
                'stock' => 25,
                'is_available' => true,
            ]);

            $coffee = Product::create([
                'category_id' => $drinks->id,
                'name' => 'Iced Coffee',
                'description' => 'Chilled coffee',
                'price' => 80,
                'stock' => 50,
                'is_available' => true,
            ]);

            Product::create([
                'category_id' => $drinks->id,
                'name' => 'Fruit Juice',
                'description' => 'Fresh fruit juice',
                'price' => 60,
                'stock' => 40,
                'is_available' => true,
            ]);

            Product::create([
                'category_id' => $snacks->id,
                'name' => 'French Fries',
                'description' => 'Crispy potato fries',
                'price' => 70,
                'stock' => 35,
                'is_available' => true,
            ]);

            $customer = Customer::create([
                'name' => 'Juan Dela Cruz',
                'email' => 'juan@example.com',
                'phone' => '09171234567',
                'address' => 'Clarin, Bohol',
            ]);

            Customer::factory(9)->create();

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . Str::upper(Str::random(12)),
                'ordered_at' => now(),
                'total' => 0,
                'status' => 'pending',
                'notes' => 'Sample demonstration order',
            ]);

            $totalCents = 0;

            foreach ([[$chicken, 2], [$coffee, 1]] as [$product, $quantity]) {
                $unitCents = (int) round((float) $product->price * 100);
                $subtotalCents = $unitCents * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => number_format($unitCents / 100, 2, '.', ''),
                    'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
                ]);

                $product->decrement('stock', $quantity);
                $totalCents += $subtotalCents;
            }

            $order->update(['total' => $totalCents / 100]);

            // Add nine more products under three new categories.
            Category::factory(3)->create()->each(function ($category) {
                Product::factory(3)->create([
                    'category_id' => $category->id,
                ]);
            });
        });
    }
}