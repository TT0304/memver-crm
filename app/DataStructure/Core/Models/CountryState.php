<?php

namespace App\DataStructure\Core\Models;

use App\DataStructure\Core\Contracts\CountryState as CountryStateContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class CountryState extends Model implements CountryStateContract
{
    // use TenantScoped;
    public $timestamps = false;
    protected $fillable = [
        'client_id',
    ];
}
