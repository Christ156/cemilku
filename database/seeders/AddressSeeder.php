<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        // Buat alamat untuk user1
        $address1 = Address::create([
            'user_id' => 1,
            'receiver_name' => 'Sisca Limit',
            'phone_number' => '081234598690',
            'label' => 'Rumah Utama',
            'provinsi' => 'Jawa Barat',
            'kota_kabupaten' => 'Bandung',
            'kecamatan' => 'Coblong',
            'kelurahan_desa' => 'Dago',
            'rt' => '01',
            'rw' => '02',
            'kode_pos' => '40135',
            'address' => 'Jl. Cemara No. 1',
            'is_primary' => 1
        ]);

        // Buat alamat untuk user2
        $address2 = Address::create([
            'user_id' => 2,
            'receiver_name' => 'John Doe',
            'phone_number' => '081234567890',
            'label' => 'Kantor',
            'provinsi' => 'DKI Jakarta',
            'kota_kabupaten' => 'Jakarta Selatan',
            'kecamatan' => 'Setiabudi',
            'kelurahan_desa' => 'Karet',
            'rt' => '03',
            'rw' => '05',
            'kode_pos' => '12920',
            'address' => 'Jl. Sudirman No. 2',
            'is_primary' => 1
        ]);

        // Update user dengan address_id, jadi buat user dulu baru, address,
        // sebelumnya kolom address_id di user masih null, di sini baru diisi

        //gajadi pake ini, karena masalah circular foreign key
        // User::find(1)->update(['address_id' => $address1->id]);
        // User::find(2)->update(['address_id' => $address2->id]);
    }
}

