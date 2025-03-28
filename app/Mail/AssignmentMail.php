<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Assignment;

class AssignmentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $assignment;
    protected $user;

    public function __construct(Assignment $assignment, $user)
    {
        $this->assignment = $assignment;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@quanlythietbi.com', 'Hệ thống Quản lý Thiết bị'),
            subject: 'Thông báo phân công công việc mới'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.assignments.notification',
            with: [
                'userName' => $this->user->name,
                'assignmentTime' => $this->assignment->assignment_time,
                'description' => $this->assignment->description,
                'assignmentId' => $this->assignment->id
            ]
        );
    }
}