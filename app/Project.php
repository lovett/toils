<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use SoftDeletes;

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

    protected $dates = [
        'deleted_at' => 'datetime'
    ];

    public static function listing(HasMany $relation)
    {
        $relation = $relation->with('client');
        $relation = $relation->orderByRaw('LOWER(projects.name) ASC');
        return $relation;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function durationMinutes()
    {
        return $this->end->diffInMinutes($this->start);
    }

    public function time()
    {
        return $this->hasMany('App\Time');
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
}
