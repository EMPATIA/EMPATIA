<?php

namespace App\Console\Commands;

use App\Mail\GenericEmail;
use App\Models\Backend\Notifications\Email;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Mail;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command used to send emails';
    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            $sent = 0;
            logDebug("[Command SendEmails: handle] init");
            logDebug("[Command SendEmails: handle] used parameters: ".
                json_encode(['max_allowed_errors_per_email' => config('mail.max_allowed_errors'), 'number_of_emails_to_send' => config('mail.chunk_size')]));
            
            // FETCH EMAILS TO SEND
            $emailsToSend = Email::whereSent(false)
                ->where("errors","<",config('mail.max_allowed_errors'))
                ->orderBy('created_at', 'asc')
                ->take(config('mail.chunk_size'))
                ->get();
            
            logDebug("[Command SendEmails: handle] number of emails to send: ".$emailsToSend->count());
            
            // SEND EMAILS
            if(!empty($emailsToSend)){
                foreach ($emailsToSend as $email){
                    $result = $this->send($email);
                    if($result){
                        $sent++;
                    }
                }
            }
            logDebug("[Command SendEmails: handle] number of emails sent: ".$sent);
            logDebug("[Command SendEmails: handle] finish");
        } catch (\Exception $e) {
            logError("[Command SendEmails: handle] Error: '{$e->getMessage()}' | File: '{$e->getFile()}' | Line: {$e->getLine()}");
        } catch (\Throwable $t) {
            logError("[Command SendEmails: handle] Error: '{$t->getMessage()}' | File: '{$t->getFile()}' | Line: {$t->getLine()}");
        }
    }
    
    public function send($email)
    {
        try {
            logDebug("[Command SendEmails: send] init");
            // BUILD MESSAGE FOR GENERIC EMAIL
            $emailMessage = [];
            $emailMessage['to'] = $email->user_email;
            $emailMessage['no_reply'] = $email->from_email;
            $emailMessage['sender_name'] = $email->from_name;
            $emailMessage['subject'] = $email->subject;
            $emailMessage['content'] = html_entity_decode($email->content);
            $emailMessage['attachments'] = [];
            //TODO: prepare email attachments
            Mail::to($emailMessage['to'])->send(new GenericEmail($emailMessage));
            $email->sent = true;
            $email->sent_at = Carbon::now();
            $email->save();
            logDebug("[Command SendEmails: send] finish");
            return true;
        } catch (Exception | \Throwable $e) {
            logError("[Command SendEmails: send] Error: '{$e->getMessage()}' | File: '{$e->getFile()}' | Line: {$e->getLine()}");
            $email->errors = $email->errors + 1;
            $email->save();
            return false;
        }
    }
}
