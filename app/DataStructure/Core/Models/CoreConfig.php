<?php

namespace App\DataStructure\Core\Models;

use App\DataStructure\Core\Contracts\CoreConfig as CoreConfigContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class CoreConfig extends Model implements CoreConfigContract
{
    // use TenantScoped;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'core_config';

    protected $fillable = [
        'code',
        'value',
        'locale',
        'client_id',
    ];

    protected $hidden = ['token'];
}
