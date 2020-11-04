<?php

namespace App\Models;

use App\Enums\ExamStatus;
use App\Filters\Filterable;
use App\Traits\HasOwner;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @method static self filter($param)
 * @method static self owner(\Illuminate\Contracts\Auth\Authenticatable|null $user)
 * @method static self paginate($perPage)
 * @method static create(array $data)
 * @method select(array $select)
 * @method firstOrFail()
 * @method static find($id)
 * @property mixed id
 * @property mixed sections
 * @property mixed config
 * @property int|mixed status_id
 * @property mixed name
 * @property mixed description
 * @property mixed user
 * @property mixed participants
 * @property mixed user_id
 */
class Exam extends Model
{
    use Filterable, HasOwner;

    protected $fillable = ['user_id', 'name', 'description', 'code', 'visibility_status'];

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

    public function scopePublished($query)
    {
        return $query->where('status_id', ExamStatus::Publish);
    }

    /**
     * @return string
     */
    public function getStatusAttribute()
    {
        return ExamStatus::getDescription($this->status_id);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function config()
    {
        return $this->hasOne('App\Models\Config');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections()
    {
        return $this->hasMany('App\Models\Section')->orderBy('order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany('App\Models\Participant');
    }
}
