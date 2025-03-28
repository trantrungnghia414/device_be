<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class IncidentCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $incident;
    public $completionDetails;
    public $classroom;

    public function __construct($incident, $completionDetails, $classroom)
    {
        $this->incident = $incident;
        $this->completionDetails = $completionDetails;
        $this->classroom = $classroom;
    }

    public function build()
    {
        try {
            $recipientEmail = trim($this->incident->user->email);
            
            if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Email không hợp lệ: {$recipientEmail}");
            }

            Log::info('Bắt đầu gửi email thông báo hoàn thành', [
                'to' => $recipientEmail,
                'incident_id' => $this->incident->id,
                'classroom' => $this->classroom->name,
                'completion_count' => count($this->completionDetails)
            ]);

            return $this->markdown('emails.incidents.completed')
                        ->to($recipientEmail)
                        ->subject('Thông báo: Sự cố đã được xử lý hoàn tất')
                        ->with([
                            'incident' => $this->incident,
                            'completionDetails' => $this->completionDetails,
                            'classroom' => $this->classroom
                        ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi email:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'incident_id' => $this->incident->id ?? null,
                'recipient' => $recipientEmail ?? null
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $e)
    {
        Log::error('Email gửi thất bại:', [
            'error' => $e->getMessage(),
            'incident_id' => $this->incident->id ?? null,
            'recipient' => $this->incident->user->email ?? null
        ]);
    }
}