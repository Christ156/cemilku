<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil 2 user pertama yang role-nya 'user'
        $users = User::where('role', 'user')->take(2)->get();

        // Ambil collection berdasarkan name
        $tower = Collection::where('name', 'Kongsi Tower')->first();
        $bouquet = Collection::where('name', 'Kongkow Bouquet')->first();

        // Validasi
        if (!$tower || !$bouquet || $users->count() < 2) {
            $this->command->error('Collection atau user tidak ditemukan!');
            return;
        }

        // ======= ORDER 1 - User 1 pesan Tower Lebaran 2x (paid)
        $order1 = Order::create([
            'user_id' => $users[0]->id,
            'status' => 'paid',
            'total_price' => 0,
            'payment_method' => 'BCA',
        ]);

        OrderDetail::create([
            'order_id' => $order1->id,
            'collection_id' => $tower->id,
            'quantity' => 2,
            'price' => $tower->price,
        ]);

        $order1->update([
            'total_price' => $tower->price * 2,
        ]);

        // ======= ORDER 2 - User 2 pesan Bouquet + Tower (completed)
        $order2 = Order::create([
            'user_id' => $users[1]->id,
            'status' => 'completed',
            'total_price' => 0,
            'payment_method' => 'Mandiri',
        ]);

        OrderDetail::create([
            'order_id' => $order2->id,
            'collection_id' => $bouquet->id,
            'quantity' => 1,
            'price' => $bouquet->price,
        ]);

        OrderDetail::create([
            'order_id' => $order2->id,
            'collection_id' => $tower->id,
            'quantity' => 1,
            'price' => $tower->price,
        ]);

        $order2->update([
            'total_price' => $bouquet->price + $tower->price,
        ]);

        // ======= ORDER 3 - User 2 pesan Tower (pending)
        $order3 = Order::create([
            'user_id' => $users[1]->id,
            'status' => 'pending',
            'total_price' => 0,
            'payment_method' => 'Cimb Niaga',
        ]);

        OrderDetail::create([
            'order_id' => $order3->id,
            'collection_id' => $tower->id,
            'quantity' => 1,
            'price' => $tower->price,
        ]);

        $order3->update([
            'total_price' => $tower->price * 1,
        ]);

        // ======= ORDER 4 - User 2 pesan Bouquet (paid)
        $order4 = Order::create([
            'user_id' => $users[1]->id,
            'status' => 'paid',
            'total_price' => 0,
            'payment_method' => 'Danamon',
        ]);

        OrderDetail::create([
            'order_id' => $order4->id,
            'collection_id' => $bouquet->id,
            'quantity' => 2,
            'price' => $bouquet->price,
        ]);

        $order4->update([
            'total_price' => $bouquet->price * 2,
        ]);
    }
}
