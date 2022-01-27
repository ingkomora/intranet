<?php

namespace App\Mail\Memberships;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('emails.memberships.confirmationemail')
            ->from(backpack_user()->email, 'IKS - Stručna služba za poslove matičnih sekcija')
            ->subject('Potvrda o obradi zahteva za prijem u članstvo')
            ->with(['data' => $this->request]);
    }
}
