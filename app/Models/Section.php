<?php

namespace App\Models;

use App\Filters\Filterable;
use App\Traits\HasOwner;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @method static self filter($param)
 * @method static self where(int|null $id)
 * @method static self owner(\Illuminate\Contracts\Auth\Authenticatable|null $user)
 * @method static self paginate($perPage)
 * @method static create(array $data)
 * @method static find($section_id)
 * @property mixed id
 * @property mixed instruction
 * @property mixed order
 * @property mixed exam
 * @property mixed questions
 * @property mixed exam_id
 * @property mixed user_id
 */
class Section extends Model
{
    use Filterable, HasOwner;

    protected $fillable = ['user_id', 'exam_id', 'name', 'instruction', 'score_per_question', 'passing_grade','order', 'time_limit'];

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
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo('App\Models\Exam');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany('App\Models\Question')->orderBy('order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function randomQuestions()
    {
        return $this->hasMany('App\Models\Question')->inRandomOrder();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany('App\Models\Answer');
    }
}
