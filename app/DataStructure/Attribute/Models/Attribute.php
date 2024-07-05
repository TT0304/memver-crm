<?php

namespace App\DataStructure\Attribute\Models;

use App\DataStructure\Attribute\Contracts\Attribute as AttributeContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Attribute extends Model implements AttributeContract
{
    // use TenantScoped;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'entity_type',
        'lookup_type',
        'is_required',
        'is_unique',
        'quick_add',
        'validation',
        'is_user_defined',
        'client_id',
    ];

    /**
     * Get the options.
     */
    public function options()
    {
        return $this->hasMany(AttributeOptionProxy::modelClass());
    }
}
