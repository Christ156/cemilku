<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MysteryBox extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'budget',
        'mood',
        'stock',
    ];

    /**
     * The snacks that belong to the MysteryBox.
     * Many-to-many relationship with the Snack model.
     */
    public function snacks()
    {
        return $this->belongsToMany(Snack::class, 'mystery_box_snack', 'mystery_box_id', 'snack_id')
                    ->withPivot('quantity');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Atau logOnly(['name', 'email'])
            ->dontSubmitEmptyLogs(); // Hindari log kosong jika tidak ada perubahan
    }
}
