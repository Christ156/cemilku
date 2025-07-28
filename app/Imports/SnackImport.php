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
        // Validasi data setiap baris
        $validator = Validator::make($row, [
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $imageName = null;

        // Tangani gambar jika ada
        if (!empty($row['image'])) {
            if (filter_var($row['image'], FILTER_VALIDATE_URL)) {
                // Jika image berupa URL
                $imageName = basename(parse_url($row['image'], PHP_URL_PATH));
                $destinationPath = public_path('assets/snack_items/' . $imageName);

                if (!file_exists($destinationPath)) {
                    try {
                        $imageContents = file_get_contents($row['image']);
                        if ($imageContents === false) {
                            throw new \Exception("Gagal mengunduh konten dari URL.");
                        }
                        file_put_contents($destinationPath, $imageContents);
                    } catch (\Exception $e) {
                        throw ValidationException::withMessages([
                            'image' => ["Gagal mengunduh gambar dari URL: {$row['image']}"],
                        ]);
                    }
                }
            } else {
                // Nama file lokal
                $imageName = $row['image'];
                if (!file_exists(public_path('assets/snack_items/' . $imageName))) {
                    throw ValidationException::withMessages([
                        'image' => ["File gambar '{$imageName}' tidak ditemukan di folder assets/snack_items."],
                    ]);
                }
            }
        }

        // Cek apakah snack dengan nama sama sudah ada
        $existingSnack = Snack::where('name', $row['name'])->first();

        if ($existingSnack) {
            $existingSnack->price = $row['price'];
            $existingSnack->stock += $row['stock'] ?? 0;
            if ($imageName) {
                $existingSnack->image = $imageName;
            }
            $existingSnack->save();

            return null; // Tidak buat baru
        }

        // Buat snack baru
        return new Snack([
            'name'  => $row['name'],
            'price' => $row['price'],
            'stock' => $row['stock'] ?? 0,
            'image' => $imageName,
        ]);
    }
}
