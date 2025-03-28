<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class IncidentReportMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $reportData;
    protected $user;
    protected $classroom;

    public function __construct($reportData, $user, $classroom)
    {
        $this->reportData = $reportData;
        $this->user = $user;
        $this->classroom = $classroom;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@quanlythietbi.com', 'Hệ thống Quản lý Thiết bị'),
            subject: "[Báo cáo sự cố] - Phòng {$this->classroom->name}"
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.incidents.report',
            with: [
                'description' => $this->reportData->description,
                'report_time' => $this->reportData->report_time,
                'user' => $this->user,
                'classroom' => $this->classroom
            ]
        );
    }
}
