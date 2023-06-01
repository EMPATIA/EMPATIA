<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericEmail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $content;
    public $subject;
    public $senderName;
    public $senderEmail;
    public $destinyEmail;
    public $filesToSend;
    
    /**
     * Create a new message instance.
     *
     * @param $email
     */
    public function __construct($email)
    {
        $this->content = $email['content'];
        $this->subject = $email['subject'];
        $this->senderName = $email['sender_name'];
        $this->senderEmail = $email['no_reply'];
        $this->destinyEmail = $email['to'];
        $this->filesToSend = $email['attachments'];
    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this
            ->view('emails.rich')
            ->text("emails.simple")
            ->to($this->destinyEmail)
            ->from($this->senderEmail,$this->senderName)
            ->subject($this->subject);
        
        if (!empty($this->filesToSend)) {
            foreach ($this->filesToSend as $attachment) {
                if(isset($attachment['path']) && isset($attachment['as']) && isset($attachment['type'])){
                    $this->attach($attachment['path'], [
                        'as'   => $attachment['as'],
                        'mime' => $attachment['type'],
                    ]);
                }
            }
        }
        return $this;
        
    }
}
