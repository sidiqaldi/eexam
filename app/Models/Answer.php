<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @property mixed question
 * @property mixed section_id
 * @property mixed section
 * @property mixed uuid
 * @property mixed option_id
 * @property mixed id
 * @property mixed is_correct
 * @property int|mixed score
 * @method static withSectionOrder()
 * @method static withOptionUuid()
 */
class Answer extends Model
{
    protected $fillable = ['user_id', 'participant_id', 'section_id', 'question_id', 'start_at', 'finish_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'start_at',
        'finish_at',
    ];

    /**
     * Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string)Uuid::uuid4();
        });
    }

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function option()
    {
        return $this->belongsTo('App\Models\Option');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo('App\Models\Section');
    }

    /**
     * @param $query
     */
    public function scopeWithSectionOrder($query)
    {
        $query->addSelect(['section_order' => Section::query()->select('order')
            ->whereColumn('section_id', 'sections.id')
            ->take(1)
        ]);
    }

    public function scopeWithOptionUuid($query)
    {
        $query->addSelect(['option_uuid' => Option::query()->select('uuid')
            ->whereColumn('option_id', 'options.id')
            ->take(1)
        ]);
    }
}
