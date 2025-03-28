<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $equipments = Equipment::all();
        return response()->json($equipments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $equipment = new Equipment();
        $equipment->equipment_name = $request->name;
        $equipment->description = $request->description;
        $equipment->quantity = $request->quantity;
        $equipment->save();
        return response()->json($equipment);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $equipment = Equipment::find($id);
        return response()->json($equipment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $equipment = Equipment::find($id);
        $equipment->equipment_name = $request->name;
        $equipment->description = $request->description;
        $equipment->quantity = $request->quantity;
        $equipment->save();
        return response()->json($equipment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $equipment = Equipment::find($id);
        $equipment->delete();
        return response()->json($equipment);
    }
}
