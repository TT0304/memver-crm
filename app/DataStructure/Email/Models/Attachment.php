<?php

namespace App\DataStructure\Email\Models;

use App\DataStructure\Email\Contracts\Attachment as AttachmentContract;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScoped;

class Attachment extends Model implements AttachmentContract
{
    // use TenantScoped;
    protected $table = 'email_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'size',
        'content_type',
        'content_id',
        'email_id',
        'client_id',
    ];

    /**
     * Get the email.
     */
    public function email()
    {
        return $this->belongsTo(EmailProxy::modelClass());
    }
}
