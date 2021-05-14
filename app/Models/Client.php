<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'clients';

    protected $fillable = [
        'client_name', 'address1', 'address2', 'city', 'state', 'country', 'latitude',
        'longitude', 'phone_no1', 'phone_no2', 'zip', 'start_validity', 'end_validity', 'status',
    ];


    public function latLngCacheKey()
    {
        /* we can update this according to our future needs to make it unique. */
        return strtolower(implode('_', [$this->address1, $this->city, $this->state, $this->zip]));
    }

    public function users()
    {
        return $this->hasMany(User::class, 'client_id');
    }
}
