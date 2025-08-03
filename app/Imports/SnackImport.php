<?php

namespace App\Imports;

use App\Models\Snack;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SnackImport implements ToModel, WithHeadingRow
{
    private int $currentRow = 1; // Untuk tracking baris

    public function model(array $row)
    {
        $this->currentRow++; // Tambahkan setiap baris dibaca

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

        // Cek apakah snack dengan nama sama sudah ada
        $existingSnack = Snack::where('name', $row['name'])->first();

        if ($existingSnack) {
            $existingSnack->price = $row['price'];
            $existingSnack->stock += $row['stock'] ?? 0;
            $existingSnack->save();

            return null; // Tidak buat baru
        }

        // Buat snack baru
        return new Snack([
            'name'  => $row['name'],
            'price' => $row['price'],
            'stock' => $row['stock'] ?? 0,
        ]);
    }
}
