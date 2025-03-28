<?php

namespace App\Listeners;

use App\Events\IncidentReported;
use App\Mail\IncidentReportMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendIncidentNotification
{
    public function handle(IncidentReported $event)
    {
        try {
            Log::info('Bắt đầu xử lý gửi mail');
            
            $adminEmails = User::where('role_id', 1)
                              ->pluck('email')
                              ->toArray();

            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->send(new IncidentReportMail(
                    $event->incident,
                    $event->user,
                    $event->classroom
                ));
                Log::info('Đã gửi mail thành công');
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi mail: ' . $e->getMessage());
        }
    }
}