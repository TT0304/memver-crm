<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Common extends Mailable
{
    use Queueable, SerializesModels;

    public $data;


    /**
     * @param  array  $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the mail representation of the notification.
     */
    public function build()
    {
        $message = $this
            ->to($this->data['to'])
            ->subject($this->data['subject'])
            ->view('emails.common', [
                'body' => $this->data['body'],
            ]);

        if (isset($this->data['attachments'])) {
            foreach ($this->data['attachments'] as $attachment) {
                $message->attachData($attachment['content'], $attachment['name'], [
                    'mime' => $attachment['mime'],
                ]);
            }
        }

        return $message;
    }
}
