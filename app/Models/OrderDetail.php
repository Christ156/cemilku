<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderDetail extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = ['order_id', 'collection_id', 'customize_id', 'quantity', 'price'];

    public function customize()
    {
        return $this->belongsTo(Customize::class, 'customize_id');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function mysteryBox()
    {
        return $this->belongsTo(MysteryBox::class, 'mysterybox_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()          // Atau logOnly(['name', 'email'])
            ->dontSubmitEmptyLogs(); // Hindari log kosong jika tidak ada perubahan
    }
}
