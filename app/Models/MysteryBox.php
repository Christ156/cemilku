<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MysteryBox extends Model
{
    use HasFactory;

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
}
