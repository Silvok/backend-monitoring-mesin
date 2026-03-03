<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineControlController extends Controller
{
    public function status($machineId)
    {
        $machine = Machine::find($machineId);
        if (!$machine) {
            return response()->json([
                'status' => 'error',
                'message' => 'Machine not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'machine_id' => (int) $machine->id,
            'is_active' => (bool) $machine->is_active,
        ]);
    }
}
