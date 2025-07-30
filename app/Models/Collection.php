<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Collection extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'type', 'category', 'description', 'price', 'stock', 'image', 'layer'
    ];


    public function snacks()
    {
        return $this->belongsToMany(Snack::class, 'collection_snacks')->withPivot('quantity');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Atau logOnly(['name', 'email'])
            ->dontSubmitEmptyLogs(); // Hindari log kosong jika tidak ada perubahan
    }
}
