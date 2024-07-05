<?php

namespace App\DataStructure\Contact\Models;

use App\DataStructure\Contact\Contracts\Person as PersonContract;
use App\Traits\Attribute\CustomAttribute;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Person extends Model implements PersonContract
{
    use CustomAttribute;
    // use TenantScoped;

    protected $table = 'persons';

    protected $with = 'organization';

    protected $casts = [
        'emails'          => 'array',
        'contact_numbers' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'emails',
        'contact_numbers',
        'organization_id',
        'client_id',
    ];

    /**
     * Get the organization that owns the person.
     */
    public function organization()
    {
        return $this->belongsTo(OrganizationProxy::modelClass());
    }
}
