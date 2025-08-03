<?php

namespace App\Exports;

use App\Models\Decoration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DecorationExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Decoration::select('name', 'price', 'stock')->get();
    }

    public function headings(): array
    {
        return ['Name', 'Price', 'Stock'];
    }
}
