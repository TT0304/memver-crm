<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;

trait TenantScoped
{

    public static function boot()
    {
        parent::boot();
        if (auth()->guard('client_users')->check()) {
            $currentTenantID = optional(auth()->user())->client_id;
            \Log::error($currentTenantID);
            if($currentTenantID != null){
                self::creating(function($model) use ($currentTenantID) {
                    $model->client_id = $currentTenantID;
                });

                self::addGlobalScope(function(Builder $builder) use ($currentTenantID) {
                    // $builder->where('client_id', $currentTenantID);
                    if ($builder->getModel() instanceof \App\Models\Client) {
                        $builder->where('id', $currentTenantID);
                    } else {
                        $tableName = $builder->getModel()->getTable();
                        $builder->where( $tableName.'.'.'client_id', $currentTenantID);
                    }
                });

                // Attach a deleting event listener to remove related sessions
                static::deleting(function ($model) {
                  
                    \Log::info($model);

                    if ($model instanceof \App\Models\ClassSchedule && method_exists($model, 'sessions')) {
                        $model->sessions()->delete();
                        \Log::info('delete...');
                    } else{
                        \Log::info('no delete...');
                    }
                   
                });


            } else{
                \Log::error("no client for this user");
            }
        } else{
            \Log::error("no client_users guard");
        }
        
    }
}
