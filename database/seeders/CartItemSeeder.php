<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\CartItem;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil cart aktif milik user_id = 2
        $cart = Cart::where('user_id', 2)->where('is_active', true)->first();

        if (!$cart) {
            $this->command->warn('Cart untuk user_id 2 tidak ditemukan. Jalankan CartSeeder terlebih dahulu.');
            return;
        }

        // Tambahkan item ke dalam cart
        CartItem::create([
            'cart_id'       => $cart->id,
            'collection_id' => 1,
            'customize_id'  => null,
            'quantity'      => 2,
            'price'         => 25000,
            'total_price'   => 50000,
        ]);

        CartItem::create([
            'cart_id'       => $cart->id,
            'collection_id' => 2,
            'customize_id'  => null,
            'quantity'      => 1,
            'price'         => 35000,
            'total_price'   => 35000,
        ]);
    }
}
