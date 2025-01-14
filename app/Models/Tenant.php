<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * class Tenant
 *
 * @property int $user_id;
 * @property string $first_name;
 * @property string $last_name;
 * @property string $email;
 * @property string $address;
 * @property string $city;
 * @property string $post_code;
 * @property string $country;
 */
class Tenant extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'address',
        'city',
        'post_code',
        'country',
    ];

    /**
     * @var bool
     */
    public $timestamps =  true;

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
