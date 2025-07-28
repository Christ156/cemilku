<?php

namespace App\Imports;

use App\Models\Decoration;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;

class DecorationImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        $existing = Decoration::where('name', $row['name'])->first();

        if ($existing) {
            $existing->stock += $row['stock'] ?? 0;
            $existing->price = $row['price'];
            $existing->save();

            return null;
        }

        return new Decoration([
            'name'  => $row['name'],
            'price' => $row['price'],
            'stock' => $row['stock'] ?? 0,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string', 'max:255'],
            '*.price' => ['required', 'numeric', 'min:0'],
            '*.stock' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.name.required' => 'Nama dekorasi wajib diisi.',
            '*.name.max' => 'Nama dekorasi tidak boleh lebih dari 255 karakter.',
            '*.price.required' => 'Harga wajib diisi.',
            '*.price.numeric' => 'Harga harus berupa angka.',
            '*.price.min' => 'Harga tidak boleh negatif.',
            '*.stock.integer' => 'Stok harus berupa bilangan bulat.',
            '*.stock.min' => 'Stok tidak boleh negatif.',
        ];
    }
}
