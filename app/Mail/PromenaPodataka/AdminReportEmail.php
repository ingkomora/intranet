<?php

namespace App\Mail\PromenaPodataka;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $mail_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data)
    {
        $this->mail_data = $mail_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('emails.promenapodataka.adminreportemail')
            ->from('izmeneadresa@ingkomora.rs', 'IKS - Promena ličnih podataka')
            ->with(['data' => $this->mail_data]);
    }
}
