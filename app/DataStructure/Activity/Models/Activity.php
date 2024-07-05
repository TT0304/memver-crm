<?php

namespace App\DataStructure\Activity\Models;

use App\DataStructure\Activity\Contracts\Activity as ActivityContract;
use App\DataStructure\Lead\Models\LeadProxy;
use App\DataStructure\User\Models\UserProxy;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Activity extends Model implements ActivityContract
{
    // use TenantScoped;

    protected $table = 'activities';

    protected $with = ['file', 'user'];

    protected $dates= [
        'schedule_from',
        'schedule_to',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'type',
        'location',
        'comment',
        'additional',
        'schedule_from',
        'schedule_to',
        'is_done',
        'user_id',
        'client_id',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * The participants that belong to the activity.
     */
    public function participants()
    {
        return $this->hasMany(ParticipantProxy::modelClass());
    }

    /**
     * Get the file associated with the activity.
     */
    public function file()
    {
        return $this->hasOne(FileProxy::modelClass(), 'activity_id');
    }

    /**
     * The leads that belong to the activity.
     */
    public function leads()
    {
        return $this->belongsToMany(LeadProxy::modelClass(), 'lead_activities');
    }
}
