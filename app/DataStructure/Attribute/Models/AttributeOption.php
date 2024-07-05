<?php

namespace App\DataStructure\Attribute\Models;

use App\DataStructure\Attribute\Contracts\AttributeOption as AttributeOptionContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class AttributeOption extends Model implements AttributeOptionContract
{
    // use TenantScoped;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sort_order',
        'attribute_id',
        'client_id',
    ];

    /**
     * Get the attribute that owns the attribute option.
     */
    public function attribute()
    {
        return $this->belongsTo(AttributeProxy::modelClass());
    }
}
