<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;

class Address extends Model
{

    use HasFactory;

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'user_id',
        'label',
        'provinsi',
        'kota_kabupaten',
        'kecamatan',
        'kelurahan_desa',
        'rt',
        'rw',
        'kode_pos',
        'address',
        'is_primary',
        'receiver_name',
        'phone_number'
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        // When an address is created
        static::created(function (Address $address) {
            $causer = Auth::user(); // Get the currently authenticated user

            // Customizing the description
            $description = "Address for user ID {$address->user_id} with label {$address->label} has been created.";

            activity()
                ->performedOn($address) // The address that was just created
                ->causedBy($causer)    // The user who created it
                ->withProperties([
                    'address_id' => $address->id,
                    'user_id' => $address->user_id, // User who owns this address
                    'label' => $address->label,
                    'provinsi' => $address->provinsi,
                    'kota_kabupaten' => $address->kota_kabupaten,
                    'kecamatan' => $address->kecamatan,
                    'kelurahan_desa' => $address->kelurahan_desa,
                    'rt' => $address->rt,
                    'rw' => $address->rw,
                    'kode_pos' => $address->kode_pos,
                    'address_detail' => $address->address, // renamed to avoid confusion with model
                    'is_primary' => $address->is_primary,
                    'receiver_name' => $address->receiver_name,
                    'phone_number' => $address->phone_number
                ])
                ->event('created') // Explicitly set event to 'created'
                ->log($description); // Custom description
        });

        // When an address is updated
        static::updated(function (Address $address) {
            $causer = Auth::user();

            $properties = [
                'address_id' => $address->id,
                'user_id' => $address->user_id, // User who owns this address
                'label' => $address->label,
                'provinsi' => $address->provinsi,
                'kota_kabupaten' => $address->kota_kabupaten,
                'kecamatan' => $address->kecamatan,
                'kelurahan_desa' => $address->kelurahan_desa,
                'rt' => $address->rt,
                'rw' => $address->rw,
                'kode_pos' => $address->kode_pos,
                'address_detail' => $address->address, // renamed to avoid confusion with model
                'is_primary' => $address->is_primary,
                'receiver_name' => $address->receiver_name,
                'phone_number' => $address->phone_number
            ];

            $changes = $address->getChanges();
            $original = $address->getOriginal();

            $changeMessages = [];
            foreach ($changes as $attribute => $newValue) {
                if ($attribute === 'updated_at') {
                    continue;
                }
                $oldValue = $original[$attribute] ?? 'null';
                $changeMessages[] = "{$attribute} from '{$oldValue}' to '{$newValue}'";
            }

            $changeDetails = implode(', ', $changeMessages);

            // Customizing the description for update
            $description = "Address for user ID '{$address->user_id}' with label '{$address->label}' has been updated. Changes = {$changeDetails}.";
            // Alternative: If you only want changes like "label from X to Y":
            // $description = "Address changes: {$changeDetails}.";


            // Add old and new values to properties for more detailed auditing
            if (!empty($changes)) {
                $properties['old'] = $original;
                $properties['new'] = $changes;
            }

            activity()
                ->performedOn($address)
                ->causedBy($causer)
                ->withProperties($properties)
                ->event('updated') // Explicitly set event to 'updated'
                ->log($description); // Custom description
        });

        // When an address is deleted
        static::deleted(function (Address $address) {
            $causer = Auth::user();

            // Customizing the description for delete
            $description = "Address for user ID '{$address->user_id}' with label '{$address->label}' has been deleted.";

            activity()
                ->performedOn($address)
                ->causedBy($causer)
                ->withProperties([
                    'address_id' => $address->id,
                    'user_id' => $address->user_id, // User who owns this address
                    'label' => $address->label,
                    'provinsi' => $address->provinsi,
                    'kota_kabupaten' => $address->kota_kabupaten,
                    'kecamatan' => $address->kecamatan,
                    'kelurahan_desa' => $address->kelurahan_desa,
                    'rt' => $address->rt,
                    'rw' => $address->rw,
                    'kode_pos' => $address->kode_pos,
                    'address_detail' => $address->address, // renamed to avoid confusion with model
                    'is_primary' => $address->is_primary,
                    'receiver_name' => $address->receiver_name,
                    'phone_number' => $address->phone_number
                ])
                ->event('deleted') // Explicitly set event to 'deleted'
                ->log($description); // Custom description
        });
    }
}
