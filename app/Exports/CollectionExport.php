<?php

namespace App\Exports;

use App\Models\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CollectionExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Ambil data collections beserta relasi snacks
     */
    public function collection()
    {
        return Collection::with('snacks')->get([
            'id',
            'category',
            'name',
            'type',
            'description',
            'price',
            'stock',
            'image',
            'layer',
            'created_at'
        ]);
    }

    /**
     * Mapping data setiap baris
     */
    public function map($collection): array
    {
        // Gabungkan semua snack menjadi satu string
        $snacks = $collection->snacks->map(function ($snack) {
            return $snack->name . ' x ' . ($snack->pivot->quantity ?? 1);
        })->implode(', ');

        return [
            $collection->id,
            $collection->category,
            $collection->name,
            $collection->type,
            $collection->description,
            $collection->price,
            $collection->stock,
            $collection->image,
            $collection->layer,
            $snacks,
            $collection->created_at,
        ];
    }

    /**
     * Header untuk file Excel
     */
    public function headings(): array
    {
        return [
            'id',
            'category',
            'name',
            'type',
            'description',
            'price',
            'stock',
            'image',
            'layer',
            'snacks',
            'created_at',
        ];
    }
}
