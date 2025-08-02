<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::select('id', 'name', 'role', 'is_blocked')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Role',
            'Is_blocked'
        ];
    }
}
