<?php
namespace App\Models;

use App\Models\Address;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    // relasi one to many dari user terhadap address
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function mainAddress()
    {
        return $this->hasOne(Address::class)->where('is_primary', 1);
    }

    //relasi one to many dari user terhadap order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'date_of_birth',
        'gender',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    protected static function booted()
    {
        // When a user is created
        static::created(function (User $user) {
            $causer = Auth::user();

            // Customizing the description
            $description = "User with name {$user->name}, id {$user->id}, and email {$user->email} has been created.";

            activity()
                ->performedOn($user)
                ->causedBy($causer)
                ->withProperties([
                    'created_user_id' => $user->id,
                    'created_user_name' => $user->name,
                    'created_user_email' => $user->email,
                    'created_phone_number' => $user->phone_number,
                    'created_date_of_birth' => $user->date_of_birth,
                    'created_gender' => $user->gender,
                    'created_profile_image' => $user->profile_image
                ])
                ->event('created')
                ->log($description); // Use the custom description here
        });

        static::updated(function (User $user) {
            $causer = Auth::user();

            $properties = [
                'updated_user_id' => $user->id,
                'updated_user_name' => $user->name,
                'updated_user_email' => $user->email,
                'updated_phone_number' => $user->phone_number,
                'updated_date_of_birth' => $user->date_of_birth,
                'updated_gender' => $user->gender,
                'updated_profile_image' => $user->profile_image
            ];

            $changes = $user->getChanges();
            $original = $user->getOriginal(); // Get all original values

            $changeMessages = [];
            foreach ($changes as $attribute => $newValue) {
                // Skip 'updated_at' if you don't want to log every save
                if ($attribute === 'updated_at') {
                    continue;
                }

                // Get the old value for this specific attribute
                $oldValue = $original[$attribute] ?? 'null'; // Use 'null' if old value doesn't exist (e.g., for new attributes)

                // Add a message for each changed attribute
                $changeMessages[] = "{$attribute} from {$oldValue} to {$newValue}";
            }

            // Combine individual change messages into a readable string
            $changeDetails = implode(', ', $changeMessages);

            // Customize the description for update
            $description = "Changes = {$changeDetails}";

            // Add old and new values to properties for more detailed auditing
            if (!empty($changes)) {
                $properties['old'] = $original; // Store all original values
                $properties['new'] = $changes;  // Store only the changed new values
            }

            activity()
                ->performedOn($user)
                ->causedBy($causer)
                ->withProperties($properties)
                ->event('updated')
                ->log($description);
        });

        // When a user is deleted
        static::deleted(function (User $user) {
            $causer = Auth::user();

            // Customizing the description for delete
            $description = "User with name '{$user->name}', id '{$user->id}'  has been deleted.";

            activity()
                ->performedOn($user)
                ->causedBy($causer)
                ->withProperties([
                    'deleted_user_id' => $user->id,
                    'deleted_user_name' => $user->name,
                    'deleted_user_email' => $user->email,
                    'deleted_phone_number' => $user->phone_number,
                    'deleted_date_of_birth' => $user->date_of_birth,
                    'deleted_gender' => $user->gender,
                    'deleted_profile_image' => $user->profile_image
                ])
                ->event('deleted')
                ->log($description);
        });
    }
}
