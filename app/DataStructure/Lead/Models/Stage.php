<?php

namespace App\DataStructure\Lead\Models;

use App\DataStructure\Lead\Contracts\Stage as StageContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Stage extends Model implements StageContract
{
    // use TenantScoped;
    public $timestamps = false;

    protected $table = 'lead_pipeline_stages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'probability',
        'sort_order',
        'lead_pipeline_id',
        'client_id',
    ];

    /**
     * Get the pipeline that owns the pipeline stage.
     */
    public function pipeline()
    {
        return $this->belongsTo(PipelineProxy::modelClass(), 'lead_pipeline_id');
    }

    /**
     * Get the leads.
     */
    public function leads()
    {
        return $this->hasMany(LeadProxy::modelClass(), 'lead_pipeline_stage_id');
    }
}
