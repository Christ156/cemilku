<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;

class OrderDetail extends Model
{
    // 1 create

    use SoftDeletes;

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

    protected static function booted()
    {
        // When an order detail is created
        static::created(function (OrderDetail $orderDetail) {
            $causer = Auth::user(); // Get the currently authenticated user

            // Customizing the description
            $description = "Order Detail ID {$orderDetail->id} has been created for Order ID {$orderDetail->order_id} where Quantity: {$orderDetail->quantity} and Price: {$orderDetail->price}.";

            activity()
                ->performedOn($orderDetail) // The order detail that was just created
                ->causedBy($causer)          // The user who created it
                ->withProperties([
                    'order_detail_id' => $orderDetail->id,
                    'order_id' => $orderDetail->order_id,
                    'quantity' => $orderDetail->quantity,
                    'price' => $orderDetail->price,
                    'collection_id' => $orderDetail->collection_id, // Include all possible IDs
                    'customize_id' => $orderDetail->customize_id,
                ])
                ->event('created') // Explicitly set event to 'created'
                ->log($description); // Custom description
        });

    }
}
