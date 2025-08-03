<?php

namespace App\Imports;

use App\Models\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CollectionImport implements ToModel, WithHeadingRow
{
    private int $currentRow = 1; // Tracking baris Excel

    public function model(array $row)
    {
        $this->currentRow++; // Tambahkan counter baris

        // Validasi data per baris
        $validator = Validator::make($row, [
            'category'    => 'required|in:Chinese New Year,Valentine,Ramadhan,Christmas,Birthday,Graduation',
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:tower,bouquet',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'image'       => 'nullable|string|max:255',
            'layer'       => 'required|integer|between:2,4',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'file' => "Error pada baris {$this->currentRow}: " . implode(', ', $validator->errors()->all()),
            ]);
        }

        // Cek apakah collection sudah ada
        $existing = Collection::where('name', $row['name'])->first();

        if ($existing) {
            $existing->stock += $row['stock'] ?? 0;
            $existing->price = $row['price'];
            $existing->save();

            return null;
        }

        // Buat collection baru
        return new Collection([
            'category'    => $row['category'],
            'name'        => $row['name'],
            'type'        => $row['type'],
            'description' => $row['description'] ?? null,
            'price'       => $row['price'],
            'stock'       => $row['stock'] ?? 0,
            'image'       => $row['image'] ?? null,
            'layer'       => $row['layer'],
            'created_at'  => $row['created_at'] ?? now(),
        ]);
    }
}
