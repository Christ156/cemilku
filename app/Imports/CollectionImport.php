<?php

namespace App\Imports;

use App\Models\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CollectionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $existing = Collection::where('name', $row['name'])->first();

        if ($existing) {
            // Tambah stok dan update harga
            $existing->stock += $row['stock'];
            $existing->price = $row['price']; // update harga baru
            $existing->save();

            return null; // tidak perlu buat entri baru
        }

        // Jika produk belum ada, buat entri baru
        return new Collection([
            'category'    => $row['category'],
            'name'        => $row['name'],
            'type'        => $row['type'],
            'description' => $row['description'],
            'price'       => $row['price'],
            'stock'       => $row['stock'],
            'image'       => $row['image'],
            'layer'       => $row['layer'],
            'created_at'  => $row['created_at'] ?? now(),
        ]);
    }
}
