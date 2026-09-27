<?php

namespace App\Mail;

use App\Models\HospitalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HospitalRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public HospitalRequest $hospitalRequest;

    public function __construct(HospitalRequest $hospitalRequest)
    {
        $this->hospitalRequest = $hospitalRequest;
        // dd($this->hospitalRequest->hospital);
    }

    public function build()
    {
        return $this
            ->subject('New Hospital Request Received')
            ->view('emails.hospital-request');
    }
}