<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @property mixed exam_id
 * @method static create(array $defaultConfig)
 */
class Config extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'time_limit',
        'time_mode',
        'question_order',
        'answer_order',
        'result_status',
        'ranking_status',
        'score_status',
        'passing_grade_status',
        'default_score',
        'default_passing_grade',
    ];

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::uuid4();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo('App\Models\Exam');
    }
}
