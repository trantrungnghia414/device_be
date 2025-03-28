<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\IncidentReportMail;
use App\Models\User;

class SendIncidentMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Số lần thử lại nếu fail
    public $timeout = 30; // Thời gian timeout (giây)

    protected $description;
    protected $reportTime;
    protected $user;
    protected $classroom;
    protected $adminEmails;

    public function __construct($description, $reportTime, $user, $classroom, $adminEmails)
    {
        $this->description = $description;
        $this->reportTime = $reportTime;
        $this->user = $user;
        $this->classroom = $classroom;
        $this->adminEmails = $adminEmails;
    }

    public function handle()
    {
        try {
            Log::info('Bắt đầu xử lý email...', [
                'incident' => [
                    'description' => $this->description,
                    'report_time' => $this->reportTime,
                    'reporter' => $this->user->email,
                    'classroom' => $this->classroom->name
                ]
            ]);

            if (empty($this->adminEmails)) {
                Log::warning('Không có email admin để gửi');
                return;
            }

            Mail::to($this->adminEmails)->send(new IncidentReportMail(
                (object)[
                    'description' => $this->description,
                    'report_time' => $this->reportTime
                ],
                $this->user,
                $this->classroom
            ));

            Log::info('Gửi email thành công', [
                'recipients' => $this->adminEmails,
                'sender' => $this->user->email,
                'time' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi gửi email', [
                'error' => $e->getMessage(),
                'sender' => $this->user->email ?? 'unknown'
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Job thất bại', [
            'error' => $exception->getMessage(),
            'sender' => $this->user->email ?? 'unknown'
        ]);
    }
}