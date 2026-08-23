<?php

namespace App\Mail;

use App\Models\WaitlistSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\SubscriptionPlansReference;

class WaitlistAdminNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public WaitlistSubmission $submission
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎯 New Waitlist Submission - ' . $this->submission->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if (!empty($this->submission->plan_id)) {
            $SubscriptionPlansReference = SubscriptionPlansReference::where('id', $this->submission->plan_id)->first();
            $plan_name = $SubscriptionPlansReference->display_name;    
        }else{
            $plan_name = '';
        }
        return new Content(
            view: 'emails.waitlist-admin-notification',
            with: [
                'submission' => $this->submission,
                'plan_name' => $plan_name
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
