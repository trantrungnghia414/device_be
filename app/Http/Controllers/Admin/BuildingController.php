<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buildings = Building::all();
        return response()->json($buildings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $building = new Building();
       $building->building_name = $request->name;
       $building->save();
       return response()->json($building);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $building = Building::find($id);
        return response()->json($building);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $building = Building::find($id);
       $building->building_name = $request->name;
       $building->save();
        return response()->json($building);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $building = Building::find($id);
        $building->delete();
        return response()->json($building);
    }
}
