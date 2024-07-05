<?php

namespace App\DataStructure\Lead\Models;

use App\DataStructure\Lead\Contracts\Source as SourceContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Source extends Model implements SourceContract
{
    // use TenantScoped;
    protected $table = 'lead_sources';

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
