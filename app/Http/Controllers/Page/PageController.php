<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\IncidentReport;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Events\IncidentReported;
use App\Mail\IncidentReportMail;
use App\Jobs\SendIncidentMailJob;
use App\Models\Assignment;
use App\Models\AssignmentUser;
use App\Mail\IncidentCompletedMail;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        return "Hello World";
    }

  
    // public function incidentReport()
    // {
    //    $classrooms = Classroom::with(['building','roomType'])->get();
    //    return response()->json($classrooms);
    // }
    public function incidentReport()
{
    $buildings = Classroom::with(['building', 'roomType'])
        ->get()
        ->groupBy('building_id')
        ->map(function ($classrooms) {
            $building = $classrooms->first()->building;
            return [
                'building_id' => $building->id,
                'building_name' => $building->building_name,
                'room_types' => $classrooms
                    ->groupBy('room_type_id')
                    ->map(function ($roomTypeClassrooms) {
                        $roomType = $roomTypeClassrooms->first()->roomType;
                        return [
                            'room_type_id' => $roomType->id,
                            'room_type_name' => $roomType->room_type_name,
                            'classrooms' => $roomTypeClassrooms->map(function ($classroom) {
                                return [
                                    'id' => $classroom->id,
                                    'name' => $classroom->name
                                ];
                            })->values()->toArray()
                        ];
                    })->values()->toArray()
            ];
        })->values();

    return response()->json($buildings);
}

public function classroomsIncident(Request $request,$id){
    $classrooms = Classroom::with(['incidentReports'])->where('id',$id)->get();
  //$classrooms = IncidentReport::with(['classroom'])->where('classroom_id',$id)->get();
    return response()->json($classrooms);
}

