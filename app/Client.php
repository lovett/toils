<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'deleted_at' => 'datetime',
    ];

    public static function listing(HasMany $relation)
    {
        $relation = $relation->selectRaw('
            clients.*,
            count(distinct projects.id) as projectCount,
            sum(times.minutes) as totalTime,
            max(times.start) as latestTime
        ');

        $relation = $relation->leftJoin('projects', function ($join) {
            $join->on('clients.id', '=', 'projects.client_id')
                ->where('projects.active', '=', 1)
                ->whereNull('projects.deleted_at');
        });

        $relation = $relation->leftJoin('times', function ($join) {
            $join->on('times.project_id', '=', 'projects.id');
        });


        $relation = $relation->orderBy('clients.updated_at', 'DESC');
        $relation = $relation->groupBy('clients.id');
        return $relation;
    }

    public function times()
    {
        return $this->hasManyThrough('App\Time', 'App\Project');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function projects()
    {
        return $this->hasMany('App\Project');
    }

    public function status()
    {
        return ($this->active) ? 'active' : 'inactive';
    }


    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}
