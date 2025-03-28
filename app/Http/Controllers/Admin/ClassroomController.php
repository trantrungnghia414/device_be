<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = Classroom::with([
            'building:id,building_name',
            'roomType:id,room_type_name'
        ])
        ->select('id', 'name', 'building_id', 'room_type_id')
        ->get()
        ->map(function ($classroom) {
            return [
                'id' => $classroom->id,
                'name' => $classroom->name,
                'building_name' => $classroom->building->building_name,
                'room_type_name' => $classroom->roomType->room_type_name
            ];
        });

        return response()->json($classrooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validatedData = $request->validate([
            'building_id' => 'required|integer',
            'room_type_id' => 'required|integer',
            'rooms' => 'required|array',
            'rooms.*' => 'string|unique:classrooms,name', // Mỗi phòng phải là chuỗi và không trùng
        ]);
    
        $savedClassrooms = [];
    
        foreach ($validatedData['rooms'] as $roomName) {
            $classroom = Classroom::create([
                'building_id' => $validatedData['building_id'],
                'room_type_id' => $validatedData['room_type_id'],
                'name' => $roomName,
            ]);
    
            $savedClassrooms[] = $classroom;
        }
    
        return response()->json([
            'message' => 'Classrooms created successfully'
        ], 201);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $classroom = Classroom::find($id);
        return response()->json($classroom);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $classroom = Classroom::find($id);
        $classroom->building_id = $request->building_id;
        $classroom->room_type_id = $request->room_type_id;
        $classroom->name = $request->name;
        $classroom->save();
        return response()->json($classroom);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $classroom = Classroom::find($id);
        $classroom->delete();
        return response()->json($classroom);
    }
}
