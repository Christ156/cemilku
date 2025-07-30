<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    // 2 create, update

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_price',
        'payment_method',
        'status',
        'address_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected static function booted()
    {
        // When an order is created
        static::created(function (Order $order) {
            $causer = Auth::user(); // Get the currently authenticated user

            // Customizing the description
            $description = "Order ID {$order->id} with total price {$order->total_price} has been created.";

            activity()
                ->performedOn($order) // The order that was just created
                ->causedBy($causer)   // The user who created it
                ->withProperties([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'total_price' => $order->total_price,
                    'payment_method' => $order->payment_method,
                    'status' => $order->status,
                ])
                ->event('created') // Explicitly set event to 'created'
                ->log($description); // Custom description
        });

        // When an order is updated
        static::updated(function (Order $order) {
            $causer = Auth::user();

            $properties = [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'total_price' => $order->total_price,
                'payment_method' => $order->payment_method,
                'status' => $order->status,
            ];

            $changes = $order->getChanges();
            $original = $order->getOriginal();

            $changeMessages = [];
            foreach ($changes as $attribute => $newValue) {
                if ($attribute === 'updated_at') {
                    continue;
                }
                $oldValue = $original[$attribute] ?? 'null';
                // Handle potential sensitive data or complex types for display
                if (is_array($oldValue) || is_object($oldValue)) {
                    $oldValue = json_encode($oldValue);
                }
                if (is_array($newValue) || is_object($newValue)) {
                    $newValue = json_encode($newValue);
                }
                $changeMessages[] = "{$attribute} from '{$oldValue}' to '{$newValue}'";
            }

            $changeDetails = implode(', ', $changeMessages);

            // Customizing the description for update
            $description = "Order ID {$order->id} has been updated. Changes = {$changeDetails}.";

            // Add old and new values to properties for more detailed auditing
            if (!empty($changes)) {
                $properties['old'] = $original;
                $properties['new'] = $changes;
            }

            activity()
                ->performedOn($order)
                ->causedBy($causer)
                ->withProperties($properties)
                ->event('updated') // Explicitly set event to 'updated'
                ->log($description); // Custom description
        });
    }
}
