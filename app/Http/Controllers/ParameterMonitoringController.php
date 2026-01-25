<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParameterMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $groups = [
            'rms' => __('messages.parameters.groups.rms'),
            'frequency' => __('messages.parameters.groups.frequency'),
            'peak' => __('messages.parameters.groups.peak'),
            'temperature' => __('messages.parameters.groups.temperature'),
        ];

        $parameters = [
            [
                'group' => 'rms',
                'key' => 'rms_normal_range',
                'label' => __('messages.parameters.items.rms_normal.label'),
                'description' => __('messages.parameters.items.rms_normal.desc'),
                'value' => __('messages.parameters.items.rms_normal.value'),
                'detail' => __('messages.parameters.items.rms_normal.detail'),
                'status' => 'NORMAL',
            ],
            [
                'group' => 'rms',
                'key' => 'rms_warning_range',
                'label' => __('messages.parameters.items.rms_warning.label'),
                'description' => __('messages.parameters.items.rms_warning.desc'),
                'value' => __('messages.parameters.items.rms_warning.value'),
                'detail' => __('messages.parameters.items.rms_warning.detail'),
                'status' => 'WARNING',
            ],
            [
                'group' => 'rms',
                'key' => 'rms_critical_range',
                'label' => __('messages.parameters.items.rms_critical.label'),
                'description' => __('messages.parameters.items.rms_critical.desc'),
                'value' => __('messages.parameters.items.rms_critical.value'),
                'detail' => __('messages.parameters.items.rms_critical.detail'),
                'status' => 'CRITICAL',
            ],
            [
                'group' => 'rms',
                'key' => 'rms_zone_a',
                'label' => __('messages.parameters.items.rms_zone_a.label'),
                'description' => __('messages.parameters.items.rms_zone_a.desc'),
                'value' => __('messages.parameters.items.rms_zone_a.value'),
                'detail' => __('messages.parameters.items.rms_zone_a.detail'),
                'status' => 'NORMAL',
            ],
            [
                'group' => 'rms',
                'key' => 'rms_zone_b',
                'label' => __('messages.parameters.items.rms_zone_b.label'),
                'description' => __('messages.parameters.items.rms_zone_b.desc'),
                'value' => __('messages.parameters.items.rms_zone_b.value'),
                'detail' => __('messages.parameters.items.rms_zone_b.detail'),
                'status' => 'WARNING',
            ],
            [
                'group' => 'rms',
                'key' => 'rms_zone_c',
                'label' => __('messages.parameters.items.rms_zone_c.label'),
                'description' => __('messages.parameters.items.rms_zone_c.desc'),
                'value' => __('messages.parameters.items.rms_zone_c.value'),
                'detail' => __('messages.parameters.items.rms_zone_c.detail'),
                'status' => 'WARNING',
            ],
            [
                'group' => 'rms',
                'key' => 'rms_zone_d',
                'label' => __('messages.parameters.items.rms_zone_d.label'),
                'description' => __('messages.parameters.items.rms_zone_d.desc'),
                'value' => __('messages.parameters.items.rms_zone_d.value'),
                'detail' => __('messages.parameters.items.rms_zone_d.detail'),
                'status' => 'CRITICAL',
            ],
            [
                'group' => 'rms',
                'key' => 'rms_trend_up',
                'label' => __('messages.parameters.items.rms_trend_up.label'),
                'description' => __('messages.parameters.items.rms_trend_up.desc'),
                'value' => __('messages.parameters.items.rms_trend_up.value'),
                'detail' => __('messages.parameters.items.rms_trend_up.detail'),
                'status' => 'WARNING',
            ],
            [
                'group' => 'frequency',
                'key' => 'freq_1x_rpm',
                'label' => __('messages.parameters.items.freq_1x.label'),
                'description' => __('messages.parameters.items.freq_1x.desc'),
                'value' => __('messages.parameters.items.freq_1x.value'),
                'detail' => __('messages.parameters.items.freq_1x.detail'),
                'status' => 'INFO',
            ],
            [
                'group' => 'frequency',
                'key' => 'freq_2x_rpm',
                'label' => __('messages.parameters.items.freq_2x.label'),
                'description' => __('messages.parameters.items.freq_2x.desc'),
                'value' => __('messages.parameters.items.freq_2x.value'),
                'detail' => __('messages.parameters.items.freq_2x.detail'),
                'status' => 'INFO',
            ],
            [
                'group' => 'frequency',
                'key' => 'freq_harmonics',
                'label' => __('messages.parameters.items.freq_harmonics.label'),
                'description' => __('messages.parameters.items.freq_harmonics.desc'),
                'value' => __('messages.parameters.items.freq_harmonics.value'),
                'detail' => __('messages.parameters.items.freq_harmonics.detail'),
                'status' => 'WARNING',
            ],
            [
                'group' => 'peak',
                'key' => 'peak_spike',
                'label' => __('messages.parameters.items.peak_spike.label'),
                'description' => __('messages.parameters.items.peak_spike.desc'),
                'value' => __('messages.parameters.items.peak_spike.value'),
                'detail' => __('messages.parameters.items.peak_spike.detail'),
                'status' => 'WARNING',
            ],
            [
                'group' => 'peak',
                'key' => 'peak_consistent',
                'label' => __('messages.parameters.items.peak_consistent.label'),
                'description' => __('messages.parameters.items.peak_consistent.desc'),
                'value' => __('messages.parameters.items.peak_consistent.value'),
                'detail' => __('messages.parameters.items.peak_consistent.detail'),
                'status' => 'CRITICAL',
            ],
            [
                'group' => 'peak',
                'key' => 'crest_factor',
                'label' => __('messages.parameters.items.crest_factor.label'),
                'description' => __('messages.parameters.items.crest_factor.desc'),
                'value' => __('messages.parameters.items.crest_factor.value'),
                'detail' => __('messages.parameters.items.crest_factor.detail'),
                'status' => 'WARNING',
            ],
            [
                'group' => 'temperature',
                'key' => 'temp_normal',
                'label' => __('messages.parameters.items.temp_normal.label'),
                'description' => __('messages.parameters.items.temp_normal.desc'),
                'value' => __('messages.parameters.items.temp_normal.value'),
                'detail' => __('messages.parameters.items.temp_normal.detail'),
                'status' => 'NORMAL',
            ],
            [
                'group' => 'temperature',
                'key' => 'temp_overheat',
                'label' => __('messages.parameters.items.temp_overheat.label'),
                'description' => __('messages.parameters.items.temp_overheat.desc'),
                'value' => __('messages.parameters.items.temp_overheat.value'),
                'detail' => __('messages.parameters.items.temp_overheat.detail'),
                'status' => 'CRITICAL',
            ],
        ];

        $groupCounts = [];
        foreach ($parameters as $item) {
            $groupCounts[$item['group']] = ($groupCounts[$item['group']] ?? 0) + 1;
        }

        return view('pages.parameter-monitoring', compact('groups', 'parameters', 'groupCounts'));
    }
}
