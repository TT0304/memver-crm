<?php

namespace App\DataStructure\Lead\Models;

use App\DataStructure\Email\Models\MemberProxy;
use App\DataStructure\Lead\Contracts\Member as MemberContract;
use App\Traits\Attribute\CustomAttribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\DataStructure\Contact\Models\OrganizationProxy;
use App\Traits\TenantScoped;

class Member extends Model implements MemberContract
{
    use CustomAttribute;
    // use TenantScoped;
    
    protected $table = 'members';

    protected $with = 'organization';

    protected $casts = [
        'email'  => 'array',
        'mobile' => 'array',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'email',
        'mobile',
        'national_id',
        'preferred_language',
        'password',
        'status',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        'gender',
        'channel',
        'photo',
        'member_since',
        'city_id',
        'referred_by',
        'arabic_name',
        'english_name',
        'dob',
        'organization_id',
        'member_type',
        'client_id',
        'default_branch',
        'parent_mobile',
        'parent_name',
        'referral_code',
        'fcm_token',
        'removed_by_member',
    ];
    
    /**
     * Get the organization that owns the person.
     */
    public function organization()
    {
        return $this->belongsTo(OrganizationProxy::modelClass());
    }
}
