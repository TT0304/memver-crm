<?php

namespace App\Repositories\Email;

use App\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Storage;

class AttachmentRepository extends Repository
{
    /**
     * Parser object
     *
     * @var \App\Helpers\Email\Parser
     */
    protected $emailParser;

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Email\Contracts\Attachment';
    }

    /**
     * @param  \App\Helpers\Email\Parser  $emailParser
     * @return self
     */
    public function setEmailParser($emailParser)
    {
        $this->emailParser = $emailParser;

        return $this;
    }

    /**
     * @param  \App\DataStructure\Email\Contracts\Email  $email
     * @param  array $data
     * @return void
     */
    public function uploadAttachments($email, array $data)
    {
        if (! isset($data['source'])) {
            return;
        }

        if ($data['source'] == 'email') {
            foreach ($this->emailParser->getAttachments() as $attachment) {
                Storage::put($path = 'emails/' . $email->id . '/' . $attachment->getFilename(), $attachment->getContent());

                $this->create([
                    'path'         => $path,
                    'name'         => $attachment->getFileName(),
                    'content_type' => $attachment->contentType,
                    'content_id'   => $attachment->contentId,
                    'size'         => Storage::size($path),
                    'email_id'     => $email->id,
                ]);
            }
        } else {
            if (! isset($data['attachments'])) {
                return;
            }

            foreach ($data['attachments'] as $index => $attachment) {
                $this->create([
                    'path'         => $path = request()->file('attachments.' . $index)->store('emails/' . $email->id),
                    'name'         => $attachment->getClientOriginalName(),
                    'content_type' => $attachment->getClientMimeType(),
                    'size'         => Storage::size($path),
                    'email_id'     => $email->id,
                ]);
            }
        }
    }
}
