<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

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

    protected $dates = [
        'deleted_at' => 'datetime'
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

    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

}
