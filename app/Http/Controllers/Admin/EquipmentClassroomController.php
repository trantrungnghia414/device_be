<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipmentClassroom;

class EquipmentClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $equipmentClassrooms = EquipmentClassroom::with(['equipment', 'classroom'])->get();
        //     return [
        //         ' equipmentClassrooms' =>  $equipmentClassrooms
        //     ];
        
        // return response()->json($equipmentClassrooms);
        $equipmentRooms = EquipmentClassroom::with([
            'equipment:id,equipment_name',
            'classroom:id,name'
        ])
        ->select('id', 'equipment_id', 'classroom_id','quantity')
        ->get()
        ->map(function ($equipmentRoom) {
            return [
                'id' => $equipmentRoom->id,             
                'equipment_name' => $equipmentRoom->equipment->equipment_name,
                'classroom_name' => $equipmentRoom->classroom->name,
                'quantity' => $equipmentRoom->quantity
            ];
        });

        return response()->json($equipmentRooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $equipmentClassroom = new EquipmentClassroom();
        $equipmentClassroom->equipment_id = $request->device_id;
        $equipmentClassroom->classroom_id = $request->classroom_id;
        $equipmentClassroom->quantity = $request->quantity;
        $equipmentClassroom->save();
        return response()->json([
            'message' => 'Equipment Classroom created successfully',
            'equipmentClassroom' => $equipmentClassroom
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $equipmentClassroom = EquipmentClassroom::with(['equipment', 'classroom'])->where('id', $id)->first();
        return response()->json($equipmentClassroom);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $equipmentClassroom = EquipmentClassroom::find($id);
        $equipmentClassroom->equipment_id = $request->device_id;
        $equipmentClassroom->classroom_id = $request->classroom_id;
        $equipmentClassroom->quantity = $request->quantity;
        $equipmentClassroom->save();
        return response()->json($equipmentClassroom);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $equipmentClassroom = EquipmentClassroom::find($id);
        $equipmentClassroom->delete();
        return response()->json($equipmentClassroom);
    }

    public function equipmentRoomId(Request $request, $id)
    {
        $equipmentRooms = EquipmentClassroom::with([
            'equipment:id,equipment_name',
            'classroom:id,name'
        ])
        ->select('id', 'equipment_id', 'classroom_id','quantity')
        ->where('classroom_id', $id)
        ->get()
        ->map(function ($equipmentRoom) {
            return [
                'id' => $equipmentRoom->id,             
                'equipment_name' => $equipmentRoom->equipment->equipment_name,
                'classroom_name' => $equipmentRoom->classroom->name,
                'quantity' => $equipmentRoom->quantity
            ];
        });

        return response()->json($equipmentRooms);
    }
}
