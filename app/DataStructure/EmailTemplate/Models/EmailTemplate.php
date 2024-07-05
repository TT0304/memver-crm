<?php

namespace App\DataStructure\EmailTemplate\Models;

use Illuminate\Database\Eloquent\Model;
use App\DataStructure\EmailTemplate\Contracts\EmailTemplate as EmailTemplateContract;
use App\Traits\TenantScoped;

class EmailTemplate extends Model implements EmailTemplateContract
{
    // use TenantScoped;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'client_id',
    ];
}