public function incidentForm(Request $request)
{
    try {
        // Validate request
       

        // Find user
        $user = User::select('id', 'name', 'email')
            ->where('email', $request->email)
            ->first();
            
        if (!$user) {
            Log::warning('User not found:', ['email' => $request->email]);
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        // Find classroom
        $classroom = Classroom::select('id', 'name')
            ->find($request->classroom_id);
            
        if (!$classroom) {
            Log::warning('Classroom not found:', ['id' => $request->classroom_id]);
            return response()->json(['message' => 'Không tìm thấy phòng học'], 404);
        }

        // Save incident in transaction
        $reportTime = now();
        $incident = DB::transaction(function() use ($user, $request, $reportTime) {
            return IncidentReport::create([
                'user_id' => $user->id,
                'classroom_id' => $request->classroom_id,
                'description' => $request->description,
                'status' => 'reported',
                'report_time' => $reportTime
            ]);
        });

        // Queue email sending
        Queue::push(function() use ($incident, $user, $classroom) {
            try {
                $adminEmails = User::where('role_id', 1)
                    ->pluck('email')
                    ->toArray();

                if (!empty($adminEmails)) {
                    Mail::to($adminEmails)
                        ->queue(new IncidentReportMail(
                            $incident,
                            $user, 
                            $classroom
                        ));

                    Log::info('Email queued successfully', [
                        'incident_id' => $incident->id,
                        'admins' => $adminEmails
                    ]);
                } else {
                    Log::warning('No admin emails found');
                }
            } catch (\Exception $e) {
                Log::error('Email queuing failed:', [
                    'error' => $e->getMessage(),
                    'incident_id' => $incident->id
                ]);
            }
        });

        // Return response immediately
        return response()->json([
            'success' => true,
            'message' => 'Sự cố đã được báo cáo thành công',
            'data' => [
                'incident_id' => $incident->id
            ]
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Incident reporting failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi báo cáo sự cố'
        ], 500);
    }
}


// public function deviceLog()
// {
//     $incidents = IncidentReport::with(['classroom:id,name', 'assignment.assignmentUsers'])
//     ->get()    
//     ->map(function ($incident) {
//             return [
//                 'id' => $incident->id,
//                 'description' => $incident->description,
//                 'status' => $incident->status,
//                 'report_time' => $incident->report_time,
//                 'classroom_name' => $incident->classroom->name,
//                 'assignmentUsers'=> $incident->assignment
//             ];
//         });

//     return response()->json($incidents);
// }

public function deviceLog()
{
    $incidents = IncidentReport::with(['classroom:id,name', 'assignment.assignmentUsers'])
    ->get()    
    ->map(function ($incident) {
            // Get assignment users through assignment relationship
            $assignmentUsers = $incident->assignment ? 
                $incident->assignment->assignmentUsers->map(function($assignmentUser) {
                    return [
                        'assignment_id' => $assignmentUser->assignment_id,
                        'completion_time' => $assignmentUser->completion_time
                    ];
                }) : [];

            return [
                'id' => $incident->id,
                'description' => $incident->description,
                'status' => $incident->status,
                'report_time' => $incident->report_time,
                'classroom_name' => $incident->classroom->name,
                'assignmentUsers' => $assignmentUsers
            ];
        });

    return response()->json($incidents);
}

public function assignmentList()
{
    try {
        $assignments = AssignmentUser::with([
            'assignment' => function($query) {
                $query->with(['incidentReport' => function($q) {
                    $q->with('classroom');
                }]);
            },
            'user'
        ])
        ->whereHas('assignment', function($query) {
            $query->whereNotNull('incident_reports_id');
        })
        ->get()
        ->map(function ($assignmentUser) {
            $assignment = $assignmentUser->assignment;
            $incident = $assignment->incidentReport;
            $classroom = $incident->classroom ?? null;
            
            return [
                'id' => $assignmentUser->id,
                'assignment_id' => $assignment->id,
                'user_name' => $assignmentUser->user->name ?? 'N/A',
                'classroom_name' => $classroom ? $classroom->name : 'N/A',
                'incident_id' => $incident->id ?? null,
                'description' => $assignment->description,
               // 'incident_description' => $incident->description ?? 'Không có mô tả',
                'incident_status' => $incident->status ?? 'Không xác định',
                'assignment_time' => $assignment->assignment_time,
                'completion_time' => $assignmentUser->completion_time,
                'status' => $assignmentUser->status ?? 'Chưa xử lý',
            ];
        });

        return response()->json($assignments);

    } catch (\Exception $e) {
        Log::error('Lỗi lấy danh sách công việc:', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lấy danh sách công việc',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

public function assignmentListUser(Request $request)
{
    try {
        // Find user by email
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng với email này'
            ], 404);
        }

        $assignments = AssignmentUser::with([
            'assignment' => function($query) {
                $query->with(['incidentReport' => function($q) {
                    $q->with('classroom');
                }]);
            },
            'user'
        ])
        ->where('user_id', $user->id)
        ->whereHas('assignment', function($query) {
            $query->whereNotNull('incident_reports_id');
        })
        ->get()
        ->map(function ($assignmentUser) {
            $assignment = $assignmentUser->assignment;
            $incident = $assignment->incidentReport;
            $classroom = $incident->classroom ?? null;
            
            return [
                'id' => $assignmentUser->id,
                'assignment_id' => $assignment->id,
                'user_name' => $assignmentUser->user->name ?? 'N/A',
                'classroom_name' => $classroom ? $classroom->name : 'N/A',
                'incident_id' => $incident->id ?? null,
                'incident_description' => $incident->description ?? 'Không có mô tả',
                'incident_status' => $incident->status ?? 'Không xác định',
                'assignment_time' => $assignment->assignment_time,
                'completion_time' => $assignmentUser->completion_time,
                'status' => $assignmentUser->status ?? 'Chưa xử lý',
            ];
        });

        return response()->json($assignments);

    } catch (\Exception $e) {
        Log::error('Lỗi lấy danh sách công việc của người dùng:', [
            'email' => $request->email,
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lấy danh sách công việc',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}


public function postAssignmentUser(Request $request, $id)
{
    try {
        DB::beginTransaction();

        // Load assignment user with relationships
        $assignmentUser = AssignmentUser::with([
            'assignment.incidentReport.user', 
            'assignment.incidentReport.classroom',
            'user'
        ])->find($id);

        if (!$assignmentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phân công'
            ], 404);
        }

        // Handle image upload
        if ($request->hasFile('proof_image')) {
            try {
                $image = $request->file('proof_image');
                
                // Validate image
                if (!$image->isValid() || !in_array($image->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File không hợp lệ. Chỉ chấp nhận ảnh JPG, JPEG hoặc PNG'
                    ], 422);
                }

                // Create unique filename
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $image->getClientOriginalName());
                
                // Store file in storage
                $path = $image->storeAs('images/proof', $filename, 'public');
                
                if (!$path) {
                    throw new \Exception('Failed to store image');
                }

                // Save image path to database
                $assignmentUser->image = $path;

            } catch (\Exception $e) {
                Log::error('Image upload failed:', [
                    'error' => $e->getMessage(),
                    'file' => $image->getClientOriginalName()
                ]);
                throw $e;
            }
        }

        // Update completion info
        $assignmentUser->completion_time = now();
        $assignmentUser->notes = $request->notes;
        $assignmentUser->save();

        // Check completion status
        $allAssignmentUsers = $assignmentUser->assignment->assignmentUsers;
        $totalUsers = $allAssignmentUsers->count();
        $completedUsers = $allAssignmentUsers->filter(function($au) {
            return !empty($au->completion_time);
        })->count();

        // If all users completed their assignments
        if ($totalUsers === $completedUsers) {
            $incidentReport = $assignmentUser->assignment->incidentReport;
            
            // Update incident status
            $incidentReport->status = 'completed';
            $incidentReport->save();

            // Send email to reporter
            try {
                $reporter = $incidentReport->user;
                if ($reporter && $reporter->email) {
                    $completionDetails = $allAssignmentUsers->map(function($au) {
                        return [
                            'user_name' => $au->user ? $au->user->name : 'Unknown',
                            'completion_time' => $au->completion_time,
                            'notes' => $au->notes,
                            'image' => $au->image ? Storage::url($au->image) : null
                        ];
                    });

                    Mail::to($reporter->email)
                        ->queue(new IncidentCompletedMail(
                            $incidentReport,
                            $completionDetails,
                            $incidentReport->classroom
                        ));

                    Log::info('Completion notification email queued', [
                        'incident_id' => $incidentReport->id,
                        'reporter_email' => $reporter->email
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Email sending failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'incident_id' => $incidentReport->id
                ]);
                // Continue execution even if email fails
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công',
            'data' => [
                'is_completed' => ($totalUsers === $completedUsers),
                'total_users' => $totalUsers,
                'completed_users' => $completedUsers,
                'image_url' => $assignmentUser->image ? Storage::url($assignmentUser->image) : null,
              //  'reporter_email' => $reporter->email
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Assignment update failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'assignment_id' => $id,
            'request_data' => $request->except(['proof_image'])
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage()
        ], 500);
    }
}

}
