<?php

namespace App\Exports;

use App\Models\Snack;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SnackExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Snack::select('id', 'name', 'description', 'price', 'stock')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Price',
            'Stock',
        ];
    }
}
