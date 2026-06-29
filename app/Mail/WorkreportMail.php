<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkreportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $filename;
    public $workreport;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($filename, $workreport)
    {
        $this->filename = $filename;
        $this->workreport = $workreport;
        $this->attach(storage_path('app/public/output/'.$filename));

        foreach($workreport->documents->where('type', 'lieferschein') as $document) {
            $this->attach(storage_path('app/public/upload/'.$document->name));
        }

        foreach($workreport->documents->where('type', 'vertrag') as $document) {
            $this->attach(storage_path('app/public/upload/'.$document->name));
        }

        foreach($workreport->documents->where('type', 'zusatzdokument') as $document) {
            $this->attach(storage_path('app/public/upload/'.$document->name));
        }
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Workreport Mail',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'workreportmail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
