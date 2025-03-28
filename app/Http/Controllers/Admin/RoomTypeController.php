<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomType;
class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypes = RoomType::all();
        return response()->json($roomTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $roomType = new RoomType();
        $roomType->room_type_name = $request->name;
        $roomType->save();
        return response()->json($roomType);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $roomType = RoomType::find($id);
        return response()->json($roomType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $roomType = RoomType::find($id);
        $roomType->room_type_name = $request->name;
        $roomType->save();
        return response()->json($roomType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $roomType = RoomType::find($id);
        $roomType->delete();
        return response()->json($roomType);
    }
}
