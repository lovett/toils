<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for the tags table.
 */
class Tag extends Model
{

    /**
     * This model does not have created_at and updated_at fields.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Time entries associated with a tag.
     */
    public function time()
    {
        return $this->morphedByMany('App\Time', 'taggable');
    }

    /**
     * Given a comma-delimited list of tags, create any that
     * don't already exist and assign them to the given record.
     *
     * @param object $record        The object to sync with. Must have a tags relationship.
     * @param string $delimitedList A comma-delimited list of tag names.
     *
     * @return Tag[] $tags The tags that have been synced to the record.
     */
    public static function syncFromList($record, string $delimitedList = null)
    {
        $delimitedList = strtolower($delimitedList);
        $delimitedList = trim($delimitedList);

        $tags = [];

        if (empty($delimitedList) === false) {
            $names = explode(',', $delimitedList);
            $names = array_map('trim', $names);
            $names = array_unique($names);

            $tags = static::whereIn('name', $names)->get();

            $existingNames = $tags->map(function ($tag) {
                return $tag->name;
            });

            foreach ($names as $name) {
                if ($existingNames->contains($name) === false) {
                    $tag = self::create(['name' => $name]);
                    $tags->push($tag);
                }
            }
        }

        $record->tags()->sync($tags);

        return $tags;
    }
}
