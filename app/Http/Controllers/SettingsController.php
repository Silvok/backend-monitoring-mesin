<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Notification;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $machines = Machine::orderBy('name')->get();
        $sampleRate = (int) env('SAMPLE_RATE_HZ', 200);
        $batchSize = (int) env('N_SAMPLES', 256);
        $queueDriver = config('queue.default', env('QUEUE_CONNECTION', 'sync'));
        $unreadCount = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return view('pages.settings', compact(
            'machines',
            'sampleRate',
            'batchSize',
            'queueDriver',
            'unreadCount'
        ));
    }
}
