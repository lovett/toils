<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function time()
    {
        return $this->morphedByMany('App\Time', 'taggable');
    }

    /**
     * Given a comma-delimited list of tags, create any that
     * don't already exist and assign them to the given record.
     *
     * @param object $record The object to sync with. Must have a tags relationship.
     * @param string $delimitedList A comma-delimited list of tag names.
     * @return Tag[] $tags The tags that have been synced to the record.
     */
    public static function syncFromList($record, $delimitedList=null)
    {
        $delimitedList = strtolower($delimitedList);
        $delimitedList = trim($delimitedList);

        $tags = [];

        if (!empty($delimitedList)) {
            $names = explode(',', $delimitedList);
            $names = array_map('trim', $names);
            $names = array_unique($names);

            $tags = Tag::whereIn('name', $names)->get();

            $existingNames = $tags->map(function ($tag) {
                return $tag->name;
            });

            foreach ($names as $name) {
                if ($existingNames->contains($name) === false) {
                    $tag = Tag::create(['name' => $name]);
                    $tags->push($tag);
                }
            }
        }

        $record->tags()->sync($tags);

        return $tags;
    }
}
