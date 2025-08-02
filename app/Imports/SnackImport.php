<?php

namespace App\Imports;

use App\Models\Snack;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SnackImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $validator = Validator::make($row, [
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
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
