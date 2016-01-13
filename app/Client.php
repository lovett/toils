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

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function projects()
    {
        return $this->hasMany('App\Project');
    }

    public function numericPhone() {
        $numericPhone = preg_replace('/x.*/i', '', $this->phone);
        return preg_replace('/[^0-9]/', '', $numericPhone);
    }

}
