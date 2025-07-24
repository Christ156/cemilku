<?php

namespace App\Exports;

use App\Models\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CollectionExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Ambil hanya kolom yang diperlukan dari tabel collections
     */
    public function collection()
    {
        return Collection::all([
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
     * Atur urutan dan isi data setiap baris
     */
    public function map($collection): array
    {
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
            'created_at',
        ];
    }
}
