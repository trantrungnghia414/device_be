<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Models\IncidentReport;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IncidentReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $incidentReports = IncidentReport::with([
                'user:id,name,email',
                'classroom:id,name',
                'assignment' => function($query) {
                    $query->select('id', 'incident_reports_id', 'assignment_time', 'description');
                },
                'assignment.assignmentUsers:id,assignment_id,user_id,completion_time,image,notes',
                'assignment.assignmentUsers.user:id,name'
            ])
            ->select('id', 'user_id', 'classroom_id', 'description', 'status', 'report_time')
            ->orderBy('id', 'desc')  // Add sorting by id in descending order
            ->get()
            ->map(function($report) {
                if ($report->assignment) {
                    $report->assignment->assignmentUsers->transform(function($assignmentUser) {
                        // Add image URL if image exists
                        if ($assignmentUser->image) {
                            $assignmentUser->image_url = Storage::url($assignmentUser->image);
                        }
                        return $assignmentUser;
                    });
                }
                return $report;
            });

            return response()->json($incidentReports);

        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách báo cáo:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'message' => 'Lỗi khi lấy danh sách báo cáo sự cố',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index123()
    {
        try {
            $incidentReports = IncidentReport::with([
                'user:id,name,email',
                'classroom:id,name',
                'assignment' => function($query) {
                    $query->select('id', 'incident_reports_id','assignment_time', 'description')
                        ->with(['assignmentUsers' => function($q) {
                            $q->select('id', 'assignment_id', 'user_id','completion_time','notes')
                                ->with('user:id,name');
                        }]);
                }
            ])
            ->select('id', 'user_id', 'classroom_id', 'description', 'status', 'report_time')
            ->get();

            return response()->json($incidentReports);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy danh sách báo cáo sự cố',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        try { 
            // Find user by email
            $user = User::where('email', $request->email)->first();
            // Create new incident report
            $incidentReport = new IncidentReport();
            $incidentReport->user_id = $user->id;
            $incidentReport->classroom_id = $request->classroom_id;
            $incidentReport->report_time = now(); // Add timestamp
            $incidentReport->description = $request->description;
            $incidentReport->status = "reported";
            $incidentReport->save();
    
            // Return success response
            return response()->json([
                'message' => 'Incident report created successfully',
            ], 201);  
       
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating incident report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $incidentReport = IncidentReport::with(['user', 'equipmentClassroom.equipment', 'equipmentClassroom.classroom'])->find($id);
        return response()->json($incidentReport);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $incidentReport = IncidentReport::find($id);
        $incidentReport->user_id = $request->user_id;
        $incidentReport->equipment_id = $request->equipment_id;
        $incidentReport->classroom_id = $request->classroom_id;
        $incidentReport->description = $request->description;
        $incidentReport->save();
        return response()->json($incidentReport);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $incidentReport = IncidentReport::find($id);
        $incidentReport->delete();
        return response()->json($incidentReport);
    }

    public function getBuildingRoom()
    {
        $building_room = Classroom::with('building')
            ->get()
            ->groupBy('building.building_name')
            ->map(function ($classrooms) {
                return [
                    'building_name' => $classrooms->first()->building->building_name,
                    'building_id' => $classrooms->first()->building->id,
                    'classrooms' => $classrooms->map(function ($classroom) {
                        return [
                            'id' => $classroom->id,
                            'name' => $classroom->name
                        ];
                    })
                ];
            })->values();
    
        return response()->json($building_room);
    }
    
    public function incidentNotification()
    {
        try {
            $incidentReports = IncidentReport::whereIn('status', ['reported', 'viewed'])
                ->with(['user:id,name,avatar'])
                ->select('id', 'user_id', 'description', 'report_time', 'status')
                ->orderBy('id', 'desc')  // Add sorting here
                ->get()
                ->map(function ($incident) {
                    return [
                        'avatar_url' => $incident->user->avatar ? 
                            asset('storage/' . $incident->user->avatar) : null,
                        'id' => $incident->id,
                        'user_name' => $incident->user->name,
                        'description' => $incident->description,
                        'report_time' => Carbon::parse($incident->report_time)->format('H:i - d/m/Y'),
                        'status' => $incident->status
                    ];
                });
    
            return response()->json($incidentReports);
    
        } catch (\Exception $e) {
            Log::error('Lỗi lấy thông báo sự cố:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông báo sự cố'
            ], 500);
        }
    }
}
