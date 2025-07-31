<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;

class Collection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'category',
        'description',
        'price',
        'stock',
        'image',
        'layer'
    ];


    public function snacks()
    {
        return $this->belongsToMany(Snack::class, 'collection_snacks')->withPivot('quantity');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected static function booted()
    {
        // When a collection is created
        static::created(function (Collection $collection) {
            $causer = Auth::user(); // Get the currently authenticated user

            // Customizing the description
            $description = "Collection {$collection->name} with ID {$collection->id} has been created by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($collection) // The collection that was just created
                ->causedBy($causer)       // The user who created it
                ->withProperties([
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->name,
                    'collection_type' => $collection->type,
                    'collection_category' => $collection->category,
                    'collection_description' => $collection->description,
                    'collection_price' => $collection->price,
                    'collection_stock' => $collection->stock,
                    'collection_image' => $collection->image,
                    'collection_layer' => $collection->layer
                ])
                ->event('created') // Explicitly set event to 'created'
                ->log($description); // Custom description
        });

        // When a collection is updated
        static::updated(function (Collection $collection) {
            $causer = Auth::user();

            $properties = [
                'collection_id' => $collection->id,
                'collection_name' => $collection->name,
                'collection_type' => $collection->type,
                'collection_category' => $collection->category,
                'collection_description' => $collection->description,
                'collection_price' => $collection->price,
                'collection_stock' => $collection->stock,
                'collection_image' => $collection->image,
                'collection_layer' => $collection->layer
            ];

            $changes = $collection->getChanges();
            $original = $collection->getOriginal();

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
            $description = "Collection {$collection->name} with ID {$collection->id} has been updated by {$causer->name} with ID {$causer->id}. Changes = {$changeDetails}.";

            // Add old and new values to properties for more detailed auditing
            if (!empty($changes)) {
                $properties['old'] = $original;
                $properties['new'] = $changes;
            }

            activity()
                ->performedOn($collection)
                ->causedBy($causer)
                ->withProperties($properties)
                ->event('updated') // Explicitly set event to 'updated'
                ->log($description); // Custom description
        });

        // When a collection is deleted (soft deleted)
        static::deleted(function (Collection $collection) {
            $causer = Auth::user();

            // Customizing the description for delete
            $description = "Collection {$collection->name} with ID {$collection->id} has been soft deleted at {$collection->deleted_at->toDateTimeString()} by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($collection)
                ->causedBy($causer)
                ->withProperties([
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->name,
                    'collection_type' => $collection->type,
                    'collection_category' => $collection->category,
                    'collection_description' => $collection->description,
                    'collection_price' => $collection->price,
                    'collection_stock' => $collection->stock,
                    'collection_image' => $collection->image,
                    'collection_layer' => $collection->layer,
                    'deleted_at' => $collection->deleted_at->toDateTimeString(), // Tanggal soft delete
                ])
                ->event('soft_deleted') // Explicitly set event to 'deleted'
                ->log($description); // Custom description
        });

        static::forceDeleted(function (Collection $collection) {
            $causer = Auth::user();

            // Customizing the description for delete
            $description = "Collection {$collection->name} with ID {$collection->id} has been permanently deleted by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($collection)
                ->causedBy($causer)
                ->withProperties([
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->name,
                    'collection_type' => $collection->type,
                    'collection_category' => $collection->category,
                    'collection_description' => $collection->description,
                    'collection_price' => $collection->price,
                    'collection_stock' => $collection->stock,
                    'collection_image' => $collection->image,
                    'collection_layer' => $collection->layer
                ])
                ->event('permanently_deleted') // Explicitly set event to 'deleted'
                ->log($description); // Custom description
        });

        // When a collection is restored (due to SoftDeletes)
        static::restored(function (Collection $collection) {
            $causer = Auth::user();

            // Customizing the description for restore
            $description = "Collection {$collection->name} with ID {$collection->id} has been restored by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($collection)
                ->causedBy($causer)
                ->withProperties([
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->name,
                    'collection_type' => $collection->type,
                    'collection_category' => $collection->category,
                    'collection_description' => $collection->description,
                    'collection_price' => $collection->price,
                    'collection_stock' => $collection->stock,
                    'collection_image' => $collection->image,
                    'collection_layer' => $collection->layer
                ])
                ->event('restored') // Explicitly set event to 'restored'
                ->log($description); // Custom description
        });
    }
}
