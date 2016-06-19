<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Time extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'start',
        'end',
        'summary',
        'estimated_duration',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'summary' => 'string',
        'estimated_duration' => 'integer'
    ];

    protected $dates = [
        'deleted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /* Disabled until invoice model has been created
    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }*/

    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }


}