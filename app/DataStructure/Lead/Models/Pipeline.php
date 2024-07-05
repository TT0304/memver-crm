<?php

namespace App\DataStructure\Lead\Models;

use App\DataStructure\Lead\Contracts\Pipeline as PipelineContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Pipeline extends Model implements PipelineContract
{
    // use TenantScoped;
    protected $table = 'lead_pipelines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'rotten_days',
        'is_default',
        'client_id',
    ];

    /**
     * Get the leads.
     */
    public function leads()
    {
        return $this->hasMany(LeadProxy::modelClass(), 'lead_pipeline_id');
    }

    /**
     * Get the stages that owns the pipeline.
     */
    public function stages()
    {
        return $this->hasMany(StageProxy::modelClass(), 'lead_pipeline_id')->orderBy('sort_order', 'ASC');
    }
}
