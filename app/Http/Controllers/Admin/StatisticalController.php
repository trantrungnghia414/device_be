<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Classroom;
use App\Models\IncidentReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class StatisticalController extends Controller
{

    
public function statisticBuilding(Request $request)
{
    try {
        $query = Building::with(['classrooms.incidentReports']);
        $startDate = now();
        $endDate = now();

        // Check if custom date range is provided
        if ($request->has(['start_date', 'end_date'])) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Get date range based on period
            $period = $request->period ?? 'week';
            
            switch ($period) {
                case 'week':
                    $startDate = $startDate->startOfWeek();
                    break;
                case 'month':
                    $startDate = $startDate->startOfMonth();
                    break;
                case 'quarter':
                    $startDate = $startDate->startOfQuarter();
                    break;
                case 'year':
                    $startDate = $startDate->startOfYear();
                    break;
            }
        }

        $buildings = $query->get()
            ->map(function($building) use ($startDate, $endDate) {
                return [
                    'name' => $building->building_name,
                    'total_incidents' => $building->classrooms->sum(function($classroom) use ($startDate, $endDate) {
                        return $classroom->incidentReports
                            ->whereBetween('report_time', [$startDate, $endDate])
                            ->count();
                    })
                ];
            })
            ->sortByDesc('total_incidents')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $request->period ?? 'custom',
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'buildings' => $buildings
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Lỗi lấy thống kê sự cố:', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'period' => $request->period ?? 'custom',
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lấy thống kê sự cố'
        ], 500);
    }
}


// public function statisticRoom(Request $request)
// {
//     try {
//         $buildingId = $request->building_id;
//         if (!$buildingId) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Vui lòng chọn tòa nhà'
//             ], 400);
//         }

//         $period = $request->period ?? 'week';
//         $startDate = now();
        
//         switch ($period) {
//             case 'week':
//                 $startDate = $startDate->startOfWeek();
//                 break;
//             case 'month':
//                 $startDate = $startDate->startOfMonth();
//                 break;
//             case 'quarter':
//                 $startDate = $startDate->startOfQuarter();
//                 break;
//             case 'year':
//                 $startDate = $startDate->startOfYear();
//                 break;
//         }

//         $rooms = Classroom::where('building_id', $buildingId)
//             ->with(['incidentReports' => function($query) use ($startDate) {
//                 $query->where('report_time', '>=', $startDate);
//             }])
//             ->get()
//             ->map(function($room) {
//                 return [
//                     'name' => $room->name,
//                     'total_incidents' => $room->incidentReports->count()
//                 ];
//             })
//             ->sortByDesc('total_incidents')
//             ->values();

//         return response()->json([
//             'success' => true,
//             'data' => [
//                 'period' => $period,
//                 'start_date' => $startDate->format('Y-m-d'),
//                 'end_date' => now()->format('Y-m-d'),
//                 'building_id' => $buildingId,
//                 'rooms' => $rooms
//             ]
//         ]);

//     } catch (\Exception $e) {
//         Log::error('Lỗi lấy thống kê sự cố phòng:', [
//             'error' => $e->getMessage(),
//             'line' => $e->getLine(),
//             'building_id' => $request->building_id ?? null,
//             'period' => $request->period ?? 'week'
//         ]);

//         return response()->json([
//             'success' => false,
//             'message' => 'Có lỗi xảy ra khi lấy thống kê sự cố phòng'
//         ], 500);
//     }
// }

public function statisticRoom(Request $request)
{
    try {
        $buildingId = $request->building_id;
        if (!$buildingId) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn tòa nhà'
            ], 400);
        }

        // Initialize dates
        $startDate = now();
        $endDate = now();

        // Handle custom date range
        if ($request->has(['start_date', 'end_date'])) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Handle period-based date range
            $period = $request->period ?? 'week';
            
            switch ($period) {
                case 'week':
                    $startDate = $startDate->startOfWeek();
                    break;
                case 'month':
                    $startDate = $startDate->startOfMonth();
                    break;
                case 'quarter':
                    $startDate = $startDate->startOfQuarter();
                    break;
                case 'year':
                    $startDate = $startDate->startOfYear();
                    break;
            }
        }

        $rooms = Classroom::where('building_id', $buildingId)
            ->with(['incidentReports' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('report_time', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($room) {
                return [
                    'name' => $room->name,
                    'total_incidents' => $room->incidentReports->count()
                ];
            })
            ->sortByDesc('total_incidents')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $request->period ?? 'custom',
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'building_id' => $buildingId,
                'rooms' => $rooms
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Lỗi lấy thống kê sự cố phòng:', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'building_id' => $request->building_id ?? null,
            'period' => $request->period ?? 'custom',
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lấy thống kê sự cố phòng'
        ], 500);
    }
}


public function statisticIncidentReport(Request $request)
{
    try {
        $query = User::query();
        $startDate = now();
        $endDate = now();

        // Handle custom date range
        if ($request->has(['start_date', 'end_date'])) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Handle period-based date range
            $period = $request->period ?? 'week';
            
            switch ($period) {
                case 'week':
                    $startDate = $startDate->startOfWeek();
                    break;
                case 'month':
                    $startDate = $startDate->startOfMonth();
                    break;
                case 'quarter':
                    $startDate = $startDate->startOfQuarter();
                    break;
                case 'year':
                    $startDate = $startDate->startOfYear();
                    break;
            }
        }

        $teacherReports = User::withCount(['incidentReports' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('report_time', [$startDate, $endDate]);
        }])
        ->having('incident_reports_count', '>', 0)
        ->get()
        ->map(function($teacher) {
            return [
                'name' => $teacher->name,
                'total_incidents' => $teacher->incident_reports_count
            ];
        })
        ->sortByDesc('total_incidents')
        ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $request->period ?? 'custom',
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'teachers' => $teacherReports
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Lỗi lấy thống kê báo cáo giáo viên:', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'period' => $request->period ?? 'custom',
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lấy thống kê báo cáo giáo viên'
        ], 500);
    }
}
}
