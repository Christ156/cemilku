<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Collection;
use App\Models\Address;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil 2 user pertama yang role-nya 'user'
        $users = User::where('role', 'user')->take(2)->get();
        // dump('Users found:', $users->toArray()); // DEBUG

        // Ambil collection berdasarkan name
        $tower = Collection::where('name', 'Kongsi Tower')->first();
        $bouquet = Collection::where('name', 'Kongkow Bouquet')->first();
        // dump('Kongsi Tower:', $tower ? $tower->toArray() : 'NOT FOUND'); // DEBUG
        // dump('Kongkow Bouquet:', $bouquet ? $bouquet->toArray() : 'NOT FOUND'); // DEBUG

        // Ambil alamat yang sudah dibuat oleh AddressSeeder
        $addressUser1 = null;
        $addressUser2 = null;
        if ($users->count() >= 1) {
            $addressUser1 = Address::where('user_id', $users[0]->id)->first();
            // dump('Address for User 1:', $addressUser1 ? $addressUser1->toArray() : 'NOT FOUND'); // DEBUG
        }
        if ($users->count() >= 2) {
            $addressUser2 = Address::where('user_id', $users[1]->id)->first();
            // dump('Address for User 2:', $addressUser2 ? $addressUser2->toArray() : 'NOT FOUND'); // DEBUG
        }

        // Validasi
        if (!$tower || !$bouquet || $users->count() < 2 || !$addressUser1 || !$addressUser2) {
            $this->command->error('Collection, user, atau address tidak ditemukan! Pastikan CollectionSeeder, UserSeeder, dan AddressSeeder berjalan sebelum OrderSeeder.');
            // dump('Validation Failed:'); // DEBUG
            // dump('!$tower', !$tower);
            // dump('!$bouquet', !$bouquet);
            // dump('$users->count() < 2', $users->count() < 2);
            // dump('!$addressUser1', !$addressUser1);
            // dump('!$addressUser2', !$addressUser2);
            return;
        }
        // dump('Validation Passed! Proceeding to create orders.'); // DEBUG

        // ======= ORDER 1 - User 1 pesan Tower Lebaran 2x (paid)
        $order1 = Order::create([
            'user_id' => $users[0]->id,
            'status' => 'paid',
            'total_price' => 0, // Akan diupdate setelah order details
            'payment_method' => 'BCA',
            'address_id' => $addressUser1->id,
        ]);
        OrderDetail::create([
            'order_id' => $order1->id,
            'collection_id' => $tower->id,
            'quantity' => 2,
            'price' => $tower->price,
            // 'product_type' => Collection::class, // DIHAPUS/DIKOMENTARI
        ]);
        $order1->update([
            'total_price' => $tower->price * 2,
        ]);


        // ======= ORDER 2 - User 2 pesan Bouquet + Tower (completed)
        $order2 = Order::create([
            'user_id' => $users[1]->id,
            'status' => 'completed',
            'total_price' => 0, // Akan diupdate setelah order details
            'payment_method' => 'Mandiri',
            'address_id' => $addressUser2->id,
        ]);
        OrderDetail::create([
            'order_id' => $order2->id,
            'collection_id' => $bouquet->id,
            'quantity' => 1,
            'price' => $bouquet->price,
            // 'product_type' => Collection::class, // DIHAPUS/DIKOMENTARI
        ]);
        OrderDetail::create([
            'order_id' => $order2->id,
            'collection_id' => $tower->id,
            'quantity' => 1,
            'price' => $tower->price,
            // 'product_type' => Collection::class, // DIHAPUS/DIKOMENTARI
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
            // 'product_type' => Collection::class, // DIHAPUS/DIKOMENTARI
        ]);
        $order3->update([
            'total_price' => $tower->price * 1,
        ]);

        // ======= ORDER 4 - User 2 pesan Bouquet (paid)
        $order4 = Order::create([
            'user_id' => $users[1]->id,
            'status' => 'paid',
            'total_price' => 0, // Akan diupdate setelah order details
            'payment_method' => 'Danamon',
            'address_id' => $addressUser2->id,
        ]);
        OrderDetail::create([
            'order_id' => $order4->id,
            'collection_id' => $bouquet->id,
            'quantity' => 2,
            'price' => $bouquet->price,
            // 'product_type' => Collection::class, // DIHAPUS/DIKOMENTARI
        ]);
        $order4->update([
            'total_price' => $bouquet->price * 2,
        ]);

        // dump('Orders created successfully!'); // DEBUG
    }
}
