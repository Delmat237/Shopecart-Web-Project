<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $bodyContent;

    public function __construct(string $subject, string $body)
    {
        $this->subjectLine = $subject;
        $this->bodyContent = $body;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->markdown('emails.generic');
    }
}