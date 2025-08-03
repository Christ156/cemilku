<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus dulu jika ada cart sebelumnya (optional)
        Cart::where('user_id', 2)->delete();

        // Buat cart aktif untuk user_id = 2
        Cart::create([
            'user_id'   => 2,
            'is_active' => true,
        ]);
    }
}
