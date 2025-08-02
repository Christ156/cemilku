<?php

namespace App\Imports;

use App\Models\Decoration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DecorationImport implements ToModel, WithHeadingRow
{
    private int $currentRow = 1; // Untuk tracking baris Excel

    public function model(array $row)
    {
        $this->currentRow++; // Menambahkan baris yang sedang dibaca

        // Validasi data per baris
        $validator = Validator::make($row, [
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'file' => "Error pada baris {$this->currentRow}: " . implode(', ', $validator->errors()->all()),
            ]);
        }

        // Cek apakah dekorasi sudah ada
        $existing = Decoration::where('name', trim($row['name']))->first();

        if ($existing) {
            // Update stok dan harga jika sudah ada
            $existing->stock += $row['stock'] ?? 0;
            $existing->price = $row['price'];
            $existing->save();

            return null;
        }

        // Buat dekorasi baru
        return new Decoration([
            'name'  => trim($row['name']),
            'price' => $row['price'],
            'stock' => $row['stock'] ?? 0,
        ]);
    }
}
