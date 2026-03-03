<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $machines = Machine::orderBy('name')->get();
        $sampleRate = (int) env('SAMPLE_RATE_HZ', 200);
        $batchSize = (int) env('N_SAMPLES', 256);
        $queueDriver = config('queue.default', env('QUEUE_CONNECTION', 'sync'));
        $samplingIntervalMinutes = (int) Cache::get('sampling_interval_minutes', 1);
        $unreadCount = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return view('pages.settings', compact(
            'machines',
            'sampleRate',
            'batchSize',
            'queueDriver',
            'samplingIntervalMinutes',
            'unreadCount'
        ));
    }

    public function updateSamplingInterval(Request $request)
    {
        $validated = $request->validate([
            'sampling_interval_minutes' => 'required|integer|min:1|max:60',
        ]);

        Cache::forever('sampling_interval_minutes', (int) $validated['sampling_interval_minutes']);

        return response()->json([
            'success' => true,
            'message' => 'Sampling interval updated.',
            'sampling_interval_minutes' => (int) $validated['sampling_interval_minutes'],
        ]);
    }

    public function updateMachineStatus(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'is_active' => 'required|boolean',
        ]);

        $machine = Machine::findOrFail($validated['machine_id']);
        $machine->is_active = (bool) $validated['is_active'];
        $machine->save();

        return response()->json([
            'success' => true,
            'message' => 'Machine status updated.',
            'machine_id' => (int) $machine->id,
            'is_active' => (bool) $machine->is_active,
        ]);
    }
}
