<?php

namespace App\Exports;

use App\Models\Snack;
use Maatwebsite\Excel\Concerns\FromCollection;

class SnackExport implements FromCollection
{
    public function collection()
    {
        return Snack::all();
    }
}
