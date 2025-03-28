<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RepairTeam;
class RepairTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $repairTeams = RepairTeam::all();
        return response()->json($repairTeams);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $repairTeam = new RepairTeam();
        $repairTeam->name = $request->name;
        $repairTeam->save();
        return response()->json($repairTeam);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $repairTeam = RepairTeam::find($id);
        return response()->json($repairTeam);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $repairTeam = RepairTeam::find($id);
        $repairTeam->name = $request->name;
        $repairTeam->save();
        return response()->json($repairTeam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $repairTeam = RepairTeam::find($id);
        $repairTeam->delete();
        return response()->json($repairTeam);
    }
}
