<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customize extends Model
{
    use SoftDeletes, LogsActivity;


    protected $fillable = ['name', 'type', 'price', 'image', 'layer'];

    public function snacks() {
        return $this->belongsToMany(Snack::class, 'customize_snacks')->withPivot('quantity');
    }

    public function decorations() {
        return $this->belongsToMany(Decoration::class, 'customize_decorations');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Atau logOnly(['name', 'email'])
            ->dontSubmitEmptyLogs(); // Hindari log kosong jika tidak ada perubahan
    }
}
