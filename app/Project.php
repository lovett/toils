<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'active',
        'name',
        'billable',
        'tax_deducted',
    ];

    protected $casts = [
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
        return ($this->tax_deducted) ? 'taxed' : 'untaxed';
    }
}
