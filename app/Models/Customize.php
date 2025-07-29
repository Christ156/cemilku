<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customize extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'type', 'price', 'image', 'layer'];

    public function snacks() {
        return $this->belongsToMany(Snack::class, 'customize_snacks')->withPivot('quantity');
    }

    public function decorations() {
        return $this->belongsToMany(Decoration::class, 'customize_decorations');
    }
}
