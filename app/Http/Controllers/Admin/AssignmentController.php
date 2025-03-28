<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\AssignmentUser;
use App\Models\IncidentReport;
use App\Models\RepairTeam;
use App\Models\Role;
use App\Models\User;
use App\Mail\AssignmentMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = Assignment::all();
        return response()->json($assignments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $assignment = new Assignment();
            $assignment->incident_reports_id = $request->incident_reports_id;
            $assignment->assignment_time = $request->assignment_date;
            $assignment->description = $request->description;
            $assignment->save();

            // Lấy tất cả users cần gửi email
            $users = User::whereIn('id', $request->user_ids)->get();

            foreach ($request->user_ids as $userId) {
                $assignmentUser = new AssignmentUser();
                $assignmentUser->assignment_id = $assignment->id;
                $assignmentUser->user_id = $userId;
                $assignmentUser->save();
            }

            // Gửi email bất đồng bộ
            foreach ($users as $user) {
                Mail::to($user->email)->queue(new AssignmentMail($assignment, $user));
            }

            $editStatus = IncidentReport::find($request->incident_reports_id);
            $editStatus->status = 'assigned';
            $editStatus->save();

            return response()->json([
                'success' => true,
                'message' => 'Phân công công việc thành công!'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi tạo phân công:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo phân công'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assignment = Assignment::find($id);
        return response()->json($assignment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $assignment = Assignment::find($id);
        $assignment->incident_reports_id = $request->incident_reports_id;
        $assignment->assignment_time = $request->assignment_time;
        $assignment->description = $request->description;
        $assignment->completion_time = $assignment->completion_time;
        $assignment->save();
        return response()->json($assignment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assignment = Assignment::find($id);
        $assignment->delete();
        return response()->json($assignment);
    }

    public function getAssignmentUser(string $id)
    {
        try {
            // Get incident report with user name
            $incident = IncidentReport::with(['user:id,name'])
                ->select('id', 'user_id', 'classroom_id', 'description', 'status', 'report_time')
                ->find($id);
            
            // Get users from repair teams with minimal fields
            // $users = User::whereNotNull('repair_team_id')
            //     ->select('id', 'name', 'repair_team_id')
            //     ->get();

            $role_name = Role::where('name','staff')->get();
            $users = User::where('role_id',$role_name[0]->id)
                ->select('id', 'name')
                ->get();

            return response()->json([
                'incident' => [
                    'id' => $incident->id,
                    //'report_user' => $incident->user->name,
                    'classroom_name' => $incident->classroom->name,
                    'classroom_id' => $incident->classroom_id,
                    'description' => $incident->description,
                    'status' => $incident->status,
                    'report_time' => $incident->report_time
                ],
                'repair_team_users' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy danh sách nhân viên sửa chữa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
