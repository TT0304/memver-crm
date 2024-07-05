<?php

namespace App\DataStructure\Lead\Models;

use App\DataStructure\Lead\Contracts\Type as TypeContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Type extends Model implements TypeContract
{
    // use TenantScoped;
    protected $table = 'lead_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'client_id',
    ];

    /**
     * Get the leads.
     */
    public function leads()
    {
        return $this->hasMany(LeadProxy::modelClass());
    }
}
