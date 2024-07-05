<?php

namespace App\DataStructure\Activity\Models;

use App\DataStructure\Activity\Contracts\Participant as ParticipantContract;
use App\DataStructure\Contact\Models\PersonProxy;
use App\DataStructure\User\Models\UserProxy;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Participant extends Model implements ParticipantContract
{
    // use TenantScoped;
    public $timestamps = false;

    protected $table = 'activity_participants';

    protected $with = ['user', 'person'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'activity_id',
        'user_id',
        'person_id',
        'client_id',
    ];

    /**
     * Get the activity that owns the participant.
     */
    public function activity()
    {
        return $this->belongsTo(ActivityProxy::modelClass());
    }

   /**
    * Get the user that owns the participant.
    */
   public function user()
   {
       return $this->belongsTo(UserProxy::modelClass());
   }

   /**
    * Get the person that owns the participant.
    */
   public function person()
   {
       return $this->belongsTo(PersonProxy::modelClass());
   }
}
