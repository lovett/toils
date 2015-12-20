<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'active',
        'name',
        'contact_name',
        'contact_email',
        'address1',
        'address2',
        'city',
        'locality',
        'postal_code',
        'phone',
    ];
}
