<?php

namespace App\DataStructure\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use App\DataStructure\Workflow\Contracts\Workflow as WorkflowContract;
use App\Traits\TenantScoped;

class Workflow extends Model implements WorkflowContract
{
    // use TenantScoped;
    protected $casts = [
        'conditions' => 'array',
        'actions'    => 'array',
    ];

    protected $fillable = [
        'name',
        'description',
        'entity_type',
        'event',
        'condition_type',
        'conditions',
        'actions',
        'client_id',
    ];
}