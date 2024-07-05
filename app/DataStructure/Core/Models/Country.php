<?php

namespace App\DataStructure\Core\Models;

use App\DataStructure\Core\Contracts\Country as CountryContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Country extends Model implements CountryContract
{
    // use TenantScoped;
    public $timestamps = false;
    protected $fillable = [
        'client_id',
    ];
}
