<?php

namespace App\Mail\Memberships;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $request, $error;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $request, array $error)
    {
        $this->request = $request;
        $this->error = $error;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('emails.memberships.adminreportemail')
            ->from(backpack_user()->email, 'IKS - Stručna služba za poslove matičnih sekcija')
            ->with(['data' => $this->request, 'error' => $this->error]);
    }
}

