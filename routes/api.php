<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Page\PageController;
use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\RepairTeamController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\EquipmentClassroomController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\IncidentReportController;
use App\Http\Controllers\Admin\StatisticalController;
use App\Http\Controllers\Page\AuthController;
use App\Http\Controllers\Page\GoogleController;
use App\Models\EquipmentClassroom;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//=====================================USER==================================================
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load(['role' => function($query) {
        $query->select('id', 'name');
    }]);
});

Route::get('/page',[PageController::class,'index']);
Route::post('/user/login',[AuthController::class,'login']);

// ===========================================ADMIN==========================================

Route::resource('buildings', BuildingController::class);
Route::resource('room_types', RoomTypeController::class);
Route::resource('roles', RoleController::class);
Route::resource('repair_teams', RepairTeamController::class);
Route::resource('equipments', EquipmentController::class);
Route::resource('assignments', AssignmentController::class);
Route::resource('classrooms', ClassroomController::class);
Route::resource('equipment_classrooms', EquipmentClassroomController::class);
Route::resource('users', UserController::class);
Route::resource('incident_reports', IncidentReportController::class);
Route::get('equipment_room/{id}',[EquipmentClassroomController::class,'equipmentRoomId']);
Route::put('users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
Route::get('building_room',[IncidentReportController::class,'getBuildingRoom']);
Route::get('user-profile',[UserController::class,'userProfile']);
Route::put('user-update-profile',[UserController::class,'updateProfile']);
Route::get('incident',[PageController::class,'incidentReport']);
Route::get('classrooms-incident/{id}',[PageController::class,'classroomsIncident']);
Route::post('incident-form',[PageController::class,'incidentForm']);
Route::get('device-log',[PageController::class,'deviceLog']);
Route::get('assignment-user/{id}',[AssignmentController::class,'getAssignmentUser']);
Route::get('assignment-list',[PageController::class,'assignmentList']);
Route::get('assignment-list-user',[PageController::class,'assignmentListUser']);
Route::post('post-assignment-user/{id}',[PageController::class,'postAssignmentUser']);

Route::get('incident-notification',[IncidentReportController::class,'incidentNotification']);

Route::get('statistic-building',[StatisticalController::class,'statisticBuilding']);
Route::get('statistic-room',[StatisticalController::class,'statisticRoom']);
Route::get('statistic-teacher-reports',[StatisticalController::class,'statisticIncidentReport']);

