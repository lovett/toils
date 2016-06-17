<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Project extends Model
{
    protected $fillable = [
        'active',
        'name',
        'billable',
        'tax_deducted',
    ];

    protected $casts = [
        'id' => 'integer',
        'active' => 'boolean',
        'name' => 'string',
        'billable' => 'boolean',
        'tax_deducted' => 'boolean',
        'user_id' => 'integer',
        'client_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function status()
    {
        return ($this->active) ? 'active' : 'inactive';
    }

    public function taxStatus()
    {
        return ($this->tax_deducted) ? 'deducted' : 'not deducted';
    }

    public function billableStatus()
    {
        return ($this->billable) ? 'yes' : 'no';
    }

    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}
